<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\M_User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ForgotPasswordController extends Controller
{
    // Show forgot password form
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    // Send password reset link
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['username' => 'required']);

        // Cek apakah username ada
        $user = M_User::where('username', $request->username)->first();

        if (!$user) {
            return back()->withErrors(['username' => 'Username tidak ditemukan.']);
        }

        // Generate token
        $token = Str::random(64);

        // Simpan token ke database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['username' => $request->username],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // TODO: Di sini nanti tambahkan logika kirim email
        // Untuk sekarang, kita redirect langsung ke reset form dengan token
        return redirect()->route('password.reset', ['token' => $token, 'username' => $request->username])
                        ->with('status', 'Silakan reset password Anda.');
    }

    // Show reset password form
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'username' => $request->username
        ]);
    }

    // Reset password
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'username' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        // Cek token validity
        $tokenData = DB::table('password_reset_tokens')
                        ->where('username', $request->username)
                        ->where('token', $request->token)
                        ->first();

        if (!$tokenData) {
            return back()->withErrors(['username' => 'Token tidak valid.']);
        }

        // Cek token expiry (1 jam)
        $tokenAge = Carbon::parse($tokenData->created_at)->diffInHours(Carbon::now());
        if ($tokenAge > 1) {
            DB::table('password_reset_tokens')->where('username', $request->username)->delete();
            return back()->withErrors(['username' => 'Token sudah kadaluarsa.']);
        }

        // Update password
        $user = M_User::where('username', $request->username)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token
        DB::table('password_reset_tokens')->where('username', $request->username)->delete();

        return redirect()->route('login.page')->with('status', 'Password berhasil direset! Silakan login.');
    }
}