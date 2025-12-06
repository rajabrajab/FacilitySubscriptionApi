<?php

/*
*   Developer : Abd alwahed rajab
*/

// Run
// php artisan make:provider ResponseServiceProvider
namespace App\Providers;

use App\Http\Helpers\HttpCodes;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\ResponseFactory;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {

        $factory->macro('sendResponse', function ($data = false, $message = '') use ($factory) {

            $format = [
                'state' => true,
                'statusCode' => HttpCodes::OK,
                'message' => $message,
            ];

            $format['data'] = $data ?? [];

            return $factory->make($format);
        });


        $factory->macro('sendError', function ($code, $message = '', $data = []) use ($factory) {

            $false = [
                'state' => false,
                'statusCode' => $code,
                'message' => $message,
            ];

            if ($data) {
                $false['errors'] = $data;
            }

            return $factory->make($false,$code);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
