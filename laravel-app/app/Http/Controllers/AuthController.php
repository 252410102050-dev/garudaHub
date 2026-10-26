<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private array $pesan = [
        'required' => ':attribute ojo dikosongno talah, diisi sek.',
        'email' => 'Format email-mu aneh lur, sing bener o.',
        'email.unique' => 'Email iki wes dipakai uwong, golek sing liyane o.',
        'password.min' => 'Password kependekan lur, minimal 8 karakter.',
        'password.confirmed' => 'Password karo ulangane bedo, sing konsisten o.',
        'role.in' => 'Role-e sing bener o, iku gak onok pilihane.',
    ];

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:klien,developer'],
        ], $this->pesan);

        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/')->with('status', 'Kon wes daftar le, welcum welcum!');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], $this->pesan);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email utawa passwordmu salah woiii, iso-isone salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect('/')->with('status', 'Wes login yo, ojo lali ngopi sek.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('status', 'Wes metu yo, ati-ati nang dalan.');
    }
}
