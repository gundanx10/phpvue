<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // model findOrFail 返回404
        \API::error(function (ModelNotFoundException $exception) {
            abort(404);
        });

        Blade::directive('jsonscript', function (...$data) {
            $params = explode(",", $data[0]);
            $arr = ["<script>"];
            foreach ($params as $item) {
                $arr[] = 'var ' . str_replace("$", "", $item) . ' = <?php echo json_encode(' . $item . ') ?>;';
            }
            $arr[] = '</script>';

            return join("\n", $arr);
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
    }
}
