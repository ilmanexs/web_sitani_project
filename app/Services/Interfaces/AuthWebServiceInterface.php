<?php

namespace App\Services\Interfaces;

use App\Models\User;
use Illuminate\Http\Request;

interface AuthWebServiceInterface
{
    public function login(array $credentials, Request $request): User;

    public function setupPassword(User $user, string $newPassword): bool;

    public function logout(Request $request): void;

    public function sendForgotPasswordOtp(string $email): void;

    public function verifyForgotPasswordOtp(string $email, string $otpCode): bool;

    public function performPasswordReset(string $email, string $newPassword): bool;
}
