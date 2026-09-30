<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationController extends Controller
{
    /**
     * Get user notifications.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $notifications = $request->user()
            ->notifications()
            ->when($request->read === 'false', function ($query) {
                $query->whereNull('read_at');
            })
            ->when($request->read === 'true', function ($query) {
                $query->whereNotNull('read_at');
            })
            ->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return NotificationResource::collection($notifications);
    }

    /**
     * Mark a notification as read.
     */
    public function markRead(Request $request, $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        if ($notification->read_at) {
            return response()->json([
                'success' => false,
                'message' => 'Notification already marked as read',
            ], 422);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data' => [
                'id' => $notification->id,
                'read_at' => $notification->read_at,
            ],
        ], 200);
    }
}
