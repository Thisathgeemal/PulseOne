<?php
namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
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

    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $userId           = Auth::id();
                $currentSessionId = Session::getId();

                $sessions = DB::table('sessions')
                    ->where('user_id', $userId)
                    ->get()
                    ->map(function ($session) use ($currentSessionId) {
                        return [
                            'id'            => $session->id,
                            'ip_address'    => $session->ip_address,
                            'user_agent'    => $session->user_agent,
                            'device'        => $this->parseUserAgent($session->user_agent),
                            'last_activity' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                            'is_current'    => $session->id === $currentSessionId,
                        ];
                    });
            } else {
                $sessions = collect();
            }
            $view->with('sessions', $sessions);
        });
    }

    // Get Divise and Browser
    public function parseUserAgent($userAgent)
    {
        $browser  = 'Unknown Browser';
        $platform = 'Unknown OS';

        if (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
            $browser = 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
            $browser = 'Internet Explorer';
        }

        if (strpos($userAgent, 'Windows NT') !== false) {
            $platform = 'Windows';
        } elseif (strpos($userAgent, 'Macintosh') !== false) {
            $platform = 'macOS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $platform = 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            $platform = 'Android';
        } elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            $platform = 'iOS';
        }

        return "$platform on $browser";
    }
}
