<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Visitors;
use App\Models\Appointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['staff', 'receptionist'])->where('id', '!=', Auth::id())->get();
        $messages = Message::where(function($query) {
            $query->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
        })
        ->where(function($query) {
            // Exclude system messages and status change messages
            $query->where('is_system_message', false)
                  ->orWhereNull('is_system_message');
        })
        ->where('message', 'not like', '%status changed%')
        ->where('message', 'not like', '%Status changed%')
        ->where('message', 'not like', '%is now free%')
        ->where('message', 'not like', '%is now in a meeting%')
        ->where('message', 'not like', '%is now busy%')
        ->where('message', 'not like', '%is now available%')
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'desc')
        ->get();

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
        ->where(function($query) {
            // Exclude system messages and status change messages
            $query->where('is_system_message', false)
                  ->orWhereNull('is_system_message');
        })
        ->where('message', 'not like', '%status changed%')
        ->where('message', 'not like', '%Status changed%')
        ->where('message', 'not like', '%is now free%')
        ->where('message', 'not like', '%is now in a meeting%')
        ->where('message', 'not like', '%is now busy%')
        ->where('message', 'not like', '%is now available%')
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip,rar',
        ]);

        // At least message or attachment is required
        if (!$request->has('message') && !$request->hasFile('attachment')) {
            return response()->json([
                'success' => false,
                'error' => 'Either message text or attachment is required'
            ], 422);
        }

        // Check if this is a status change message
        $messageText = $request->message ?? '';
        $isSystemMessage = false;
        
        // Detect status change messages
        $statusPatterns = [
            'is now free',
            'is now in a meeting',
            'is now busy',
            'is now available',
            'status changed',
            'Status changed'
        ];
        
        foreach ($statusPatterns as $pattern) {
            if (stripos($messageText, $pattern) !== false) {
                $isSystemMessage = true;
                break;
            }
        }

        // Handle file upload
        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;
        $attachmentSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            // Determine if it's an image or document
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $attachmentType = in_array(strtolower($extension), $imageExtensions) ? 'image' : 'document';
            
            // Generate unique filename
            $fileName = 'message_' . time() . '_' . uniqid() . '.' . $extension;
            $folder = $attachmentType === 'image' ? 'messages/images' : 'messages/documents';
            $attachmentPath = $folder . '/' . $fileName;
            
            // Store file
            Storage::disk('public')->put($attachmentPath, file_get_contents($file));
            
            $attachmentName = $originalName;
            $attachmentSize = $file->getSize();
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $messageText,
            'is_system_message' => $isSystemMessage,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
            'attachment_size' => $attachmentSize,
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

    public function getNotifications()
    {
        $staff = User::where('role', 'staff')->get();

        $staffStatus = $staff->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'status' => $user->status ?? 'free',
                'last_updated' => $user->status_updated_at ? $user->status_updated_at->diffForHumans() : null,
            ];
        });

        return response()->json(['staff_status' => $staffStatus]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:free,busy',
        ]);

        $user = Auth::user();
        $oldStatus = $user->status;
        
        $user->update([
            'status' => $request->status,
            'status_updated_at' => now(),
        ]);

        // Create notification for status change (non-blocking)
        if ($oldStatus !== $request->status) {
            try {
                \App\Models\Notification::createStaffStatusChange($user, $oldStatus ?? 'free', $request->status);
            } catch (\Exception $e) {
                // Log error but don't fail the status update
                \Log::warning('Failed to create status change notification: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'status' => $request->status]);
    }

    public function getStatus()
    {
        try {
            $user = Auth::user();
            return response()->json([
                'status' => $user->status ?? 'free',
                'last_updated' => $user->status_updated_at ? $user->status_updated_at->diffForHumans() : null
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting user status: ' . $e->getMessage());
            return response()->json([
                'status' => 'free',
                'last_updated' => null
            ], 200);
        }
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

    public function getAllStaff()
    {
        $staff = User::where('role', 'staff')
            ->select('id', 'name', 'status', 'status_updated_at')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'status' => $user->status ?? 'free',
                    'last_updated' => $user->status_updated_at ? $user->status_updated_at->diffForHumans() : null,
                ];
            });

        return response()->json(['staff' => $staff]);
    }

    public function getRecentMessages()
    {
        $messages = Message::where('receiver_id', Auth::id())
            ->where(function($query) {
                // Exclude system messages and status change messages
                $query->where('is_system_message', false)
                      ->orWhereNull('is_system_message');
            })
            ->where('message', 'not like', '%status changed%')
            ->where('message', 'not like', '%Status changed%')
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json(['messages' => $messages]);
    }

    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->where(function($query) {
                // Exclude system messages and status change messages
                $query->where('is_system_message', false)
                      ->orWhereNull('is_system_message');
            })
            ->where('message', 'not like', '%status changed%')
            ->where('message', 'not like', '%Status changed%')
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getStaffNotifications()
    {
        $userId = Auth::id();
        
        // Get fresh visitors (checked in today, not checked out yet)
        $freshVisitors = Visitors::where('user_id', $userId)
            ->whereDate('created_at', today())
            ->whereNull('check_out_time')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($visitor) {
                return [
                    'id' => $visitor->id,
                    'type' => 'visitor',
                    'name' => $visitor->name,
                    'phone' => $visitor->phone,
                    'purpose' => $visitor->purpose,
                    'check_in_time' => $visitor->check_in_time ? $visitor->check_in_time->toDateTimeString() : null,
                    'check_in_human' => $visitor->check_in_time ? $visitor->check_in_time->diffForHumans() : null,
                    'photo_path' => $visitor->photo_path ? asset('storage/' . $visitor->photo_path) : null,
                    'status' => $visitor->status,
                ];
            });

        // Get upcoming appointments (today and future)
        $freshAppointments = Appointments::where('user_id', $userId)
            ->where('appointment_time', '>=', now()->startOfDay())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('appointment_time', 'asc')
            ->get()
            ->map(function($appointment) {
                return [
                    'id' => $appointment->id,
                    'type' => 'appointment',
                    'visitor_name' => $appointment->visitor_name,
                    'visitor_phone' => $appointment->visitor_phone,
                    'purpose' => $appointment->purpose,
                    'appointment_time' => $appointment->appointment_time ? $appointment->appointment_time->toDateTimeString() : null,
                    'appointment_time_human' => $appointment->appointment_time ? $appointment->appointment_time->diffForHumans() : null,
                    'appointment_time_formatted' => $appointment->appointment_time ? $appointment->appointment_time->format('M d, H:i') : null,
                    'status' => $appointment->status,
                ];
            });

        return response()->json([
            'visitors' => $freshVisitors,
            'appointments' => $freshAppointments,
            'visitor_count' => $freshVisitors->count(),
            'appointment_count' => $freshAppointments->count(),
        ]);
    }

    public function downloadAttachment($id)
    {
        $message = Message::where(function($query) {
            $query->where('sender_id', Auth::id())
                  ->orWhere('receiver_id', Auth::id());
        })->findOrFail($id);

        if (!$message->attachment_path || !Storage::disk('public')->exists($message->attachment_path)) {
            abort(404, 'Attachment not found');
        }

        return Storage::disk('public')->download($message->attachment_path, $message->attachment_name);
    }
}
