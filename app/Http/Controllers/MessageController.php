<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Visitors;
use App\Models\Appointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['staff', 'receptionist'])->where('id', '!=', Auth::id())->get();
        $messages = Message::where(function($query) {
            $query->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
        })->with(['sender', 'receiver'])->orderBy('created_at', 'desc')->get();

        return view('messages.index', compact('users', 'messages'));
    }

    public function getConversation($userId)
    {
        $messages = Message::where(function($query) use ($userId) {
            $query->where(function($q) use ($userId) {
                $q->where('sender_id', Auth::id())
                  ->where('receiver_id', $userId);
            })
            ->orWhere(function($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', Auth::id());
            });
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load(['sender', 'receiver'])
            ]);
        }

        return redirect()->back()->with('success', 'Message sent successfully!');
    }

    public function markAsRead($id)
    {
        $message = Message::where('receiver_id', Auth::id())->findOrFail($id);
        $message->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function getVisitorDetails($id)
    {
        $visitor = Visitors::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($visitor);
    }

    public function getAppointmentDetails($id)
    {
        $appointment = Appointments::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($appointment->load('visitor'));
    }
}
