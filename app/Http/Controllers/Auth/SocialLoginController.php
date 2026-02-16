<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialLoginService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialLoginController extends Controller
{
    public function __construct(
        protected SocialLoginService $socialLoginService
    ) {}

    /**
     * Redirect to social provider
     */
    public function redirect(string $provider): RedirectResponse
    {
        if (!$this->socialLoginService->isProviderEnabled($provider)) {
            return redirect()->route('login')
                ->with('error', 'Social login is not available for this provider.');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle callback from social provider
     */
    public function callback(string $provider): RedirectResponse
    {
        if (!$this->socialLoginService->isProviderEnabled($provider)) {
            return redirect()->route('login')
                ->with('error', 'Social login is not available for this provider.');
        }

        try {
            $socialiteUser = Socialite::driver($provider)->user();
            $user = $this->socialLoginService->handleSocialLogin($provider, $socialiteUser);
            $this->socialLoginService->login($user);

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', "Welcome back, {$user->name}!");

        } catch (Exception $e) {
            report($e);

            return redirect()->route('login')
                ->with('error', "Unable to login with " . ucfirst($provider) . ". Please try again.");
        }
    }

    /**
     * Unlink social account
     */
    public function unlink(Request $request, string $provider): RedirectResponse
    {
        $user = $request->user();

        if ($this->socialLoginService->unlinkAccount($user, $provider)) {
            return back()->with('success', ucfirst($provider) . " account has been unlinked.");
        }

        return back()->with('error', 'Cannot unlink this account. You need at least one login method.');
    }
}
