<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class HttpServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure HTTP client defaults
        Http::macro('withSslDisabled', function () {
            return Http::withOptions([
                'verify' => false,
                'timeout' => config('http.ssl.timeout', 30),
                'connect_timeout' => config('http.ssl.connect_timeout', 10),
            ]);
        });

        // Set global cURL defaults for SSL issues
        if (!config('http.ssl.verify_peer', false)) {
            ini_set('curl.cainfo', '');
            
            // Set default cURL options
            $this->setGlobalCurlDefaults();
        }
    }

    /**
     * Set global cURL defaults to handle SSL issues
     */
    private function setGlobalCurlDefaults(): void
    {
        // These settings will apply to all cURL requests
        if (function_exists('curl_setopt_array')) {
            $defaults = [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => config('http.ssl.timeout', 30),
                CURLOPT_CONNECTTIMEOUT => config('http.ssl.connect_timeout', 10),
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_USERAGENT => 'Inventory-Management-System/1.0',
            ];

            // Store defaults for later use
            app()->singleton('curl.defaults', function () use ($defaults) {
                return $defaults;
            });
        }
    }
}
