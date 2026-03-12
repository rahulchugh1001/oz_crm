<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        DB::table('user_login_activities')->insert([
            'user_id' => Auth::id(),
            'session_id' => $request->session()->getId(),
            'login_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Redirect all users to dashboard after login.
        $defaultRoute = route('admin.dashboard', absolute: false);

        return redirect()->intended($defaultRoute);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();
        $sessionId = $request->session()->getId();

        if ($userId) {
            $activityQuery = DB::table('user_login_activities')
                ->where('user_id', $userId)
                ->whereNull('logout_at');

            if (!empty($sessionId)) {
                $activityQuery->where('session_id', $sessionId);
            }

            $updatedCount = $activityQuery
                ->orderByDesc('id')
                ->limit(1)
                ->update([
                    'logout_at' => now(),
                    'updated_at' => now(),
                ]);

            if ($updatedCount === 0) {
                DB::table('user_login_activities')
                    ->where('user_id', $userId)
                    ->whereNull('logout_at')
                    ->orderByDesc('id')
                    ->limit(1)
                    ->update([
                        'logout_at' => now(),
                        'updated_at' => now(),
                    ]);
            }
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
