<?php

namespace App\Repositories;

use App\Models\Penyuluh;
use App\Models\User;
use App\Repositories\Interfaces\PenyuluhRepositoryInterface;
use App\Trait\LoggingError;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

class PenyuluhRepository implements PenyuluhRepositoryInterface
{
    use LoggingError;

    /**
     * @inheritDoc
     * @param bool $withRelations
     * @param array $criteria
     * @return Collection|array
     * @throws Throwable
     */
    public function getAll(bool $withRelations = false, array $criteria = []): Collection|array
    {
        try {
            $query = Penyuluh::query();

            if ($withRelations) {
                $query->with([
                    'user:id,email,created_at',
                    'penyuluhTerdaftar:id,nama,no_hp,alamat',
                ]);
            }

            return $query->get();

        } catch (QueryException $e) {
            $this->LogSqlException($e, [], 'Database error saat mengambil semua data penyuluh.');
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, [], 'Terjadi kesalahan tak terduga saat mengambil semua data penyuluh.');
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @return Model|null
     * @throws Throwable
     */
    public function getById(string|int $id): ?Model
    {
        try {
            return Penyuluh::where('id', $id)->with([
                'user:id,email',
                'penyuluhTerdaftar:id,nama,no_hp,alamat',
            ])->first();
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id], 'Database error saat mengambil data penyuluh berdasarkan ID.');
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id], 'Terjadi kesalahan tak terduga saat mengambil data penyuluh berdasarkan ID.');
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @param array $data
     * @return Model|null
     * @throws Throwable
     */
    public function create(array $data): ?Model
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = User::create([
                    'email' => $data['email'],
                    'password' => bcrypt($data['password']),
                    'is_password_set' => true,
                ]);

                $user->assignRole('penyuluh');

                $penyuluh = Penyuluh::create([
                    'user_id' => $user->id,
                    'penyuluh_terdaftar_id' => $data['penyuluh_terdaftar_id'],
                ]);

                $penyuluh->load('user:id,email,created_at', 'penyuluhTerdaftar:id,nama,no_hp,alamat,kecamatan_id');

                return $penyuluh;
            });
        } catch (QueryException $e) {
            $this->LogSqlException($e, $data, 'Database error saat membuat data penyuluh dan user.');
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, $data, 'Terjadi kesalahan tak terduga saat membuat data penyuluh dan user.');
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @param array $data
     * @return bool|int
     * @throws Throwable
     */
    public function update(string|int $id, array $data): bool|int
    {
        try {
            return Penyuluh::findOrFail($id)->update($data);
        } catch (ModelNotFoundException $e) {
            $this->LogGeneralException($e, ['id' => $id, 'data_baru' => $data], 'Model penyuluh tidak ditemukan untuk diupdate.');
            throw $e;
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id, 'data_baru' => $data], 'Database error saat memperbarui data penyuluh.');
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id, 'data_baru' => $data], 'Terjadi kesalahan tak terduga saat memperbarui data penyuluh.');
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @param string|int $id
     * @return bool|int
     * @throws Throwable
     */
    public function delete(string|int $id): bool|int
    {
        try {
            $deleted = Penyuluh::destroy($id);
            if ($deleted === 0) {
                throw new ModelNotFoundException('Model Penyuluh dengan ID ' . $id . ' tidak ditemukan untuk dihapus.');
            }
            return $deleted > 0;
        } catch (ModelNotFoundException $e) {
            $this->LogGeneralException($e, ['id' => $id], 'Model penyuluh tidak ditemukan untuk dihapus.');
            throw $e;
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['id' => $id], 'Database error saat menghapus data penyuluh.');
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['id' => $id], 'Terjadi kesalahan tak terduga saat menghapus data penyuluh.');
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @return int
     * @throws Throwable
     */
    public function calculateTotal(): int
    {
        try {
            return Penyuluh::count();
        } catch (QueryException $e) {
            $this->LogSqlException($e, [], 'Database error saat menghitung total penyuluh.');
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, [], 'Terjadi kesalahan tak terduga saat menghitung total penyuluh.');
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function existsByPenyuluhTerdaftarId(int|string $penyuluhTerdaftarId): bool
    {
        try {
            return Penyuluh::whereHas('penyuluhTerdaftar', function ($query) use ($penyuluhTerdaftarId) {
                $query->where('id', $penyuluhTerdaftarId);
            })->exists();
        } catch (QueryException $e) {
            $this->LogSqlException($e, [], 'Database error saat mengecek akun terdaftar penyuluh.');
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, [], 'Terjadi kesalahan tak terduga saat mengecek akun terdaftar penyuluh.');
            throw $e;
        }
    }
}
