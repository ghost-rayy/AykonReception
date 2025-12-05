<?php

namespace App\Http\Controllers;

use App\Models\Visitors;
use App\Models\VisitorCategory;
use App\Models\VisitorBlacklist;
use App\Models\VisitorPreregistration;
use App\Models\VisitorFeedback;
use App\Models\Staff;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        $query = Visitors::with(['user', 'category'])->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by VIP
        if ($request->filled('vip')) {
            $query->vip();
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $visitors = $query->paginate(20);
        
        // Safely get categories - check if table exists
        try {
            $categories = VisitorCategory::where('is_active', true)->orderBy('priority')->get();
        } catch (\Exception $e) {
            $categories = collect([]);
        }

        return view('visitors.index', compact('visitors', 'categories'));
    }

    public function history($phone)
    {
        $visitors = Visitors::where('phone', $phone)
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->get();

        $visitorInfo = $visitors->first();
        
        return view('visitors.history', compact('visitors', 'visitorInfo'));
    }

    public function create()
    {
        $users = \App\Models\User::where('role', 'staff')->get();
        try {
            $categories = VisitorCategory::where('is_active', true)->orderBy('priority')->get();
        } catch (\Exception $e) {
            $categories = collect([]);
        }
        return view('visitors.create', compact('users', 'categories'));
    }

    public function store(Request $request)
    {
        // Check blacklist
        if (VisitorBlacklist::isBlacklisted($request->phone, $request->email)) {
            return back()->withErrors(['phone' => 'This visitor is on the blacklist. Please contact administration.'])->withInput();
        }

        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'nullable|email',
            'company' => 'nullable|string',
            'purpose' => 'required',
            'staff_to_visit' => 'required',
            'category_id' => 'nullable|exists:visitor_categories,id',
            'visitor_type' => 'nullable|in:regular,contractor,vendor,guest',
            'is_vip' => 'nullable|boolean',
            'photo_data' => 'nullable|string',
        ]);

        $photoPath = null;
        if ($request->photo_data) {
            $imageData = $request->photo_data;
            $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace('data:image/jpg;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);

            $imageName = 'visitor_' . time() . '_' . uniqid() . '.jpg';
            $photoPath = 'visitors/' . $imageName;

            Storage::disk('public')->put($photoPath, base64_decode($imageData));
        }

        $visitor = Visitors::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'company' => $request->company,
            'purpose' => $request->purpose,
            'user_id' => $request->staff_to_visit,
            'category_id' => $request->category_id,
            'visitor_type' => $request->visitor_type ?? 'regular',
            'is_vip' => $request->has('is_vip'),
            'check_in_time' => Carbon::now(),
            'check_out_time' => null,
            'status' => 'checked_in',
            'photo_path' => $photoPath,
            'wait_start_time' => Carbon::now(), // Start wait time tracking
        ]);

        // Create notifications
        $visitor->load('user');
        if ($visitor->is_vip) {
            Notification::createVIPVisitor($visitor);
        } else {
            Notification::createVisitorArrival($visitor);
        }

        return redirect()->route('visitors.index')->with('success', 'Visitor checked in successfully');
    }

    public function show($id)
    {
        $visitor = Visitors::with(['user', 'category', 'feedback'])->findOrFail($id);
        $history = Visitors::where('phone', $visitor->phone)
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('visitors.show', compact('visitor', 'history'));
    }

    public function updateNotes(Request $request, $id)
    {
        $visitor = Visitors::findOrFail($id);
        
        $request->validate([
            'notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $visitor->update([
            'notes' => $request->notes,
            'internal_notes' => $request->internal_notes,
        ]);

        return back()->with('success', 'Notes updated successfully');
    }

    public function checkout($id)
    {
        $visitor = Visitors::findOrFail($id);
        
        // End wait time if still waiting
        if ($visitor->wait_start_time) {
            $visitor->endWaitTime();
        }

        $visitor->update([
            'check_out_time' => Carbon::now(),
            'status' => 'checked_out'
        ]);

        return redirect()->route('visitors.index')->with('success', 'Visitor checked out');
    }

    public function startWaitTime($id)
    {
        $visitor = Visitors::findOrFail($id);
        $visitor->startWaitTime();
        return response()->json(['success' => true, 'wait_start_time' => $visitor->wait_start_time]);
    }

    public function endWaitTime($id)
    {
        $visitor = Visitors::findOrFail($id);
        $visitor->endWaitTime();
        return response()->json(['success' => true, 'wait_duration' => $visitor->wait_duration_minutes]);
    }

    public function destroy($id)
    {
        Visitors::destroy($id);
        return redirect()->route('visitors.index')->with('success', 'Visitor deleted');
    }

    // Pre-registration methods
    public function preregister()
    {
        $users = \App\Models\User::where('role', 'staff')->get();
        $categories = VisitorCategory::where('is_active', true)->orderBy('priority')->get();
        return view('visitors.preregister', compact('users', 'categories'));
    }

    public function storePreregistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'nullable|email',
            'company' => 'nullable|string',
            'purpose' => 'required',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:visitor_categories,id',
            'expected_arrival_time' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $preregistration = VisitorPreregistration::create($request->all());
        $preregistration->generateQRCode();

        return redirect()->route('visitors.preregistrations')->with('success', 'Pre-registration created. QR code: ' . $preregistration->qr_code);
    }

    public function preregistrations()
    {
        $preregistrations = VisitorPreregistration::with(['user', 'category'])
            ->orderBy('expected_arrival_time', 'desc')
            ->paginate(20);
        
        return view('visitors.preregistrations', compact('preregistrations'));
    }

    public function checkInFromPreregistration($id)
    {
        $preregistration = VisitorPreregistration::findOrFail($id);
        
        if ($preregistration->status !== 'pending') {
            return back()->with('error', 'This pre-registration has already been processed');
        }

        // Check blacklist
        if (VisitorBlacklist::isBlacklisted($preregistration->phone, $preregistration->email)) {
            $preregistration->update(['status' => 'canceled']);
            return back()->withErrors(['error' => 'This visitor is on the blacklist.']);
        }

        $visitor = Visitors::create([
            'name' => $preregistration->name,
            'phone' => $preregistration->phone,
            'email' => $preregistration->email,
            'company' => $preregistration->company,
            'purpose' => $preregistration->purpose,
            'user_id' => $preregistration->user_id,
            'category_id' => $preregistration->category_id,
            'check_in_time' => Carbon::now(),
            'status' => 'checked_in',
            'wait_start_time' => Carbon::now(),
        ]);

        $preregistration->update([
            'status' => 'completed',
            'checked_in_at' => Carbon::now(),
            'checked_in_by' => auth()->id(),
        ]);

        // Create notification
        $visitor->load('user');
        if ($visitor->is_vip) {
            Notification::createVIPVisitor($visitor);
        } else {
            Notification::createVisitorArrival($visitor);
        }

        return redirect()->route('visitors.index')->with('success', 'Visitor checked in from pre-registration');
    }

    // Feedback methods
    public function feedback($id)
    {
        $visitor = Visitors::findOrFail($id);
        return view('visitors.feedback', compact('visitor'));
    }

    public function storeFeedback(Request $request, $id)
    {
        $visitor = Visitors::findOrFail($id);

        $request->validate([
            'rating' => 'nullable|integer|min:1|max:5',
            'comments' => 'nullable|string',
            'feedback_type' => 'nullable|in:general,service,facility,staff',
        ]);

        VisitorFeedback::create([
            'visitor_id' => $visitor->id,
            'rating' => $request->rating,
            'comments' => $request->comments,
            'feedback_type' => $request->feedback_type ?? 'general',
            'is_anonymous' => $request->has('is_anonymous'),
            'visitor_email' => $request->is_anonymous ? null : $request->visitor_email,
        ]);

        return redirect()->route('visitors.index')->with('success', 'Thank you for your feedback!');
    }

    // Public check-in methods
    public function publicCheckIn()
    {
        $users = \App\Models\User::where('role', 'staff')->get();
        return view("check-in", compact('users'));
    }

    public function publicstore(Request $request)
    {
        // Check blacklist
        if (VisitorBlacklist::isBlacklisted($request->phone, $request->email)) {
            return back()->withErrors(['phone' => 'Access denied. Please contact reception.'])->withInput();
        }

        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'email' => 'nullable|email',
            'purpose' => 'required',
            'staff_to_visit' => 'required',
            'photo_data' => 'nullable|string',
        ]);

        $photoPath = null;
        if ($request->photo_data) {
            $imageData = $request->photo_data;
            $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace('data:image/jpg;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);

            $imageName = 'visitor_' . time() . '_' . uniqid() . '.jpg';
            $photoPath = 'visitors/' . $imageName;

            Storage::disk('public')->put($photoPath, base64_decode($imageData));
        }

        $visitor = Visitors::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'purpose' => $request->purpose,
            'user_id' => $request->staff_to_visit,
            'check_in_time' => Carbon::now(),
            'check_out_time' => null,
            'status' => 'checked_in',
            'photo_path' => $photoPath,
            'visitor_type' => 'regular',
            'wait_start_time' => Carbon::now(),
        ]);

        // Create notification
        $visitor->load('user');
        Notification::createVisitorArrival($visitor);

        return redirect()->route("check-in")->with('success', 'Visitor checked in successfully');
    }
}
