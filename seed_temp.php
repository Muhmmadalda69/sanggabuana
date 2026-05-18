<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

\App\Models\Setting::firstOrCreate(
    ['key' => 'hero_background'],
    [
        'value' => 'https://images.unsplash.com/photo-1542224566-6e85f2e6772f?q=80&w=2000&auto=format&fit=crop',
        'type' => 'image',
        'group' => 'hero',
        'label' => 'Gambar Latar Hero (Background)'
    ]
);

\App\Models\Setting::firstOrCreate(
    ['key' => 'hero_image'],
    [
        'value' => 'https://images.unsplash.com/photo-1542224566-6e85f2e6772f?q=80&w=800&auto=format&fit=crop',
        'type' => 'image',
        'group' => 'hero',
        'label' => 'Gambar Samping Hero (Floating)'
    ]
);

echo "Settings successfully seeded!\n";
