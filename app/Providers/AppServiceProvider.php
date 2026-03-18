<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Category;
use Illuminate\Support\Facades\View;

use App\Events\PostCreated;
use App\Listeners\SendPostNotification;

use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    // Register events here instead of in boot()
    protected $listen = [
        PostCreated::class => [
            SendPostNotification::class,
        ],
    ];
    
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
        // Share categories to ALL views automatically
        //View::share('categories', Category::all());

        // Connect event to listener
        /* Event::listen(
            PostCreated::class,
            SendPostNotification::class
        ); */

        // Wrap in try-catch to avoid errors during migration
        try {
            View::share('categories', Category::all());
        } catch (\Exception $e) {
            View::share('categories', collect());
        }

    }
}
