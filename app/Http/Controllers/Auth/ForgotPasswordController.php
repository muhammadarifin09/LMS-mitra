<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\M_User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

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

        try {
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

            // Kirim email
            $this->sendPasswordResetEmail($request->username, $token);

            return back()->with('status', 'Link reset password telah dikirim ke email Anda!');

        } catch (\Exception $e) {
            return back()->withErrors(['username' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    // Method untuk kirim email
    private function sendPasswordResetEmail($username, $token)
    {
        $resetUrl = route('password.reset', $token);
        
        $data = [
            'username' => $username,
            'resetUrl' => $resetUrl,
            'expires' => 60 // menit
        ];

        Mail::send('emails.password-reset', $data, function ($message) use ($username) {
            $message->to($username)
                    ->subject('Reset Password - LMS MOCC Mitra BPS Tanah Laut');
        });
    }

    // Show reset password form
    public function showResetForm(Request $request, $token = null)
    {
        // Cek token validity
        $tokenData = DB::table('password_reset_tokens')
                        ->where('token', $token)
                        ->first();

        if (!$tokenData) {
            return redirect()->route('password.request')
                            ->withErrors(['username' => 'Token tidak valid atau sudah kadaluarsa.']);
        }

        // Cek token expiry (1 jam)
        $tokenAge = Carbon::parse($tokenData->created_at)->diffInMinutes(Carbon::now());
        if ($tokenAge > 60) {
            DB::table('password_reset_tokens')->where('token', $token)->delete();
            return redirect()->route('password.request')
                            ->withErrors(['username' => 'Token sudah kadaluarsa.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'username' => $tokenData->username
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

        try {
            // Cek token validity
            $tokenData = DB::table('password_reset_tokens')
                            ->where('username', $request->username)
                            ->where('token', $request->token)
                            ->first();

            if (!$tokenData) {
                return back()->withErrors(['username' => 'Token tidak valid.']);
            }

            // Cek token expiry (1 jam)
            $tokenAge = Carbon::parse($tokenData->created_at)->diffInMinutes(Carbon::now());
            if ($tokenAge > 60) {
                DB::table('password_reset_tokens')->where('username', $request->username)->delete();
                return back()->withErrors(['username' => 'Token sudah kadaluarsa.']);
            }

            // Update password
            $user = M_User::where('username', $request->username)->first();
            if ($user) {
                $user->password = Hash::make($request->password);
                $user->save();

                // Hapus token
                DB::table('password_reset_tokens')->where('username', $request->username)->delete();

                return redirect()->route('login.page')->with('status', 'Password berhasil direset! Silakan login.');
            }

            return back()->withErrors(['username' => 'User tidak ditemukan.']);

        } catch (\Exception $e) {
            return back()->withErrors(['username' => 'Terjadi kesalahan sistem. Silakan coba lagi.']);
        }
    }
}