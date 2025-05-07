<?php

namespace App\Services\Interfaces;

use App\Models\User;

interface UserServiceInterface
{
    /**
     * Mencari data user berdasarkan kondisi tertentu
     *
     * @param array $conditions Kondisi untuk memfilter pencarian
     * @param array $relations Set relasi yang ingin di load(opsional)
     * @return User|null user
     */
    public function findUser(array $conditions, array $relations = []): ?User;

    /**
     * Reset password user
     *
     * @param User $user Model user yang akan direset password
     * @param string $password Password baru
     * @return bool hasil
     */
    public function resetPassword(User $user, string $password): bool;

    /**
     * Mengirim kode OTP ke email user terkait
     *
     * @param User $user Model user yang akan dikirim email
     * @return string Kode OTP
     */
    public function sendOtpToEmail(User $user): string;

    /**
     * Verifikasi kode OTP
     *
     * @param User $user Model user
     * @param array $data Kode OTP
     * @return bool Hasil
     */
    public function verifyOtp(User $user, array $data): bool;

    /**
     * Invalidate atau menghapus kode OTP terkait user tertentu.
     *
     * @note Gunakan setelah kode OTP terverifikasi
     *
     * @param User $user
     * @return void
     */
    public function invalidateOtps(User $user): void;

    /**
     * Mengatur flow atau alur reset password(Menggunakan flow verifikasi kode OTP atau tidak)
     *
     * @param string $email Email terkait
     * @param string $newPassword Password baru
     * @param bool $invalidateOtp counter, set true untuk menggunakan flow validasi OTP
     * @return bool Hasil
     */
    public function processPasswordResetFlow(string $email, string $newPassword, bool $invalidateOtp = false): bool;
}
