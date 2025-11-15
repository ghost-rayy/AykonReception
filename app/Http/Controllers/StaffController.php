<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::all();
        $users = \App\Models\User::where('role', 'staff')->get();
        return view('staff.index', compact('staff', 'users'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'position' => 'required',
        ]);

        Staff::create($request->all());
        return redirect()->route('staff.index')->with('success', 'Staff added successfully');
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $staff->update($request->all());
        return redirect()->route('staff.index')->with('success', 'Staff updated successfully');
    }

    public function destroy($id)
    {
        Staff::destroy($id);
        return redirect()->route('staff.index')->with('success', 'Staff deleted successfully');
    }
}
