<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReceptionistNotificationController extends Controller
{
    /**
     * Display a listing of database notifications for the logged-in receptionist.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $filter = $request->query('filter', 'all');

        $notificationsQuery = $user->notifications();

        if ($filter === 'unread') {
            $notificationsQuery->whereNull('read_at');
        }

        $notifications = $notificationsQuery->latest()->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();

        return view('receptionist.notifications.index', compact('notifications', 'unreadCount', 'totalCount', 'filter'));
    }

    /**
     * Mark a specific notification as read in the database (strictly isolated to logged-in user).
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        abort_if(!$notification, 404, 'الإشعار غير موجود أو لا تملك صلاحية الوصول إليه.');

        $notification->markAsRead();

        return response()->json([
            'success'      => true,
            'unread_count' => $user->unreadNotifications()->count(),
            'message'      => 'تم تحديد الإشعار كمقروء.',
        ]);
    }

    /**
     * Mark all unread notifications as read for the logged-in receptionist.
     */
    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success'      => true,
                'unread_count' => 0,
                'message'      => 'تم تحديد كافة الإشعارات كمقروءة وحفظها في قاعدة البيانات.',
            ]);
        }

        return back()->with('info', 'تم تحديد جميع الإشعارات كمقروءة بنجاح.');
    }
}
