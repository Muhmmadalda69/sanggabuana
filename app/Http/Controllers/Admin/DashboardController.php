<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Destination;
use App\Models\Gallery;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'destinations' => Destination::count(),
            'galleries' => Gallery::count(),
            'contacts' => Contact::count(),
            'unread_contacts' => Contact::unread()->count(),
            'testimonials' => Testimonial::count(),
        ];

        $recentContacts = Contact::latest()->limit(5)->get();
        $recentDestinations = Destination::latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentContacts', 'recentDestinations'));
    }

    public function unreadCount()
    {
        $recentContacts = Contact::latest()->limit(5)->get()->map(function($contact) {
            return [
                'id' => $contact->id,
                'name' => $contact->name,
                'subject' => $contact->subject,
                'message' => Str::limit($contact->message, 80),
                'is_read' => $contact->is_read,
                'created_time' => $contact->created_at->diffForHumans(),
                'show_url' => route('admin.contacts.show', $contact->id),
                'avatar_letter' => strtoupper(substr($contact->name, 0, 1)),
            ];
        });

        return response()->json([
            'unread_contacts' => Contact::unread()->count(),
            'unread_testimonials' => Testimonial::where('is_read', false)->count(),
            'total_contacts' => Contact::count(),
            'total_testimonials' => Testimonial::count(),
            'recent_contacts' => $recentContacts,
        ]);
    }
}
