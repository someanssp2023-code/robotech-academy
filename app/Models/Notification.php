<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type', 'title', 'message', 'icon', 'url',
        'user_id', 'is_read', 'for_admin', 'for_user',
    ];

    protected $casts = [
        'is_read'   => 'boolean',
        'for_admin' => 'boolean',
        'for_user'  => 'boolean',
    ];

    public function scopeForAdmin($query)
    {
        return $query->where('for_admin', true);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('for_user', true)->where('user_id', $userId);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public static function notifyAdmin(string $type, string $title, string $message, string $url = null): self
    {
        $icons = ['order'=>'🛒','review'=>'⭐','user'=>'👤','stock'=>'📦','payment'=>'💳','system'=>'⚙️'];
        return self::create([
            'type'      => $type,
            'title'     => $title,
            'message'   => $message,
            'icon'      => $icons[$type] ?? '🔔',
            'url'       => $url,
            'for_admin' => true,
            'for_user'  => false,
            'is_read'   => false,
        ]);
    }

    public static function notifyUser(int $userId, string $type, string $title, string $message, string $url = null): self
    {
        $icons = ['order'=>'🛒','payment'=>'💳','system'=>'⚙️'];
        return self::create([
            'type'      => $type,
            'title'     => $title,
            'message'   => $message,
            'icon'      => $icons[$type] ?? '🔔',
            'url'       => $url,
            'user_id'   => $userId,
            'for_admin' => false,
            'for_user'  => true,
            'is_read'   => false,
        ]);
    }

    public function timeAgo(): string
    {
        $diff = now()->diffInSeconds($this->created_at);
        if ($diff < 60)    return 'just now';
        if ($diff < 3600)  return floor($diff / 60) . 'm ago';
        if ($diff < 86400) return floor($diff / 3600) . 'h ago';
        return floor($diff / 86400) . 'd ago';
    }
}