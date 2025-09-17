<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return redirect()->route('login')->with('error', 'Nepodporovaný poskytovatel');
        }
        
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return redirect()->route('login')->with('error', 'Nepodporovaný poskytovatel');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
            
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                $user->update([
                    $provider . '_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            } else {
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'avatar' => $socialUser->getAvatar(),
                    $provider . '_id' => $socialUser->getId(),
                    'email_verified_at' => now(),
                ]);
            }

            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Úspěšně jste se přihlásili přes ' . ucfirst($provider));

        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Nastala chyba při přihlašování přes ' . ucfirst($provider));
        }
    }
}
