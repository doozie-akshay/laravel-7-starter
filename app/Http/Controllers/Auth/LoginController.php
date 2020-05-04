<?php

namespace App\Http\Controllers\Auth;

use App\Role;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectTo()
    {
        if (Auth::user()->hasRole('admin')) {
            $this->redirectTo = route('admin.users.index');
            return $this->redirectTo;
        }
        $this->redirectTo = RouteServiceProvider::HOME;
        return $this->redirectTo;

    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $fetchUser = Socialite::driver('google')->user();
        if (isset($fetchUser->email)) {
            $user = User::where('email', $fetchUser->email)->first();
            if ($user) {
                //login the user
                Auth::loginUsingId($user->id);
            } else {
                //register and login the user
                $user = User::create([
                    'name' => $fetchUser->name,
                    'mobile' => '',
                    'email' => $fetchUser->email,
                    'email_verified_at' => Carbon::now(),
                    'password' => Hash::make(Str::random(8)),
                ]);
                $role = Role::select('id')->where('name', 'user')->first();
                $user->roles()->attach($role);
                Auth::loginUsingId($user->id);
            }
        } else {
            return redirect(route('login'));
        }
        return redirect()->route('home');
    }
}
