<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Destination;
use App\Models\Gallery;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredDestinations = Destination::active()->featured()
            ->orderBy('sort_order')->limit(6)->get();
        $destinations = Destination::active()
            ->orderBy('sort_order')->limit(8)->get();
        $galleries = Gallery::where('is_active', true)
            ->orderBy('sort_order')->limit(8)->get();
        $testimonials = Testimonial::active()
            ->orderBy('sort_order')->limit(6)->get();

        return view('landing', compact(
            'featuredDestinations', 'destinations', 'galleries', 'testimonials'
        ));
    }

    public function destination($slug)
    {
        $destination = Destination::where('slug', $slug)->active()->firstOrFail();
        $galleries = $destination->galleries()->where('is_active', true)->orderBy('sort_order')->get();
        $relatedDestinations = Destination::active()
            ->where('id', '!=', $destination->id)
            ->limit(3)->get();

        return view('destination-detail', compact('destination', 'galleries', 'relatedDestinations'));
    }

    public function destinations()
    {
        $destinations = Destination::active()->orderBy('sort_order')->paginate(9);
        return view('destinations', compact('destinations'));
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        Contact::create($validated);

        return back()->with('success', 'Pesan Anda telah terkirim! Kami akan segera menghubungi Anda.');
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->active()->firstOrFail();
        return view('page', compact('page'));
    }

    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'required|string|max:1000',
        ]);

        $validated['role'] = $request->filled('role') ? $request->role : 'Pengunjung';
        $validated['is_active'] = false;
        $validated['is_read'] = false;
        
        // Auto-increment sort_order to place new items at the bottom
        $nextSortOrder = (int) Testimonial::max('sort_order') + 1;
        $validated['sort_order'] = $nextSortOrder;

        Testimonial::create($validated);

        return back()->with('success', 'Terima kasih! Ulasan Anda telah berhasil dikirim dan menunggu persetujuan (kurasi) dari admin.');
    }
}
