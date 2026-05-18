<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Clean up the old four separate keys to keep database clean
        Setting::whereIn('key', [
            'about_feature_1_title',
            'about_feature_1_desc',
            'about_feature_2_title',
            'about_feature_2_desc'
        ])->delete();

        // Auto-seed missing hero settings if they don't exist
        Setting::firstOrCreate(
            ['key' => 'hero_background'],
            [
                'value' => 'https://images.unsplash.com/photo-1542224566-6e85f2e6772f?q=80&w=2000&auto=format&fit=crop',
                'type' => 'image',
                'group' => 'hero',
                'label' => 'Gambar Latar Hero (Background)'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'hero_image'],
            [
                'value' => 'https://images.unsplash.com/photo-1542224566-6e85f2e6772f?q=80&w=800&auto=format&fit=crop',
                'type' => 'image',
                'group' => 'hero',
                'label' => 'Gambar Samping Hero (Floating)'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'about_video_type'],
            [
                'value' => 'link',
                'type' => 'select',
                'group' => 'about',
                'label' => 'Tipe Sumber Video'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'about_video_link'],
            [
                'value' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'type' => 'text',
                'group' => 'about',
                'label' => 'Link Video (YouTube / Direct Link / Google Drive)'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'about_video_file'],
            [
                'value' => '',
                'type' => 'video',
                'group' => 'about',
                'label' => 'Unggah File Video'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'about_features'],
            [
                'value' => json_encode([
                    ['title' => 'Aman & Nyaman', 'desc' => 'Jalur terkelola baik'],
                    ['title' => 'Spot Foto', 'desc' => 'Pemandangan indah']
                ]),
                'type' => 'features',
                'group' => 'about',
                'label' => 'Daftar Fitur Tentang Kami (Dapat Ditambah/Dikurangi)'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'weather_latitude'],
            [
                'value' => '-6.505',
                'type' => 'text',
                'group' => 'cuaca',
                'label' => 'Koordinat Lintang (Latitude) Cuaca'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'weather_longitude'],
            [
                'value' => '107.218',
                'type' => 'text',
                'group' => 'cuaca',
                'label' => 'Koordinat Bujur (Longitude) Cuaca'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'weather_mode'],
            [
                'value' => 'auto',
                'type' => 'select',
                'group' => 'cuaca',
                'label' => 'Mode Status Cuaca & Kondisi Jalur'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'weather_manual_status'],
            [
                'value' => 'Jalur Ditutup',
                'type' => 'text',
                'group' => 'cuaca',
                'label' => 'Status Manual / Darurat'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'weather_manual_desc'],
            [
                'value' => 'Ditutup sementara untuk pemulihan ekosistem hutan',
                'type' => 'text',
                'group' => 'cuaca',
                'label' => 'Deskripsi Kondisi Manual'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'weather_manual_icon'],
            [
                'value' => 'alert-triangle',
                'type' => 'select',
                'group' => 'cuaca',
                'label' => 'Ikon Status Manual'
            ]
        );

        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // 1. Handle normal text/textarea settings
        $settings = $request->input('settings', []);
        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                // Filter out empty rows
                $value = array_values(array_filter($value, function($item) {
                    return !empty($item['title']) || !empty($item['desc']);
                }));
                $value = json_encode($value);
            }
            Setting::set($key, $value);
        }

        // 2. Handle file/image uploads
        if ($request->hasFile('settings_files')) {
            foreach ($request->file('settings_files') as $key => $file) {
                if ($file->isValid()) {
                    $path = $file->store('settings', 'public');
                    Setting::set($key, '/storage/' . $path);
                }
            }
        }

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }
}
