<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // ── Admin endpoints ──────────────────────────────────────────────

    public function adminCount()
    {
        $count = Notification::forAdmin()->unread()->count();
        return response()->json(['count' => $count]);
    }

    public function adminList()
    {
        $notifications = Notification::forAdmin()
            ->latest()
            ->take(30)
            ->get()
            ->map(fn($n) => [
                'id'      => $n->id,
                'type'    => $n->type,
                'title'   => $n->title,
                'message' => $n->message,
                'icon'    => $n->icon,
                'url'     => $n->url,
                'is_read' => $n->is_read,
                'time'    => $n->timeAgo(),
            ]);
        return response()->json(['notifications' => $notifications]);
    }

    public function adminMarkRead($id)
    {
        Notification::forAdmin()->where('id', $id)->update(['is_read' => true]);
        return response()->json(['ok' => true]);
    }

    public function adminMarkAllRead()
    {
        Notification::forAdmin()->unread()->update(['is_read' => true]);
        return response()->json(['ok' => true]);
    }

    public function adminClearAll()
    {
        Notification::forAdmin()->delete();
        return response()->json(['ok' => true]);
    }

    // ── User endpoints ───────────────────────────────────────────────

    public function userCount()
    {
        $count = Notification::forUser(Auth::id())->unread()->count();
        return response()->json(['count' => $count]);
    }

    public function userList()
    {
        $notifications = Notification::forUser(Auth::id())
            ->latest()
            ->take(20)
            ->get()
            ->map(fn($n) => [
                'id'      => $n->id,
                'type'    => $n->type,
                'title'   => $n->title,
                'message' => $n->message,
                'icon'    => $n->icon,
                'url'     => $n->url,
                'is_read' => $n->is_read,
                'time'    => $n->timeAgo(),
            ]);
        return response()->json(['notifications' => $notifications]);
    }

    public function userMarkRead($id)
    {
        Notification::forUser(Auth::id())->where('id', $id)->update(['is_read' => true]);
        return response()->json(['ok' => true]);
    }

    public function userMarkAllRead()
    {
        Notification::forUser(Auth::id())->unread()->update(['is_read' => true]);
        return response()->json(['ok' => true]);
    }
}