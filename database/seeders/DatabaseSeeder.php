<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\Page;
use App\Models\User;
use App\Models\Destination;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users (Superadmin & Admin & Cashiers for existing destinations)
        User::updateOrCreate(
            ['email' => 'superadmin@sanggabuana.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('superadmin123'),
                'role' => 'superadmin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@sanggabuana.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // Create default Cashiers for all existing destinations
        foreach (Destination::all() as $dest) {
            User::updateOrCreate(
                ['email' => 'kasir.' . $dest->slug . '@sanggabuana.com'],
                [
                    'name' => 'Kasir ' . $dest->name,
                    'password' => Hash::make('kasir123'),
                    'role' => 'kasir',
                    'destination_id' => $dest->id,
                ]
            );
        }

        // 2. Testimonials
        $testimonials = [
            [
                'name' => 'Ahmad Hidayat',
                'role' => 'Pendaki Profesional',
                'message' => 'Gunung Sanggabuana memberikan pengalaman pendakian yang luar biasa. Pemandangan dari puncak benar-benar memukau. Jalur pendakian yang terawat dengan baik membuat perjalanan semakin menyenangkan.',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'role' => 'Travel Blogger',
                'message' => 'Salah satu hidden gem terbaik di Jawa Barat! Air terjun Cigentis sungguh menakjubkan. Sangat direkomendasikan untuk siapa saja yang mencintai alam dan petualangan.',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Budi Santoso',
                'role' => 'Fotografer Alam',
                'message' => 'Tempat yang sempurna untuk fotografi landscape. Sunrise dari Puncak Batu Kapur adalah salah satu yang terbaik yang pernah saya abadikan. Wajib dikunjungi!',
                'rating' => 5,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Diana Putri',
                'role' => 'Pecinta Alam',
                'message' => 'Camping di Hutan Pinus Sanggabuana adalah pengalaman yang tidak terlupakan. Udara segar, pemandangan indah, dan suasana yang sangat tenang. Pasti akan kembali lagi!',
                'rating' => 4,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::updateOrCreate([
                'name' => $testimonial['name'],
                'role' => $testimonial['role']
            ], $testimonial);
        }

        // 3. Settings
        $settings = [
            ['key' => 'site_name', 'value' => 'Wisata Gunung Sanggabuana', 'type' => 'text', 'group' => 'general', 'label' => 'Nama Situs'],
            ['key' => 'site_tagline', 'value' => 'Jelajahi Keindahan Alam Pegunungan', 'type' => 'text', 'group' => 'general', 'label' => 'Tagline'],
            ['key' => 'site_description', 'value' => 'Wisata Gunung Sanggabuana menawarkan pengalaman alam terbaik di Karawang, Jawa Barat. Nikmati pendakian, air terjun, dan keindahan alam pegunungan tropis.', 'type' => 'textarea', 'group' => 'general', 'label' => 'Deskripsi Situs'],
            ['key' => 'contact_email', 'value' => 'info@sanggabuana.com', 'type' => 'text', 'group' => 'contact', 'label' => 'Email'],
            ['key' => 'contact_phone', 'value' => '+62 812-3456-7890', 'type' => 'text', 'group' => 'contact', 'label' => 'Telepon'],
            ['key' => 'contact_whatsapp', 'value' => '6281234567890', 'type' => 'text', 'group' => 'contact', 'label' => 'WhatsApp'],
            ['key' => 'contact_address', 'value' => 'Desa Sanggabuana, Kec. Tegalwaru, Kab. Karawang, Jawa Barat 41381', 'type' => 'textarea', 'group' => 'contact', 'label' => 'Alamat'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/sanggabuana', 'type' => 'text', 'group' => 'social', 'label' => 'Instagram'],
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/sanggabuana', 'type' => 'text', 'group' => 'social', 'label' => 'Facebook'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/@sanggabuana', 'type' => 'text', 'group' => 'social', 'label' => 'YouTube'],
            ['key' => 'hero_title', 'value' => 'Jelajahi Keajaiban Alam Gunung Sanggabuana', 'type' => 'text', 'group' => 'hero', 'label' => 'Judul Hero'],
            ['key' => 'hero_subtitle', 'value' => 'Temukan keindahan tersembunyi di jantung Karawang. Pendakian menakjubkan, air terjun spektakuler, dan pengalaman alam yang tak terlupakan menanti Anda.', 'type' => 'textarea', 'group' => 'hero', 'label' => 'Sub Judul Hero'],
            ['key' => 'hero_background', 'value' => 'https://images.unsplash.com/photo-1542224566-6e85f2e6772f?q=80&w=2000&auto=format&fit=crop', 'type' => 'image', 'group' => 'hero', 'label' => 'Gambar Latar Hero (Background)'],
            ['key' => 'hero_image', 'value' => 'https://images.unsplash.com/photo-1542224566-6e85f2e6772f?q=80&w=800&auto=format&fit=crop', 'type' => 'image', 'group' => 'hero', 'label' => 'Gambar Samping Hero (Floating)'],
            ['key' => 'about_text', 'value' => 'Gunung Sanggabuana merupakan kawasan wisata alam yang terletak di Kabupaten Karawang, Jawa Barat. Dengan ketinggian 1.291 mdpl, kawasan ini menawarkan berbagai destinasi wisata alam mulai dari pendakian gunung, air terjun, hutan pinus, hingga sungai yang jernih. Dikelola dengan prinsip ekowisata, kami berkomitmen menjaga kelestarian alam sambil memberikan pengalaman wisata terbaik.', 'type' => 'textarea', 'group' => 'about', 'label' => 'Tentang Kami'],
            ['key' => 'open_hours', 'value' => 'Senin - Minggu: 06:00 - 17:00 WIB', 'type' => 'text', 'group' => 'general', 'label' => 'Jam Operasional'],
            
            // New setting keys from SettingController
            ['key' => 'about_video_type', 'value' => 'link', 'type' => 'select', 'group' => 'about', 'label' => 'Tipe Sumber Video'],
            ['key' => 'about_video_link', 'value' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'type' => 'text', 'group' => 'about', 'label' => 'Link Video (YouTube / Direct Link / Google Drive)'],
            ['key' => 'about_video_file', 'value' => '', 'type' => 'video', 'group' => 'about', 'label' => 'Unggah File Video'],
            [
                'key' => 'about_features', 
                'value' => json_encode([
                    ['title' => 'Aman & Nyaman', 'desc' => 'Jalur terkelola baik'],
                    ['title' => 'Spot Foto', 'desc' => 'Pemandangan indah']
                ]), 
                'type' => 'features', 
                'group' => 'about', 
                'label' => 'Daftar Fitur Tentang Kami (Dapat Ditambah/Dikurangi)'
            ],
            ['key' => 'weather_latitude', 'value' => '-6.505', 'type' => 'text', 'group' => 'cuaca', 'label' => 'Koordinat Lintang (Latitude) Cuaca'],
            ['key' => 'weather_longitude', 'value' => '107.218', 'type' => 'text', 'group' => 'cuaca', 'label' => 'Koordinat Bujur (Longitude) Cuaca'],
            ['key' => 'weather_mode', 'value' => 'auto', 'type' => 'select', 'group' => 'cuaca', 'label' => 'Mode Status Cuaca & Kondisi Jalur'],
            ['key' => 'weather_manual_status', 'value' => 'Jalur Ditutup', 'type' => 'text', 'group' => 'cuaca', 'label' => 'Status Manual / Darurat'],
            ['key' => 'weather_manual_desc', 'value' => 'Ditutup sementara untuk pemulihan ekosistem hutan', 'type' => 'text', 'group' => 'cuaca', 'label' => 'Deskripsi Kondisi Manual'],
            ['key' => 'weather_manual_icon', 'value' => 'alert-triangle', 'type' => 'select', 'group' => 'cuaca', 'label' => 'Ikon Status Manual'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // 4. Pages
        Page::updateOrCreate([
            'slug' => 'tentang-kami'
        ], [
            'title' => 'Tentang Kami',
            'content' => '<h2>Sejarah</h2><p>Gunung Sanggabuana telah menjadi tujuan wisata alam sejak lama. Kawasan ini dikelola secara profesional untuk menyediakan pengalaman alam terbaik bagi pengunjung dari seluruh Indonesia.</p><h2>Visi & Misi</h2><p>Menjadi destinasi wisata alam terdepan di Jawa Barat yang mengedepankan kelestarian alam dan pemberdayaan masyarakat lokal.</p>',
            'meta_description' => 'Pelajari tentang sejarah and visi misi Wisata Gunung Sanggabuana.',
            'is_active' => true,
        ]);

        Page::updateOrCreate([
            'slug' => 'syarat-ketentuan'
        ], [
            'title' => 'Syarat & Ketentuan',
            'content' => '<h2>Peraturan Pengunjung</h2><ul><li>Dilarang membuang sampah sembarangan</li><li>Dilarang merusak tanaman dan satwa</li><li>Wajib mengikuti jalur pendakian yang telah ditentukan</li><li>Anak di bawah 12 tahun harus didampingi orang dewasa</li></ul>',
            'meta_description' => 'Syarat dan ketentuan pengunjung Wisata Gunung Sanggabuana.',
            'is_active' => true,
        ]);

        // 5. Seed Visit Purposes
        $this->call(PurposeSeeder::class);
    }
}





