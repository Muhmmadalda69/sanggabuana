<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::with('destination')->orderBy('sort_order')->paginate(12);
        return view('admin.galleries.index', compact('galleries'));
    }

    public function create()
    {
        $destinations = Destination::orderBy('name')->get();
        return view('admin.galleries.form', ['gallery' => new Gallery(), 'destinations' => $destinations]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'nullable|exists:destinations,id',
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'caption' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('galleries', 'public');
        }

        Gallery::create($validated);

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Galeri berhasil ditambahkan!');
    }

    public function edit(Gallery $gallery)
    {
        $destinations = Destination::orderBy('name')->get();
        return view('admin.galleries.form', compact('gallery', 'destinations'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'destination_id' => 'nullable|exists:destinations,id',
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'caption' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($gallery->image && file_exists(public_path('storage/' . $gallery->image))) {
                unlink(public_path('storage/' . $gallery->image));
            }
            $validated['image'] = $request->file('image')->store('galleries', 'public');
        }

        $gallery->update($validated);

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Galeri berhasil diperbarui!');
    }

    public function destroy(Gallery $gallery)
    {
        if ($gallery->image && file_exists(public_path('storage/' . $gallery->image))) {
            unlink(public_path('storage/' . $gallery->image));
        }

        $gallery->delete();

        return redirect()->route('admin.galleries.index')
            ->with('success', 'Galeri berhasil dihapus!');
    }
}
