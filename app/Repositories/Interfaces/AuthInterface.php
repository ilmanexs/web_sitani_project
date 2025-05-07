<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface AuthInterface
{
    /**
     * Mencari user berdasarkan kondisi tertentu
     *
     * @param array $conditions kondisi untuk filter pencarian
     * @param array $withRelations set ralasi yang ingin diload(opsional)
     * @return User|null User
     */
    public function findUser(array $conditions, array $withRelations = []): ?User;

    /**
     * Mereset password user
     *
     * @param User $user Model user yang akan direset password
     * @param string $password password baru
     * @return bool Hasil
     */
    public function resetPassword(User $user, string $password): bool;

    /**
     * Generate dan save kode OTP ke database
     *
     * @param User $user Model user
     * @return string Kode OTP
     */
    public function generateAndSaveOtp(User $user): string;

    /**
     * Validasi kode OTP
     *
     * @param User $user Model user
     * @param string $code Kode OTP
     * @return bool Hasil
     */
    public function validateOtp(User $user, string $code): bool;

    /**
     * Invalidate atau menghapus kode OTP user. @note Gunakan setelah kode OTP diverifikasi.
     *
     * @param User $user Model user
     * @return void
     */
    public function invalidateOtps(User $user): void;
}
