<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Mail\AdminNewOrderMail;
use App\Mail\OrderConfirmationMail;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function checkout()
    {
        $cartItems = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty!');
        }

        $subtotal = $cartItems->sum(fn($i) => $i->product->effective_price * $i->quantity);
        $coupon   = null;
        $discount = 0;

        if (session('coupon_code')) {
            $coupon = Coupon::where('code', session('coupon_code'))
                ->where('is_active', true)
                ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->first();

            if ($coupon) {
                $discount = $coupon->type === 'percent'
                    ? ($subtotal * $coupon->value / 100)
                    : $coupon->value;
                $discount = min($discount, $subtotal);
            } else {
                session()->forget('coupon_code');
            }
        }

        $taxRate   = (float) Setting::get('tax_rate', 0);
        $afterDisc = max(0, $subtotal - $discount);
        $taxAmount = round($afterDisc * ($taxRate / 100), 2);
        $total     = $afterDisc + $taxAmount;

        return view('orders.checkout', compact(
            'cartItems', 'subtotal', 'coupon', 'discount', 'taxRate', 'taxAmount', 'total'
        ));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'payment_method'   => 'required|in:COD,Card,BankTransfer,KHQR,ABA',
            'phone'            => 'required|string|max:20',
        ]);

        $cartItems = CartItem::with('product')->where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty!');
        }

        DB::beginTransaction();
        try {
            $subtotal = $cartItems->sum(fn($i) => $i->product->effective_price * $i->quantity);
            $discount = 0;
            $coupon   = null;

            if (session('coupon_code')) {
                $coupon = Coupon::where('code', session('coupon_code'))
                    ->where('is_active', true)
                    ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                    ->first();
                if ($coupon) {
                    $discount = $coupon->type === 'percent'
                        ? ($subtotal * $coupon->value / 100)
                        : $coupon->value;
                    $discount = min($discount, $subtotal);
                }
            }

            $taxRate   = (float) Setting::get('tax_rate', 0);
            $afterDisc = max(0, $subtotal - $discount);
            $taxAmount = round($afterDisc * ($taxRate / 100), 2);
            $total     = $afterDisc + $taxAmount;

            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(Order::max('id') + 1, 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'user_id'          => Auth::id(),
                'order_number'     => $orderNumber,
                'status'           => 'Pending',
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'Unpaid',
                'shipping_address' => $request->shipping_address,
                'phone'            => $request->phone,
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'tax_rate'         => $taxRate,
                'tax_amount'       => $taxAmount,
                'total_amount'     => $total,
                'notes'            => $request->notes,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity'     => $item->quantity,
                    'unit_price'   => $item->product->effective_price,
                    'subtotal'     => $item->product->effective_price * $item->quantity,
                ]);
                $item->product->decrement('stock', $item->quantity);
            }

            Payment::create([
                'order_id' => $order->id,
                'user_id'  => Auth::id(),
                'amount'   => $total,
                'method'   => $request->payment_method,
                'status'   => 'Pending',
            ]);

            CartItem::where('user_id', Auth::id())->delete();
            session()->forget('coupon_code');

            DB::commit();

            // Emails
            $order->load(['user', 'orderItems']);
            try { Mail::to($order->user->email)->send(new OrderConfirmationMail($order)); }
            catch (\Exception $e) { Log::error('Confirm email failed: ' . $e->getMessage()); }

            try {
                foreach (User::where('role', 'Admin')->get() as $admin) {
                    Mail::to($admin->email)->send(new AdminNewOrderMail($order));
                }
            } catch (\Exception $e) { Log::error('Admin email failed: ' . $e->getMessage()); }

            // Notifications
            try {
                Notification::notifyAdmin(
                    type: 'order',
                    title: 'New Order #' . $order->id,
                    message: Auth::user()->name . ' placed an order — $' . number_format($order->total_amount, 2),
                    url: route('admin.order.detail', $order->id)
                );
                Notification::notifyUser(
                    userId: $order->user_id,
                    type: 'order',
                    title: 'Order Confirmed! 🎉',
                    message: 'Your order #' . $order->id . ' has been placed successfully.',
                    url: route('order.detail', $order->id)
                );
            } catch (\Exception $e) { Log::error('Notification failed: ' . $e->getMessage()); }

            if ($request->payment_method === 'ABA')          return redirect()->route('payment.aba',  $order->id);
            if ($request->payment_method === 'KHQR')         return redirect()->route('payment.khqr', $order->id);
            if ($request->payment_method === 'BankTransfer') return redirect()->route('payment.bank', $order->id);

            return redirect()->route('order.success', $order->id)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order failed: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function success(int $orderId)
    {
        $order = Order::with(['orderItems', 'user'])
            ->where('id', $orderId)->where('user_id', Auth::id())->firstOrFail();
        return view('orders.order-success', compact('order'));
    }

    public function myOrders()
    {
        $orders = Order::with('orderItems')->where('user_id', Auth::id())->latest()->paginate(10);
        return view('orders.my-orders', compact('orders'));
    }

    public function detail(int $id)
    {
        $order = Order::with(['orderItems.product', 'user'])
            ->where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        return view('orders.detail', compact('order'));
    }
}