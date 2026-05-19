<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Gallery;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\Page;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Destinations
        $destinations = [
            [
                'name' => 'Gunung Sanggabuana',
                'slug' => 'gunung-sanggabuana',
                'short_description' => 'Puncak keindahan alam Karawang dengan panorama hutan tropis yang memukau dan udara segar pegunungan.',
                'description' => '<p>Gunung Sanggabuana merupakan destinasi wisata alam yang terletak di Kabupaten Karawang, Jawa Barat. Dengan ketinggian sekitar 1.291 mdpl, gunung ini menawarkan pemandangan hutan tropis yang masih asri dan alami.</p><p>Jalur pendakian yang tersedia cocok untuk berbagai level pendaki, mulai dari pemula hingga yang berpengalaman. Di sepanjang jalur pendakian, Anda akan disuguhi keindahan flora dan fauna khas pegunungan Jawa Barat.</p><p>Puncak Gunung Sanggabuana menawarkan panorama 360 derajat yang memukau, dengan pemandangan laut Jawa di utara dan deretan pegunungan di selatan. Sangat cocok untuk menikmati sunrise dan sunset yang spektakuler.</p>',
                'location' => 'Karawang, Jawa Barat',
                'altitude' => '1.291 mdpl',
                'operational_days' => 'Senin - Minggu',
                'operational_hours' => '24 Jam',
                'price' => 25000,
                'duration' => '4-5 jam pendakian',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Curug Cigentis',
                'slug' => 'curug-cigentis',
                'short_description' => 'Air terjun megah setinggi 50 meter tersembunyi di jantung hutan Sanggabuana yang masih perawan.',
                'description' => '<p>Curug Cigentis adalah air terjun yang terletak di kawasan Gunung Sanggabuana. Air terjun ini memiliki ketinggian sekitar 50 meter dengan debit air yang cukup deras sepanjang tahun.</p><p>Untuk mencapai curug ini, pengunjung harus menempuh perjalanan trekking sekitar 2 jam melalui jalur hutan yang rindang. Perjalanan ini sendiri sudah merupakan pengalaman tersendiri dengan pemandangan alam yang indah.</p>',
                'location' => 'Karawang, Jawa Barat',
                'altitude' => '800 mdpl',
                'operational_days' => 'Senin - Minggu',
                'operational_hours' => '08:00 - 17:00',
                'price' => 15000,
                'duration' => '2-3 jam trekking',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Puncak Batu Kapur',
                'slug' => 'puncak-batu-kapur',
                'short_description' => 'Formasi batu kapur raksasa dengan spot foto instagramable dan pemandangan matahari terbenam terbaik.',
                'description' => '<p>Puncak Batu Kapur menawarkan formasi batuan unik yang terbentuk selama jutaan tahun. Lokasi ini menjadi salah satu spot favorit untuk fotografi dan menikmati sunset.</p><p>Terletak di ketinggian yang lebih rendah dibanding puncak utama Sanggabuana, membuat tempat ini lebih mudah dijangkau oleh pengunjung dengan berbagai tingkat kemampuan fisik.</p>',
                'location' => 'Karawang, Jawa Barat',
                'altitude' => '650 mdpl',
                'operational_days' => 'Senin - Minggu',
                'operational_hours' => '06:00 - 18:00',
                'price' => 10000,
                'duration' => '1-2 jam',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Hutan Pinus Sanggabuana',
                'slug' => 'hutan-pinus-sanggabuana',
                'short_description' => 'Area camping terbaik di bawah kanopi pinus raksasa dengan suasana sejuk dan tenang.',
                'description' => '<p>Hutan Pinus Sanggabuana merupakan area konservasi dengan ribuan pohon pinus yang menjulang tinggi. Area ini sangat populer untuk kegiatan camping dan piknik keluarga.</p><p>Udara yang sejuk dengan suhu rata-rata 18-22°C membuat tempat ini menjadi pelarian sempurna dari panasnya kota. Fasilitas camping ground yang memadai tersedia untuk kenyamanan pengunjung.</p>',
                'location' => 'Karawang, Jawa Barat',
                'altitude' => '900 mdpl',
                'operational_days' => 'Jumat - Minggu',
                'operational_hours' => '24 Jam',
                'price' => 20000,
                'duration' => 'Sehari penuh',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Sungai Cikadongdong',
                'slug' => 'sungai-cikadongdong',
                'short_description' => 'Sungai jernih dengan aliran tenang, sempurna untuk tubing dan berendam di alam terbuka.',
                'description' => '<p>Sungai Cikadongdong mengalir jernih di antara bebatuan dan pepohonan rindang. Sungai ini menjadi tempat favorit untuk aktivitas tubing, berenang, dan bermain air.</p><p>Dengan kedalaman yang bervariasi, sungai ini aman untuk dikunjungi bersama keluarga. Pemandu lokal tersedia untuk memastikan keamanan dan kenyamanan pengunjung.</p>',
                'location' => 'Karawang, Jawa Barat',
                'altitude' => '500 mdpl',
                'operational_days' => 'Selasa - Minggu',
                'operational_hours' => '07:00 - 16:00',
                'price' => 15000,
                'duration' => '2-3 jam',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Tebing Parang Gombong',
                'slug' => 'tebing-parang-gombong',
                'short_description' => 'Tebing batu vertikal untuk rock climbing dengan pemandangan lembah hijau yang menakjubkan.',
                'description' => '<p>Tebing Parang Gombong merupakan destinasi favorit para pecinta rock climbing. Tebing ini memiliki tingkat kesulitan yang bervariasi dari pemula hingga profesional.</p><p>Selain kegiatan panjat tebing, lokasi ini juga menawarkan pemandangan lembah hijau yang sangat indah. Jalur trekking menuju lokasi tebing juga tidak kalah menantang dan seru.</p>',
                'location' => 'Karawang, Jawa Barat',
                'altitude' => '750 mdpl',
                'operational_days' => 'Sabtu - Minggu',
                'operational_hours' => '08:00 - 17:00',
                'price' => 30000,
                'duration' => '3-4 jam',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($destinations as $dest) {
            Destination::updateOrCreate(['slug' => $dest['slug']], $dest);
        }

        // Testimonials
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

        // Settings
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
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // Pages
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

        // Seed Visitors Mock Data for Analytics & POS & Monitoring
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Visitor::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sanggabuana = Destination::where('slug', 'gunung-sanggabuana')->first();
        $cigentis = Destination::where('slug', 'curug-cigentis')->first();
        $batukapur = Destination::where('slug', 'puncak-batu-kapur')->first();

        $destinationsData = [];
        if ($sanggabuana) {
            $destinationsData[$sanggabuana->id] = ['name' => $sanggabuana->name, 'price' => $sanggabuana->price, 'slug' => $sanggabuana->slug];
        }
        if ($cigentis) {
            $destinationsData[$cigentis->id] = ['name' => $cigentis->name, 'price' => $cigentis->price, 'slug' => $cigentis->slug];
        }
        if ($batukapur) {
            $destinationsData[$batukapur->id] = ['name' => $batukapur->name, 'price' => $batukapur->price, 'slug' => $batukapur->slug];
        }

        // If none of those three exist, grab whatever destinations are in the database
        if (empty($destinationsData)) {
            $allDests = Destination::limit(3)->get();
            foreach ($allDests as $d) {
                $destinationsData[$d->id] = ['name' => $d->name, 'price' => $d->price, 'slug' => $d->slug];
            }
        }

        $sanggabuanaId = $sanggabuana ? $sanggabuana->id : 1;

        $names = [
            'Rian Hidayat', 'Siti Aminah', 'Aditya Pratama', 'Dewi Lestari', 'Budi Utomo',
            'Rizky Fauzi', 'Indah Permata', 'Agus Salim', 'Fitri Handayani', 'Hendra Wijaya',
            'Yusuf Mansur', 'Lani Marlina', 'Anwar Sadat', 'Novianti', 'Eko Prasetyo',
            'Taufik Hidayat', 'Rina Wulandari', 'Ahmad Dahlan', 'Slamet Riyadi', 'Kartini'
        ];

        $addresses = [
            'Karawang', 'Bekasi', 'Jakarta', 'Bandung', 'Purwakarta', 'Subang', 'Cianjur', 'Bogor'
        ];

        $communities = [
            'Sanggabuana Adventure', 'Karawang Trail Runners', 'KPA Tapak Rimba', 'Backpacker Jakarta',
            'Pecinta Alam Karawang', null, null, null, null, null
        ];

        $purposes = ['Hiking', 'Trail Run', 'Jiarah'];
        $paymentMethods = ['Tunai', 'QRIS', 'Transfer'];

        $startDate = Carbon::create(2026, 1, 1);
        $endDate = Carbon::now();
        $daysDiff = $startDate->diffInDays($endDate);

        for ($i = 0; $i < 180; $i++) {
            // Pick a random destination from the available ones
            $destId = array_rand($destinationsData);
            $destInfo = $destinationsData[$destId];

            // Pick a random date in the past 5 months
            $randomDayOffset = rand(0, $daysDiff);
            $checkedIn = (clone $startDate)->addDays($randomDayOffset)->setHour(rand(7, 16))->setMinute(rand(0, 59));
            
            // Randomize status and checked out time
            $isToday = $checkedIn->isToday();
            $status = 'out';
            $checkedOut = null;
            if ($isToday) {
                $status = rand(0, 1) === 0 ? 'in' : 'out';
            }
            if ($status === 'out') {
                $checkedOut = (clone $checkedIn)->addHours(rand(2, 8));
            }

            // Visitor demographics & group details
            $name = $names[array_rand($names)];
            
            // Randomize address type
            $typeRand = rand(0, 100);
            if ($typeRand < 15) {
                $addressType = 'lokal';
                $province = 'Jawa Barat';
                $city = rand(0, 1) === 0 ? 'Pangkalan' : 'Tegalwaru';
            } elseif ($typeRand < 90) {
                $addressType = 'indonesia';
                $indoLocations = [
                    ['province' => 'Jawa Barat', 'cities' => ['Karawang', 'Bekasi', 'Bandung', 'Purwakarta', 'Subang', 'Cianjur', 'Bogor']],
                    ['province' => 'DKI Jakarta', 'cities' => ['Jakarta Timur', 'Jakarta Selatan', 'Jakarta Barat', 'Jakarta Pusat']],
                    ['province' => 'Banten', 'cities' => ['Serang', 'Tangerang', 'Cilegon']],
                    ['province' => 'Jawa Tengah', 'cities' => ['Semarang', 'Surakarta', 'Tegal']]
                ];
                $loc = $indoLocations[array_rand($indoLocations)];
                $province = $loc['province'];
                $city = $loc['cities'][array_rand($loc['cities'])];
            } else {
                $addressType = 'mancanegara';
                $worldLocations = [
                    ['country' => 'Germany', 'cities' => ['Berlin', 'Munich', 'Frankfurt']],
                    ['country' => 'Australia', 'cities' => ['Sydney', 'Melbourne', 'Brisbane']],
                    ['country' => 'United States', 'cities' => ['Los Angeles', 'New York', 'San Francisco']],
                    ['country' => 'Singapore', 'cities' => ['Singapore']]
                ];
                $loc = $worldLocations[array_rand($worldLocations)];
                $province = $loc['country'];
                $city = $loc['cities'][array_rand($loc['cities'])];
            }

            $address = $city . ', ' . $province;
            $community = ($destId === $sanggabuanaId) ? $communities[array_rand($communities)] : null;
            $purpose = ($destId === $sanggabuanaId) ? $purposes[array_rand($purposes)] : 'Normal';
            $campingDuration = ($purpose === 'Hiking') ? rand(1, 3) : null;

            // Generate demographics quantities
            $leaderGender = rand(0, 1) === 0 ? 'L' : 'P';
            $qtyMale = rand(0, 5);
            $qtyFemale = rand(0, 4);
            $qtyKids = rand(0, 2);
            
            if ($leaderGender === 'L') {
                $qtyMale += 1;
            } else {
                $qtyFemale += 1;
            }
            $qtyTotal = $qtyMale + $qtyFemale + $qtyKids;
            
            $avgAge = rand(18, 45);
            $price = $destInfo['price'];
            
            // Group discount for larger groups
            if ($qtyTotal > 5) {
                $price = max($price - 5000, 5000);
            }
            $totalPrice = $price * $qtyTotal;
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

            // Generate unique ticket number
            $ticketNo = 'TKT-' . $checkedIn->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            Visitor::create([
                'destination_id' => $destId,
                'ticket_no' => $ticketNo,
                'name' => $name,
                'address' => $address,
                'address_type' => $addressType,
                'city' => $city,
                'province' => $province,
                'community' => $community,
                'purpose' => $purpose,
                'camping_duration' => $campingDuration,
                'qty_male' => $qtyMale,
                'qty_female' => $qtyFemale,
                'qty_kids' => $qtyKids,
                'qty_total' => $qtyTotal,
                'avg_age' => $avgAge,
                'price' => $price,
                'total_price' => $totalPrice,
                'payment_method' => $paymentMethod,
                'status' => $status,
                'checked_in_at' => $checkedIn,
                'checked_out_at' => $checkedOut,
                'created_at' => $checkedIn,
                'updated_at' => $checkedIn
            ]);
        }
    }
}
