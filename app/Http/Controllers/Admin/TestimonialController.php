<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        // Mark all unread testimonials as read
        Testimonial::where('is_read', false)->update(['is_read' => true]);

        $testimonials = Testimonial::orderBy('sort_order')->paginate(10);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.form', ['testimonial' => new Testimonial()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'message' => 'required|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }

        Testimonial::create($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimoni berhasil ditambahkan!');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'message' => 'required|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('avatar')) {
            if ($testimonial->avatar && file_exists(public_path('storage/' . $testimonial->avatar))) {
                unlink(public_path('storage/' . $testimonial->avatar));
            }
            $validated['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }

        $testimonial->update($validated);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimoni berhasil diperbarui!');
    }

    public function toggle(Testimonial $testimonial)
    {
        $testimonial->update(['is_active' => !$testimonial->is_active]);
        $status = $testimonial->is_active ? 'diaktifkan (tampil di beranda)' : 'dinonaktifkan (disembunyikan dari beranda)';
        
        return redirect()->route('admin.testimonials.index')
            ->with('success', "Testimoni berhasil {$status}.");
    }

    public function destroy(Testimonial $testimonial)
    {
        if ($testimonial->avatar && file_exists(public_path('storage/' . $testimonial->avatar))) {
            unlink(public_path('storage/' . $testimonial->avatar));
        }

        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimoni berhasil dihapus!');
    }
}
