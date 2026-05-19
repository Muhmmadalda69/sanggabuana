<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class SyncRegionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:regions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download and sync regional data (Provinces, Cities, Countries) to local JSON files for offline access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting regional data sync...');
        $publicPath = public_path('data/wilayah');
        
        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }
        
        // 1. Fetch Countries
        $this->info('Fetching world countries...');
        try {
            $countriesResp = Http::timeout(30)->withoutVerifying()->get('https://restcountries.com/v3.1/all?fields=name,translations');
            if ($countriesResp->successful()) {
                $countriesData = $countriesResp->json();
                $formattedCountries = [];
                foreach ($countriesData as $c) {
                    $name = $c['translations']['ind']['common'] ?? $c['name']['common'];
                    $formattedCountries[] = [
                        'name' => $name
                    ];
                }
                
                // Sort alphabetically
                usort($formattedCountries, function($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });

                File::put($publicPath . '/countries.json', json_encode($formattedCountries, JSON_PRETTY_PRINT));
                $this->info('✓ Countries synced successfully.');
            }
        } catch (\Exception $e) {
            $this->error('Failed to fetch countries: ' . $e->getMessage());
        }

        // 2. Fetch Provinces (Indonesia)
        $this->info('Fetching Indonesian provinces...');
        $provincesPath = $publicPath . '/indonesia';
        if (!File::exists($provincesPath)) {
            File::makeDirectory($provincesPath, 0755, true);
        }
        
        $regenciesPath = $provincesPath . '/regencies';
        if (!File::exists($regenciesPath)) {
            File::makeDirectory($regenciesPath, 0755, true);
        }

        try {
            // Using a very stable API for JSON data
            $provResp = Http::timeout(30)->withoutVerifying()->get('https://ibnux.github.io/data-indonesia/provinsi.json');
            
            if ($provResp->successful()) {
                $provinces = $provResp->json();
                
                // Transform to match our id and name format
                $formattedProvinces = [];
                foreach ($provinces as $p) {
                    $formattedProvinces[] = [
                        'id' => $p['id'],
                        'name' => $p['nama']
                    ];
                }
                
                File::put($provincesPath . '/provinces.json', json_encode($formattedProvinces, JSON_PRETTY_PRINT));
                $this->info('✓ Provinces synced successfully. Total: ' . count($formattedProvinces));

                // 3. Fetch Cities for each Province
                $this->info('Fetching Indonesian cities/regencies...');
                $bar = $this->output->createProgressBar(count($formattedProvinces));
                $bar->start();

                foreach ($formattedProvinces as $prov) {
                    $cityResp = Http::timeout(15)->withoutVerifying()->get('https://ibnux.github.io/data-indonesia/kabupaten/' . $prov['id'] . '.json');
                    if ($cityResp->successful()) {
                        $cities = $cityResp->json();
                        $formattedCities = [];
                        foreach ($cities as $c) {
                            $formattedCities[] = [
                                'id' => $c['id'],
                                'name' => $c['nama']
                            ];
                        }
                        File::put($regenciesPath . '/' . $prov['id'] . '.json', json_encode($formattedCities, JSON_PRETTY_PRINT));
                    }
                    $bar->advance();
                    // Brief pause to avoid rate limiting
                    usleep(100000); 
                }
                $bar->finish();
                $this->newLine();
                $this->info('✓ All cities synced successfully.');
            }
        } catch (\Exception $e) {
            $this->error('Failed to fetch Indonesian regions: ' . $e->getMessage());
        }

        $this->info('Sync completed successfully! Local data is ready to use.');
    }
}
