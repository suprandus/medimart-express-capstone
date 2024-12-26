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
    

    public function viewNotification($id)
    {
        try {
            if(Auth::user()->role == 'user') {
                $notification = NotificationsUser::where('notification_id', $id)->first();

                if (!$notification) {
                    return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
                }
    
                $notification->status = 'read';
                $notification->save();
            }
            if(Auth::user()->role == 'vendor') {
                $notification = NotificationsPharmacy::where('notification_id', $id)->first();

                if (!$notification) {
                    return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
                }
    
                $notification->status = 'read';
                $notification->save();
            }
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'An error occurred'], 500);
        }
    }
}
