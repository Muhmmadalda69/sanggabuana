<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purpose;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PurposeController extends Controller
{
    public function index()
    {
        $purposes = Purpose::orderBy('name')->paginate(10);
        return view('admin.purposes.index', compact('purposes'));
    }

    public function create()
    {
        $destinations = Destination::orderBy('name')->get();
        return view('admin.purposes.form', [
            'purpose' => new Purpose(),
            'destinations' => $destinations
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_all_destinations' => 'nullable|boolean',
            'destinations' => 'nullable|array',
            'destinations.*' => 'exists:destinations,id',
        ], [
            'name.required' => 'Nama tujuan kunjungan wajib diisi.',
            'destinations.*.exists' => 'Destinasi yang dipilih tidak valid.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_all_destinations'] = $request->boolean('is_all_destinations');

        $purpose = Purpose::create($validated);

        if (!$validated['is_all_destinations'] && $request->has('destinations')) {
            // Attach with default pivot values
            $attachData = [];
            foreach ($request->input('destinations') as $destId) {
                $attachData[$destId] = ['has_custom_price' => false, 'custom_price' => 0];
            }
            $purpose->destinations()->attach($attachData);
        }

        return redirect()->route('admin.purposes.index')
            ->with('success', 'Tujuan kunjungan baru berhasil ditambahkan!');
    }

    public function edit(Purpose $purpose)
    {
        $destinations = Destination::orderBy('name')->get();
        return view('admin.purposes.form', compact('purpose', 'destinations'));
    }

    public function update(Request $request, Purpose $purpose)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_all_destinations' => 'nullable|boolean',
            'destinations' => 'nullable|array',
            'destinations.*' => 'exists:destinations,id',
        ], [
            'name.required' => 'Nama tujuan kunjungan wajib diisi.',
            'destinations.*.exists' => 'Destinasi yang dipilih tidak valid.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_all_destinations'] = $request->boolean('is_all_destinations');

        $purpose->update($validated);

        if ($validated['is_all_destinations']) {
            // Global purpose — no need for explicit mappings, but preserve any custom pricing
            // Don't detach: keep pivot rows for custom pricing data
        } else {
            // Sync destinations, preserving existing custom price pivot data
            $requestedDestIds = $request->input('destinations', []);
            $existingPivot = $purpose->destinations()->get()->keyBy('id');
            $syncData = [];
            foreach ($requestedDestIds as $destId) {
                if ($existingPivot->has($destId)) {
                    // Preserve existing pivot data (custom prices set via destination form)
                    $pivot = $existingPivot->get($destId)->pivot;
                    $syncData[$destId] = [
                        'has_custom_price' => $pivot->has_custom_price,
                        'custom_price' => $pivot->custom_price,
                    ];
                } else {
                    $syncData[$destId] = ['has_custom_price' => false, 'custom_price' => 0];
                }
            }
            $purpose->destinations()->sync($syncData);
        }

        return redirect()->route('admin.purposes.index')
            ->with('success', 'Tujuan kunjungan berhasil diperbarui!');
    }

    public function destroy(Purpose $purpose)
    {
        $purpose->delete();

        return redirect()->route('admin.purposes.index')
            ->with('success', 'Tujuan kunjungan berhasil dihapus!');
    }
}
