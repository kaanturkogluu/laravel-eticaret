<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /**
     * Register formunu göster
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Register işlemi
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'is_admin' => false,
        ]);

        // E-posta doğrulama e-postası gönder
        $user->sendEmailVerificationNotification();

        // Hoş geldin maili gönder
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            \Log::error('Welcome mail failed: ' . $e->getMessage());
        }

        return redirect()->route('verification.notice')->with('success', 'Kayıt başarılı! E-posta adresinizi doğrulamak için gönderilen e-postadaki bağlantıya tıklayın.');
    }
}
