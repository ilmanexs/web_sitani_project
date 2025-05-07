<?php

namespace App\Services\Interfaces;

use App\Models\User;
use Illuminate\Support\Collection;

interface NotificationServiceInterface
{
    /**
     * Mengambil seluruh notifikasi pengguna
     *
     * @param User $user
     * @return Collection|array
     */
    public function getUserNotification(User $user): Collection|array;

    /**
     * Menandai notifikasi sebagai telah dibaca
     *
     * @param User $user
     * @param string|int $id
     * @return bool
     */
    public function markAsRead(User $user, string|int $id): bool;

    /**
     * Menghapus notifikasi pengguna
     *
     * @param User $user
     * @param string|int $id
     * @return bool
     */
    public function deleteNotification(User $user, string|int $id): bool;
}
