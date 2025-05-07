<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\NotificationInterface;
use App\Trait\LoggingError;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Notifications\DatabaseNotification;

class NotificationRepository implements NotificationInterface
{
    use LoggingError;

    /**
     * @inheritDoc
     * @param User $user
     * @return Collection|array
     * @throws Exception
     */
    public function getUserNotification(User $user): Collection|array
    {
        try {
            return $user->notifications()
                ->latest()
                ->get()
                ->map(function (DatabaseNotification $notification) {
                    return [
                        'id' => $notification->id,
                        'title' => $notification->data['title'] ?? '',
                        'body' => $notification->data['body'] ?? '',
                        'type' => $notification->data['type'] ?? '',
                        'read_at' => $notification->read_at,
                        'created_at' => $notification->created_at,
                    ];
                });
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['user_id' => $user->id]);
            throw new Exception('Terjadi kesalahan pada query repository', 500);
        } catch (\Throwable $e) {
            throw new Exception('Terjadi kesalahan pada saat menandai notifikasi telah dibaca', 500);
        }
    }

    /**
     * @inheritDoc
     * @param User $user
     * @param int|string $id
     * @return bool
     * @throws Exception
     */
    public function markNotificationAsRead(User $user, int|string $id): bool
    {
        try {
            $notification = $user->notifications()->find($id);

            if (!$notification) {
                throw new Exception('Notifikasi tidak ditemukan', 404);
            }

            $notification->markAsRead();
            return true;
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['user_id' => $user->id, 'notification_id' => $id]);
            throw new Exception('Terjadi kesalahan pada query repository', 500);
        } catch (\Throwable $e) {
            throw new Exception('Terjadi kesalahan pada saat menandai notifikasi telah dibaca', 500);
        }
    }

    /**
     * @inheritDoc
     * @param User $user
     * @param int|string $id
     * @return bool
     * @throws Exception
     */
    public function deleteNotification(User $user, int|string $id): bool
    {
        try {
            $notification = $user->notifications()->find($id);

            if (!$notification) {
                throw new Exception('Notifikasi tidak ditemukan', 404);
            }

            $notification->delete();
            return true;
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['notification_id' => $id]);
            throw new Exception('Terjadi kesalahan pada query repository', 500);
        } catch (\Throwable $e) {
            throw new Exception('Terjadi kesalahan pada saat menghapus notifikasi', 500);
        }
    }
}
