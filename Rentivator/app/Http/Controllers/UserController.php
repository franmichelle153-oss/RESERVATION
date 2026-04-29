<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('login');
    }

    // Show register form
    public function showRegister()
    {
        return view('register');
    }

    // Handle registration
    public function register(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users',
            'password'     => 'required|min:6|confirmed',
            'phone_number' => 'nullable|string',
            'address'      => 'nullable|string',
            'role'         => 'nullable|in:admin,tenant,owner',
        ]);

        $user = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'phone_number'    => $request->phone_number,
            'address'         => $request->address,
            'role'            => $request->role ?? 'tenant',
            'profile_picture' => null,
        ]);

        return redirect('/login')->with('success', 'Account successfully created!');
    }

    // Handle login
   // Handle login
public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        $request->session()->regenerate();

        if (Auth::user()->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        // ✅ Tenant — diretso vehicle page
        return redirect('/tenant/vehicle');
    }

    return back()->withErrors([
        'email' => 'Invalid email or password.',
    ]);
}

    // Handle logout
   public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
}
}