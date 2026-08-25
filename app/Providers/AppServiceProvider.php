<?php

namespace App\Providers;

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

    }

    /**
     * Bootstrap any application services.
     */

     public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Rate limiters for the API.
     *
     * 'api'  - blanket limit applied to every API route (see bootstrap/app.php).
     *          Keyed per authenticated user, falling back to IP for guests.
     * 'auth' - tight limit for unauthenticated credential/OTP endpoints, keyed
     *          on IP + submitted email so one attacker cannot lock out a user
     *          and one IP cannot spray many accounts.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            $email = (string) $request->input('email');

            return [
                Limit::perMinute(5)->by($request->ip().'|'.$email),
                Limit::perMinute(20)->by($request->ip()),
            ];
        });
    }
}
