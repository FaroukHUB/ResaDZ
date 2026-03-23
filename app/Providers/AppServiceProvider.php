<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\ChauffeurVehicle;
use App\Models\Loueur;
use App\Models\Review;
use App\Models\TransferBooking;
use App\Models\TransferRoute;
use App\Models\Vehicle;
use App\Models\VehicleOffer;
use App\Observers\ChatbotCacheObserver;
use App\Observers\AdminNotifyObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        $this->configureRateLimiting();

        // Notify admin on new registrations, bookings, and reviews
        Loueur::observe(AdminNotifyObserver::class);
        Vehicle::observe(AdminNotifyObserver::class);
        Booking::observe(AdminNotifyObserver::class);
        TransferBooking::observe(AdminNotifyObserver::class);
        Review::observe(AdminNotifyObserver::class);

        // Auto-clear chatbot cache when data changes
        Vehicle::observe(ChatbotCacheObserver::class);
        Loueur::observe(ChatbotCacheObserver::class);
        TransferRoute::observe(ChatbotCacheObserver::class);
        ChauffeurVehicle::observe(ChatbotCacheObserver::class);
        VehicleOffer::observe(ChatbotCacheObserver::class);
        Review::observe(ChatbotCacheObserver::class);
    }

    /**
     * Configure rate limiting for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Default API rate limit: 60 requests per minute
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Strict rate limit for sensitive operations: 10 requests per minute
        RateLimiter::for('sensitive', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        // Newsletter/Marketing: 5 requests per minute to prevent spam
        RateLimiter::for('marketing', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Booking/Reservation creation: 10 requests per minute
        RateLimiter::for('booking', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Tracking endpoints: Higher limit (120/min) for analytics
        RateLimiter::for('tracking', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        // Public read endpoints: More generous limit
        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip());
        });
    }
}
