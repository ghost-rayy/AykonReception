<?php

namespace App\Http\Controllers;

use App\Models\VisitorCategory;
use App\Models\VisitorBlacklist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VisitorManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Category Management
    public function categories()
    {
        $categories = VisitorCategory::orderBy('priority')->get();
        return view('visitors.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('visitors.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer',
        ]);

        VisitorCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'color' => $request->color,
            'description' => $request->description,
            'priority' => $request->priority ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('visitors.categories')->with('success', 'Category created successfully');
    }

    public function editCategory($id)
    {
        $category = VisitorCategory::findOrFail($id);
        return view('visitors.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $category = VisitorCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string',
            'description' => 'nullable|string',
            'priority' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'color' => $request->color,
            'description' => $request->description,
            'priority' => $request->priority ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('visitors.categories')->with('success', 'Category updated successfully');
    }

    public function deleteCategory($id)
    {
        $category = VisitorCategory::findOrFail($id);
        $category->delete();
        return redirect()->route('visitors.categories')->with('success', 'Category deleted successfully');
    }

    // Blacklist Management
    public function blacklist()
    {
        $blacklist = VisitorBlacklist::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('visitors.blacklist.index', compact('blacklist'));
    }

    public function createBlacklist()
    {
        return view('visitors.blacklist.create');
    }

    public function storeBlacklist(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'expires_at' => 'nullable|date|after:now',
        ]);

        VisitorBlacklist::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);

        return redirect()->route('visitors.blacklist')->with('success', 'Visitor added to blacklist');
    }

    public function editBlacklist($id)
    {
        $blacklist = VisitorBlacklist::findOrFail($id);
        return view('visitors.blacklist.edit', compact('blacklist'));
    }

    public function updateBlacklist(Request $request, $id)
    {
        $blacklist = VisitorBlacklist::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $blacklist->update($request->all());

        return redirect()->route('visitors.blacklist')->with('success', 'Blacklist entry updated');
    }

    public function deleteBlacklist($id)
    {
        $blacklist = VisitorBlacklist::findOrFail($id);
        $blacklist->delete();
        return redirect()->route('visitors.blacklist')->with('success', 'Blacklist entry removed');
    }
}
