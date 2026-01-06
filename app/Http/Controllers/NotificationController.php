<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $notification = Notification::latest()->where('user_id', auth()->user()->id);
        $recent = $notification->clone()->whereRaw('created_at > NOW() - interval 7 day')->get();
        $previous = $notification->clone()->whereRaw('created_at < NOW() - interval 7 day')->get();
        // $layouts = (auth()->user()->role_id == User::SUPERADMIN) ? 'admin.layouts.template' : 'dash.layouts.template';

        $data = [
            'recent' => $recent,
            'previous' => $previous,
        ];
        return view('view_all_notif', $data);
    }

    public function read_redirect(string $id)
    {
        $notif = Notification::find($id);
        $notif->status = 'read';
        $notif->save();

        return redirect($notif->link);
    }

    public function hide_redirect(string $id)
    {
        $notif = Notification::find($id);
        
        if (!$notif) {
            return response()->json(['message' => 'Notification not found.'], 404);
        }

        $notif->status_hide = 'hide';
        $notif->save();

        $notif_count = Notification::where('user_id', auth()->id())
        ->where('status_hide', 'unhide')
        ->where('status', 'unread')
        ->count();

        return response()->json([
            'message' => 'Notification removed successfully.',
            'notif_count' => $notif_count
        ]);
    }


    public function readall(Request $request)
    {

        Notification::where('user_id', Auth::user()->id)
            ->where('status', 'unread')->update(['status' => 'read']);

        if ($request->ajax()) {
            return response()->json(array('error' => false));
        }

        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
