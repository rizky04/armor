<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::all();
        return view('category.index', compact('category'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->only(['name']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/category'), $imageName);
            $data['image'] = $imageName;
        }

        Category::create($data);

        return redirect()->route('category.index')
            ->with('success', 'category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category) : View
    {
        return view('category.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category) : View
    {
        return view('category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category) : RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $data = $request->only(['name']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/category'), $imageName);
            $data['image'] = $imageName;

            // Optional: hapus gambar lama jika ada
            if ($category->image && file_exists(public_path('uploads/category/' . $category->image))) {
                unlink(public_path('uploads/category/' . $category->image));
            }
        }

        $category->update($data);

        return redirect()->route('category.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Optional: hapus gambar lama jika ada
        if ($category->image && file_exists(public_path('uploads/category/' . $category->image))) {
            unlink(public_path('uploads/category/' . $category->image));
        }

        $category->delete();

        return redirect()->route('category.index')
            ->with('success', 'Category deleted successfully.');
    }
}
