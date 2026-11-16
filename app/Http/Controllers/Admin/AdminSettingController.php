<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminSettingController extends Controller
{
    // ── Show settings page ────────────────────────────────
    public function index()
    {
        $taxRate  = (float) Setting::get('tax_rate', 10);
        $settings = [
            'store_name'         => Setting::get('store_name', 'Robotech Academy'),
            'store_email'        => Setting::get('store_email', ''),
            'store_phone'        => Setting::get('store_phone', ''),
            'store_address'      => Setting::get('store_address', ''),
            'currency'           => Setting::get('currency', 'USD'),
            'shipping_type'      => Setting::get('shipping_type', 'free'),
            'shipping_fee'       => Setting::get('shipping_fee', '0'),
            'free_shipping_min'  => Setting::get('free_shipping_min', '0'),
            'tax_on_shipping'    => Setting::get('tax_on_shipping', false),
            'prices_include_tax' => Setting::get('prices_include_tax', false),
            'payment_cod'          => Setting::get('payment_cod', true),
            'payment_bank_transfer'=> Setting::get('payment_bank_transfer', true),
            'payment_aba_pay'      => Setting::get('payment_aba_pay', true),
            'payment_khqr'         => Setting::get('payment_khqr', true),
        ];

        return view('admin.settings', compact('taxRate', 'settings'));
    }

    // ── Update general / shipping / payment settings ──────
    public function update(Request $request)
    {
        $tab = $request->input('_tab', 'general');

        if ($tab === 'shipping') {
            Setting::set('shipping_type',     $request->input('shipping_type', 'free'));
            Setting::set('shipping_fee',      $request->input('shipping_fee', 0));
            Setting::set('free_shipping_min', $request->input('free_shipping_min', 0));
            return back()->with('success', 'Shipping settings saved!');
        }

        if ($tab === 'payment') {
            Setting::set('payment_cod',           $request->has('payment_cod') ? true : false);
            Setting::set('payment_bank_transfer', $request->has('payment_bank_transfer') ? true : false);
            Setting::set('payment_aba_pay',       $request->has('payment_aba_pay') ? true : false);
            Setting::set('payment_khqr',          $request->has('payment_khqr') ? true : false);
            return back()->with('success', 'Payment settings saved!');
        }

        // Default: general
        $request->validate([
            'store_name'  => 'nullable|string|max:100',
            'store_email' => 'nullable|email|max:150',
            'store_phone' => 'nullable|string|max:30',
            'currency'    => 'nullable|string|max:10',
        ]);

        Setting::set('store_name',    $request->input('store_name', 'Robotech Academy'));
        Setting::set('store_email',   $request->input('store_email', ''));
        Setting::set('store_phone',   $request->input('store_phone', ''));
        Setting::set('store_address', $request->input('store_address', ''));
        Setting::set('currency',      $request->input('currency', 'USD'));

        return back()->with('success', 'General settings saved!');
    }

    // ── Update tax rate ───────────────────────────────────
    public function updateTax(Request $request)
    {
        $request->validate([
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        Setting::set('tax_rate',         $request->tax_rate);
        Setting::set('tax_on_shipping',  $request->has('tax_on_shipping') ? true : false);
        Setting::set('prices_include_tax', $request->has('prices_include_tax') ? true : false);

        return back()->with('tax_success', 'Tax rate updated to ' . $request->tax_rate . '%');
    }

    // ── Change admin password ─────────────────────────────
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'Password changed successfully!');
    }
}