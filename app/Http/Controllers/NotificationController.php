<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationsUser;
use App\Models\NotificationsPharmacy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            // For the user role
            if (Auth::user()->role == 'user') {
                $notifications = NotificationsUser::where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->get();
    
                return view('frontend.user.notifications.notifications', compact('notifications'));
            }
            if (Auth::user()->role == 'vendor') {
                $notifications = NotificationsPharmacy::where('vendor_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->get();
    
                return view('frontend.user.notifications.notifications', compact('notifications'));
            }
        }
    
        // If user is not authenticated
        return redirect()->route('login');
    }
    public function markAllRead()
    {
        try {
            if (Auth::user()->role == 'user') {
                NotificationsUser::where('user_id', Auth::id())->update(['status' => 'read']);
            } elseif (Auth::user()->role == 'vendor') {
                NotificationsPharmacy::where('vendor_id', Auth::id())->update(['status' => 'read']);
            }
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'An error occurred'], 500);
        }
    }
    public function viewNotification($id)
    {
        Log::info('Notification ID: ' . $id);
    
        try {
            $notification = null;
    
            if (Auth::user()->role === 'user') {
                $notification = NotificationsUser::where('user_id', Auth::id())
                    ->where('notification_id', $id)
                    ->first();
            } elseif (Auth::user()->role === 'vendor') {
                $notification = NotificationsPharmacy::where('vendor_id', Auth::id())
                    ->where('notification_id', $id)
                    ->first();
            }
    
            if (!$notification) {
                Log::warning('Notification not found: ' . $id);
                return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
            }
    
            $notification->status = 'read';
            $notification->save();
    
            Log::info('Notification updated to read: ' . $notification->notification_id);
    
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error updating notification: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'An error occurred'], 500);
        }
    }
    
}
