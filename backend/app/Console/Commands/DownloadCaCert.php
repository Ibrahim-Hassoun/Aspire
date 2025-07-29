<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadCaCert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssl:download-cacert {--force : Force download even if file exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download CA certificate bundle to fix SSL issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $certPath = storage_path('app/cacert.pem');
        
        if (file_exists($certPath) && !$this->option('force')) {
            $this->info('CA certificate bundle already exists at: ' . $certPath);
            $this->info('Use --force to download again.');
            return;
        }

        $this->info('Downloading CA certificate bundle...');

        try {
            // Download from curl.se (official source)
            $response = Http::withOptions([
                'verify' => false, // We need to disable SSL to download the cert that fixes SSL
                'timeout' => 60
            ])->get('https://curl.se/ca/cacert.pem');

            if ($response->successful()) {
                file_put_contents($certPath, $response->body());
                $this->info('✅ CA certificate bundle downloaded successfully!');
                
                // Update php.ini suggestion
                $this->info('');
                $this->info('To permanently fix SSL issues, add this to your php.ini:');
                $this->warn('curl.cainfo = "' . $certPath . '"');
                $this->info('');
                $this->info('Or add this to your .env file:');
                $this->warn('CURL_CA_BUNDLE=' . $certPath);
                
                return Command::SUCCESS;
            } else {
                $this->error('Failed to download CA certificate bundle');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Error downloading CA certificate: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
