<?php

namespace App\Http\Controllers;

use App\Models\Visitors;
use App\Models\Staff;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class VisitorController extends Controller
{
    public function index()
    {
        $visitors = Visitors::orderBy('created_at', 'desc')->get();
        return view('visitors.index', compact('visitors'));
    }

    public function create()
    {
        $users = \App\Models\User::where('role', 'staff')->get();
        return view('visitors.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'purpose' => 'required',
            'staff_to_visit' => 'required',
            'photo_data' => 'nullable|string',
        ]);

        $photoPath = null;
        if ($request->photo_data) {
            // Decode base64 image data
            $imageData = $request->photo_data;
            $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace('data:image/jpg;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);

            $imageName = 'visitor_' . time() . '_' . uniqid() . '.jpg';
            $photoPath = 'visitors/' . $imageName;

            // Save the image to storage
            Storage::disk('public')->put($photoPath, base64_decode($imageData));
        }



        Visitors::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'purpose' => $request->purpose,
            'user_id' => $request->staff_to_visit,
            'check_in_time' => Carbon::now(),
            'check_out_time' => null,
            'status' => 'checked_in',
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('visitors.index')->with('success', 'Visitor checked in');
    }

    public function checkout($id)
    {
        $visitor = Visitors::findOrFail($id);
        $visitor->update([
            'check_out_time' => Carbon::now(),
            'status' => 'checked_out'
        ]);
        return redirect()->route('visitors.index')->with('success', 'Visitor checked out');
    }

    public function destroy($id)
    {
        Visitors::destroy($id);
        return redirect()->route('visitors.index')->with('success', 'Visitor deleted');
    }
}
