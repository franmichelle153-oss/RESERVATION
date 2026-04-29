<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        session()->save(); // release session lock — fixes 18s freeze

        $rows = AppNotification::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->map(fn($n) => [
                'id'       => $n->id,
                'type'     => $n->type,
                'title'    => $n->title,
                'message'  => $n->message,
                'icon'     => $n->icon,
                'color'    => $n->color,
                'read'     => $n->isRead(),
                'time_ago' => $n->created_at->diffForHumans(),
                'data'     => $n->data ?? [],
            ]);

        return response()->json([
            'notifications' => $rows,
            'unread_count'  => AppNotification::where('user_id', auth()->id())
                                ->whereNull('read_at')
                                ->count(),
        ]);
    }

    public function count()
    {
        session()->save(); // release session lock — fixes 18s freeze

        return response()->json([
            'unread_count' => AppNotification::where('user_id', auth()->id())
                ->whereNull('read_at')
                ->count(),
        ]);
    }

    public function markRead($id)
    {
        AppNotification::where('id', $id)
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        AppNotification::where('id', $id)
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json(['success' => true]);
    }

    public function destroyBulk(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No IDs provided.']);
        }

        AppNotification::whereIn('id', $ids)
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json(['success' => true]);
    }
}