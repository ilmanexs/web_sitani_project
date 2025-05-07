<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\NotificationInterface;
use App\Services\Interfaces\NotificationServiceInterface;
use App\Trait\LoggingError;
use Exception;
use Illuminate\Support\Collection;

class NotificationService implements NotificationServiceInterface
{
    use LoggingError;

    protected NotificationInterface $repository;

    public function __construct(NotificationInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     * @param User $user
     * @return Collection|array
     * @throws Exception
     */
    public function getUserNotification(User $user): Collection|array
    {
        try {
            return $this->repository->getUserNotification($user);
        } catch (\Throwable $th) {
            $this->LogGeneralException($th);
            throw new Exception('Terjadi kesalahan saat mengambil notifikasi pengguna', $th->getCode());
        }
    }

    /**
     * @inheritDoc
     * @param User $user
     * @param string|int $id
     * @return bool
     * @throws Exception
     */
    public function markAsRead(User $user, string|int $id): bool
    {
        try {
            return $this->repository->markNotificationAsRead($user, $id);
        } catch (\Throwable $th) {
            $this->LogGeneralException($th);
            throw new Exception($th->getMessage(), $th->getCode());
        }
    }

    /**
     * @inheritDoc
     * @param User $user
     * @param string|int $id
     * @return bool
     * @throws Exception
     */
    public function deleteNotification(User $user, string|int $id): bool
    {
        try {
            return $this->repository->deleteNotification($user, $id);
        } catch (\Throwable $th) {
            $this->LogGeneralException($th);
            throw new Exception($th->getMessage(), $th->getCode());
        }
    }

}
