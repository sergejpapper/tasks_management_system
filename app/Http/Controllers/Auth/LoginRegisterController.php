<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout', 'tasks'
        ]);
    }

    /**
     * Display a registration form.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function register(): \Illuminate\Foundation\Application|View|Factory|Application
    {
        return view('auth.register');
    }

    /**
     * Store a new user.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|max:200|unique:users',
            'password' => 'required|min:8|confirmed'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
        $request->session()->regenerate();

        return redirect()->route('tasks')
            ->withSuccess('You have successfully registered & logged in');
    }

    /**
     * Display a login form.
     *
     * @return Application|Factory|\Illuminate\Foundation\Application|View
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Authenticate the user.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('tasks')
                ->withSuccess('You have successfully logged in');
        }

        return back()->withErrors([
            'email' => 'Your provided credentials do not match in our records',
        ])->onlyInput('email');

    }

    /**
     * Display a tasks list to authenticated users.
     *
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse
     */
    public function tasks(): Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse
    {
        if (Auth::check()) {
            return view('auth.tasks');
        } else {
            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Please login to access the tasks list',
                ])->onlyInput('email');
        }
    }

    /**
     * Log out the user from application.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->withSuccess('You have logged out successfully');
    }
}
