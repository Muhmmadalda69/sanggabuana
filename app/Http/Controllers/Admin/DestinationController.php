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

        $purposes = \App\Models\Purpose::orderBy('name')->get();
        return view('admin.destinations.form', [
            'destination' => new Destination(),
            'purposes' => $purposes
        ]);
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
            'daily_quota' => 'nullable|integer|min:1',
            'kids_discount' => 'nullable|integer|min:0|max:100',
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
            'has_member_details' => 'nullable|boolean',
            'has_online_registration' => 'nullable|boolean',
            'purposes' => 'nullable|array',
            'purposes.*.has_custom_price' => 'nullable|boolean',
            'purposes.*.custom_price' => 'nullable|numeric|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['has_community'] = $request->boolean('has_community');
        $validated['has_purpose'] = $request->boolean('has_purpose');
        $validated['has_gender_details'] = $request->boolean('has_gender_details');
        $validated['has_member_details'] = $request->boolean('has_member_details');
        $validated['has_online_registration'] = $request->boolean('has_online_registration');

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

        $destination = Destination::create($validated);

        // Handle visit purposes custom pricing
        // Sync ALL purposes shown in the form (global + explicitly mapped)
        $purposesInput = $request->input('purposes', []);
        $syncData = [];
        foreach ($purposesInput as $purposeId => $pivotData) {
            $hasCustomPrice = isset($pivotData['has_custom_price']) && $pivotData['has_custom_price'] == '1';
            $customPrice = $hasCustomPrice ? (float) ($pivotData['custom_price'] ?? 0) : 0;

            if (\App\Models\Purpose::find($purposeId)) {
                $syncData[$purposeId] = [
                    'has_custom_price' => $hasCustomPrice,
                    'custom_price' => $customPrice,
                ];
            }
        }
        $destination->purposes()->sync($syncData);

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi berhasil ditambahkan!');
    }

    public function edit(Destination $destination)
    {
        if (auth()->user()->isKasir() && auth()->user()->destination_id !== $destination->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit destinasi ini.');
        }

        $purposes = \App\Models\Purpose::orderBy('name')->get();
        return view('admin.destinations.form', compact('destination', 'purposes'));
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
            'daily_quota' => 'nullable|integer|min:1',
            'kids_discount' => 'nullable|integer|min:0|max:100',
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
            'has_member_details' => 'nullable|boolean',
            'has_online_registration' => 'nullable|boolean',
            'purposes' => 'nullable|array',
            'purposes.*.has_custom_price' => 'nullable|boolean',
            'purposes.*.custom_price' => 'nullable|numeric|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['has_community'] = $request->boolean('has_community');
        $validated['has_purpose'] = $request->boolean('has_purpose');
        $validated['has_gender_details'] = $request->boolean('has_gender_details');
        $validated['has_member_details'] = $request->boolean('has_member_details');
        $validated['has_online_registration'] = $request->boolean('has_online_registration');

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

        // Handle visit purposes custom pricing
        // Sync ALL purposes shown in the form (global + explicitly mapped)
        $purposesInput = $request->input('purposes', []);
        $syncData = [];
        foreach ($purposesInput as $purposeId => $pivotData) {
            $hasCustomPrice = isset($pivotData['has_custom_price']) && $pivotData['has_custom_price'] == '1';
            $customPrice = $hasCustomPrice ? (float) ($pivotData['custom_price'] ?? 0) : 0;

            if (\App\Models\Purpose::find($purposeId)) {
                $syncData[$purposeId] = [
                    'has_custom_price' => $hasCustomPrice,
                    'custom_price' => $customPrice,
                ];
            }
        }
        $destination->purposes()->sync($syncData);

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
