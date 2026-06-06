<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class NotificationController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()
            ->latest()
            ->paginate(10);

        return $this->success([
            'notifications' => NotificationResource::collection($notifications->items()),
            'unread_count' => $user->unreadNotifications()->count(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
            ]
        ]);
    }

    public function markAsRead($id)
    {
        $notification = request()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return $this->success(null, 'Notification marked as read');
    }

    public function markAllAsRead()
    {
        $user = request()->user();

        $user->unreadNotifications()->update(['read_at' => now()]);

        return $this->success(null, 'All read');
    }
}
