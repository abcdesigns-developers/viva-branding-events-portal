<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRouteBindings(app_path('Models'), 'App\\Models\\');
    }

    /**
     * Bind each model's snake_case name (e.g. {role}) so URLs can use the
     * Hashids route key from App\Traits\Hashidable instead of the raw id.
     */
    private function registerRouteBindings(string $path, string $namespace): void
    {
        if (! is_dir($path)) {
            return;
        }

        foreach (scandir($path) as $file) {
            if (str_starts_with($file, '.')) {
                continue;
            }

            if (is_dir($path.DIRECTORY_SEPARATOR.$file)) {
                $this->registerRouteBindings($path.DIRECTORY_SEPARATOR.$file, $namespace.$file.'\\');

                continue;
            }

            if (! Str::endsWith($file, '.php')) {
                continue;
            }

            $class = $namespace.basename($file, '.php');

            Route::bind(Str::snake(basename($file, '.php')), fn ($value) => $this->findModel($class, $value));
        }
    }

    private function findModel(string $class, string $routeKey)
    {
        if (! is_numeric($routeKey)) {
            $routeKey = Hashids::connection($class)->decode($routeKey)[0] ?? null;
        }

        $model = resolve($class);

        try {
            return $model->withTrashed()->findOrFail($routeKey);
        } catch (\BadMethodCallException $e) {
            return $model->findOrFail($routeKey);
        }
    }
}
