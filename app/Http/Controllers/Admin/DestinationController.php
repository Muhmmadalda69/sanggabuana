<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DestinationController extends Controller
{
    public function index()
    {
        if (auth()->user()->isKasir()) {
            if (auth()->user()->destination_id) {
                return redirect()->route('admin.destinations.edit', auth()->user()->destination_id);
            }
            return redirect()->route('admin.dashboard')->with('error', 'Anda belum ditugaskan ke destinasi mana pun.');
        }

        $destinations = Destination::orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.destinations.index', compact('destinations'));
    }

    public function create()
    {
        if (auth()->user()->isKasir()) {
            abort(403, 'Kasir tidak diperbolehkan membuat destinasi baru.');
        }

        return view('admin.destinations.form', ['destination' => new Destination()]);
    }

    public function store(Request $request)
    {
        if (auth()->user()->isKasir()) {
            abort(403, 'Kasir tidak diperbolehkan membuat destinasi baru.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'location' => 'nullable|string|max:255',
            'altitude' => 'nullable|string|max:100',
            'operational_days' => 'nullable|string|max:100',
            'operational_hours' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'contacts' => 'nullable|array',
            'has_community' => 'nullable|boolean',
            'has_purpose' => 'nullable|boolean',
            'has_gender_details' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['has_community'] = $request->boolean('has_community');
        $validated['has_purpose'] = $request->boolean('has_purpose');
        $validated['has_gender_details'] = $request->boolean('has_gender_details');

        // Filter out empty contacts
        $contacts = $request->input('contacts', []);
        $filteredContacts = [];
        if (is_array($contacts)) {
            foreach ($contacts as $contact) {
                if (!empty($contact['platform']) && !empty($contact['value'])) {
                    $filteredContacts[] = [
                        'platform' => $contact['platform'],
                        'value' => $contact['value']
                    ];
                }
            }
        }
        $validated['contacts'] = $filteredContacts;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('destinations', 'public');
        }

        Destination::create($validated);

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi berhasil ditambahkan!');
    }

    public function edit(Destination $destination)
    {
        if (auth()->user()->isKasir() && auth()->user()->destination_id !== $destination->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit destinasi ini.');
        }

        return view('admin.destinations.form', compact('destination'));
    }

    public function update(Request $request, Destination $destination)
    {
        if (auth()->user()->isKasir() && auth()->user()->destination_id !== $destination->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit destinasi ini.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'location' => 'nullable|string|max:255',
            'altitude' => 'nullable|string|max:100',
            'operational_days' => 'nullable|string|max:100',
            'operational_hours' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'contacts' => 'nullable|array',
            'has_community' => 'nullable|boolean',
            'has_purpose' => 'nullable|boolean',
            'has_gender_details' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['has_community'] = $request->boolean('has_community');
        $validated['has_purpose'] = $request->boolean('has_purpose');
        $validated['has_gender_details'] = $request->boolean('has_gender_details');

        // Filter out empty contacts
        $contacts = $request->input('contacts', []);
        $filteredContacts = [];
        if (is_array($contacts)) {
            foreach ($contacts as $contact) {
                if (!empty($contact['platform']) && !empty($contact['value'])) {
                    $filteredContacts[] = [
                        'platform' => $contact['platform'],
                        'value' => $contact['value']
                    ];
                }
            }
        }
        $validated['contacts'] = $filteredContacts;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($destination->image && file_exists(public_path('storage/' . $destination->image))) {
                unlink(public_path('storage/' . $destination->image));
            }
            $validated['image'] = $request->file('image')->store('destinations', 'public');
        }

        $destination->update($validated);

        if (auth()->user()->isKasir()) {
            return redirect()->route('admin.destinations.edit', $destination->id)
                ->with('success', 'Informasi destinasi Anda berhasil diperbarui!');
        }

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi berhasil diperbarui!');
    }

    public function destroy(Destination $destination)
    {
        if (auth()->user()->isKasir()) {
            abort(403, 'Kasir tidak diperbolehkan menghapus destinasi.');
        }

        if ($destination->image && file_exists(public_path('storage/' . $destination->image))) {
            unlink(public_path('storage/' . $destination->image));
        }

        $destination->delete();

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi berhasil dihapus!');
    }
}
