<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\OtpCodeRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;
use App\Services\Interfaces\UserServiceInterface;

class AuthWebController extends Controller
{
    protected UserServiceInterface $service;

    public function __construct(UserServiceInterface $service)
    {
        $this->service = $service;
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $user = $this->service->findUser(['email' => $validated['email']]);
            if ($user && !$user->can('akses-panel.Akses ke Panel Admin')) {
                return back()->withErrors(['email' => 'Email tidak punya akses ke panel.'])->withInput();
            }
        } catch (ResourceNotFoundException $e) {
            return back()->withErrors(['email' => 'Email tidak terdaftar.'])->withInput();
        } catch (DataAccessException $e) {
            return back()->with('error', 'Terjadi kesalahan saat mencari pengguna. Silakan coba lagi.')->withInput();
        } catch (Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.')->withInput();
        }

        if (!Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            return back()->withErrors(['password' => 'Password yang anda masukkan salah.'])->withInput();
        }

        $request->session()->regenerate();
        if (!Auth::user()->is_password_set) {
            return redirect()->route('setup-password');
        }

        return redirect()->route('dashboard.admin');
    }

    public function setupPassword(PasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = Auth::user();
        try {
            $this->service->resetPassword($user, $validated['password']);
            return redirect()->route('dashboard.admin');
        } catch (DataAccessException $e) {
            return back()->with('error', 'Gagal memperbarui password. Silakan coba lagi.')->withInput();
        } catch (Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan tak terduga. Silakan coba lagi.')->withInput();
        }
    }

    public function sendForgotPasswordEmail(EmailRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $user = $this->service->findUser(['email' => $validated['email']]);

            if (!$user || !$user->can('akses-panel.Akses ke Panel Admin')) {
                return back()->withErrors(['email' => 'Email tidak punya akses ke panel.'])->withInput();
            }

            $this->service->sendOtpToEmail($user);

            session(['otp_user_id' => $user->id]);
            return redirect()->route('verifikasi-otp')->with('success', 'Kode OTP berhasil dikirim ke email anda.');

        } catch (ResourceNotFoundException $e) {
            return back()->withErrors(['email' => 'Email tidak terdaftar.'])->withInput();
        } catch (DataAccessException $e) {
            return back()->with('error', 'Gagal mengirim OTP. Silakan coba lagi.');
        } catch (Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan di server.');
        }
    }

    public function showOtpVerificationForm(): View
    {
        return view('pages.auth.otp-verification');
    }

    public function showResetPasswordForm(): View
    {
        return view('pages.auth.reset-password');
    }

    public function verifyOtp(OtpCodeRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $otpUserId = session('otp_user_id');

        try {
            $user = $this->service->findUser(['id' => $otpUserId]);
        } catch (ResourceNotFoundException $e) {
            return redirect()->route('verifikasi-email')->with('error', 'User tidak ditemukan.')->withInput();
        } catch (DataAccessException $e) {
            return redirect()->route('verifikasi-email')->with('error', 'Terjadi kesalahan saat memuat data pengguna. Silakan coba lagi.');
        } catch (Throwable $e) {
            return redirect()->route('verifikasi-email')->with('error', 'Terjadi kesalahan diserver. Silakan coba lagi.');
        }

        if ($user) {
            try {
                $this->service->verifyOtp($user, $validated);
                return redirect()->route('reset-password');
            } catch (\Exception $e) {
                return back()->withErrors(['otp' => $e->getMessage()])->withInput();
            } catch (Throwable $e) {
                return back()->with('error', 'Terjadi kesalahan di server.')->withInput();
            }
        }
        return back()->with('error', 'Terjadi kesalahan di server.')->withInput();
    }

    public function resendOtp(): RedirectResponse
    {
        $otpUserId = session('otp_user_id');

        try {
            $user = $this->service->findUser(['id' => $otpUserId]);
        } catch (ResourceNotFoundException $e) {
            return redirect()->route('verifikasi-email')->with('error', 'Kesalahan sesi pengguna. Silakan mulai ulang proses.');
        } catch (DataAccessException $e) {
            return redirect()->route('verifikasi-email')->with('error', 'Terjadi kesalahan saat memuat data pengguna. Silakan coba lagi.');
        } catch (Throwable $e) {
            return redirect()->route('verifikasi-email')->with('error', 'Terjadi kesalahan di server.');
        }

        if ($user) {
            try {
                $this->service->sendOtpToEmail($user);
                return back()->with('success', 'Kode OTP baru berhasil dikirim!');
            } catch (DataAccessException $e) {
                return back()->with('error', 'Gagal mengirim ulang OTP. Terjadi kesalahan di server.');
            } catch (Throwable $e) {
                return back()->with('error', 'Terjadi kesalahan di server.');
            }
        }
        return back()->with('error', 'User tidak ditemukan.');
    }

    public function performPasswordReset(PasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $otpUserId = session('otp_user_id');

        try {
            $user = $this->service->findUser(['id' => $otpUserId]);
        } catch (ResourceNotFoundException $e) {
            return redirect()->route('verifikasi-email')->with('error', 'Kesalahan sesi pengguna. Silakan mulai ulang proses.');
        } catch (DataAccessException $e) {
        } catch (Throwable $e) {
            return redirect()->route('verifikasi-email')->with('error', 'Terjadi kesalahan tak terduga saat memuat data pengguna. Silakan coba lagi.');
        }

        if ($user) {
            try {
                $this->service->invalidateOtps($user);
                $this->service->resetPassword($user, $validated['password']);
                session()->forget('otp_user_id');
                return redirect()->route('login')->with('success', 'Password berhasil direset! Silakan login.');
            } catch (DataAccessException $e) {
                return back()->with('error', 'Gagal memperbarui password.')->withInput();
            } catch (Throwable $e) {
                return back()->with('error', 'Gagal memperbarui password. Terjadi kesalahan di server.')->withInput();
            }
        }
        return back()->with('error', 'Terjadi kesalahan di server.')->withInput();
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
