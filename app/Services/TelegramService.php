<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private Client $client;
    private string $token;
    private string $chatId;
    private string $groupId;

    public function __construct()
    {
        $this->client  = new Client(['verify' => false, 'timeout' => 10]);
        $this->token   = config('services.telegram.token');
        $this->chatId  = config('services.telegram.chat_id');
        $this->groupId = config('services.telegram.group_id', '');
    }

    // ── Send to personal chat ──────────────────────────────
    public function send(string $message): bool
    {
        return $this->sendTo($this->chatId, $message);
    }

    // ── Send to group ──────────────────────────────────────
    public function sendToGroup(string $message): bool
    {
        if (!$this->groupId) return false;
        return $this->sendTo($this->groupId, $message);
    }

    // ── Send to both personal + group ─────────────────────
    public function sendAll(string $message): bool
    {
        $personal = $this->send($message);
        $group    = $this->sendToGroup($message);
        return $personal || $group;
    }

    // ── Core send method ───────────────────────────────────
    private function sendTo(string $chatId, string $message): bool
    {
        try {
            $this->client->post(
                "https://api.telegram.org/bot{$this->token}/sendMessage",
                [
                    'json' => [
                        'chat_id'    => $chatId,
                        'text'       => $message,
                        'parse_mode' => 'HTML',
                    ],
                ]
            );
            return true;
        } catch (\Exception $e) {
            Log::error("Telegram send to {$chatId} failed: " . $e->getMessage());
            return false;
        }
    }

    // ── Send photo to personal ─────────────────────────────
    public function sendPhoto(string $photoPath, string $caption = ''): bool
    {
        return $this->sendPhotoTo($this->chatId, $photoPath, $caption);
    }

    // ── Send photo to group ────────────────────────────────
    public function sendPhotoToGroup(string $photoPath, string $caption = ''): bool
    {
        if (!$this->groupId) return false;
        return $this->sendPhotoTo($this->groupId, $photoPath, $caption);
    }

    // ── Send photo to both ─────────────────────────────────
    public function sendPhotoAll(string $photoPath, string $caption = ''): bool
    {
        $personal = $this->sendPhoto($photoPath, $caption);
        $group    = $this->sendPhotoToGroup($photoPath, $caption);
        return $personal || $group;
    }

    // ── Core send photo ────────────────────────────────────
    private function sendPhotoTo(string $chatId, string $photoPath, string $caption): bool
    {
        try {
            $this->client->post(
                "https://api.telegram.org/bot{$this->token}/sendPhoto",
                [
                    'multipart' => [
                        ['name' => 'chat_id',    'contents' => $chatId],
                        ['name' => 'photo',      'contents' => fopen($photoPath, 'r')],
                        ['name' => 'caption',    'contents' => $caption],
                        ['name' => 'parse_mode', 'contents' => 'HTML'],
                    ],
                ]
            );
            return true;
        } catch (\Exception $e) {
            Log::error("Telegram photo to {$chatId} failed: " . $e->getMessage());
            return false;
        }
    }



    // ── Slip message (full order details) ────────────────────
    public function slipMessage(object $order, string $note = ''): string
    {
        $items = $order->orderItems->map(fn($i) =>
            "  • {$i->product_name} x{$i->quantity} = \${$i->subtotal}"
        )->join("\n");

        return "-<b>Payment Slip Received / ស្លីបការទូទាត់</b>\n\n"
            . "- Order: <b>#RA-{$order->id}</b>\n"
            . "- Customer: <b>{$order->user->name}</b>\n"
            . "- Email: {$order->user->email}\n"
            . "- Phone: " . ($order->phone ?? '—') . "\n"
            . "- Address: {$order->shipping_address}\n\n"
            . "- <b>Items:</b>\n{$items}\n\n"
            . "- Total: <b>\${$order->total_amount}</b>\n"
            . "- Method: Bank Transfer\n"
            . ($note ? "- Note: {$note}\n" : '')
            . "-Time: " . now()->format('d/m/Y H:i') . "\n\n"
            . "✅ Please verify and confirm order!\n";
    }
}