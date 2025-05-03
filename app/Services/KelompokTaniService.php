<?php

namespace App\Services;

use App\Repositories\Interfaces\CrudInterface;
use App\Repositories\Interfaces\KelompokTaniRepositoryInterface;
use App\Repositories\Interfaces\ManyRelationshipManagement;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

class KelompokTaniService
{
    protected CrudInterface $crudRepository;
    protected ManyRelationshipManagement $relationManager;
    protected KelompokTaniRepositoryInterface $repository;

    public function __construct(CrudInterface $crudRepository, ManyRelationshipManagement $relationManager, KelompokTaniRepositoryInterface $repository)
    {
        $this->crudRepository = $crudRepository;
        $this->relationManager = $relationManager;
        $this->repository = $repository;
    }

    /**
     * Mengambil seluruh data kelompok tani
     *
     * @param bool $withRelations Default false, set true untuk mengambil seluruh data beserta dengan relasi
     * @return array
     */
    public function getAll(bool $withRelations = false): array
    {
        try {
            $kelompokTanis = $this->crudRepository->getAll($withRelations);

            if ($kelompokTanis->isNotEmpty()) {
                return [
                    'success' => true,
                    'message' => 'Seluruh data kelompok tani berhasil diambil',
                    'data' => $kelompokTanis,
                ];
            }

            return [
                'success' => false,
                'message' => 'Data kelompok tani kosong',
                'data' => [],
            ];
        } catch (\Throwable $th) {
            Log::error('Gagal mengambil seluruh data kelompok tani.', [
                'source' => __METHOD__,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengambil seluruh data kelompok tani',
                'data' => [],
            ];
        }
    }

    /**
     * Mengambil data kelompok tani beserta relasi
     *
     * @return array|Collection|void
     * @deprecated ganti dengan getAll, dan set param true.
     */
    public function getAllWithRelations()
    {
        try {
            return $this->crudRepository->getAll(true);
        } catch (\Throwable $th) {
            Log::error('Gagal mengambil seluruh data kelompok tani beserta relasi: ' . $th->getMessage());
        }
    }

    /**
     * Mengambil data kelompok tani berdasarkan id
     *
     * @param string|int $id
     * @return array
     */
    public function getById(string|int $id): array
    {
        try {
            $kelompokTani = $this->crudRepository->getById($id);

            if (!empty($kelompokTani)) {
                return [
                    'success' => true,
                    'message' => 'Data kelompok tani ditemukan',
                    'data' => $kelompokTani,
                ];
            }

            return [
                'success' => false,
                'message' => 'Data kelompok tani tidak ditemukan',
                'data' => [],
            ];
        } catch (\Throwable $th) {
            Log::error('Gagal mengambil data kelompok tani berdasarkan id.', [
                'source' => __METHOD__,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengambil data kelompok tani berdasarkan id.',
                'data' => [],
            ];
        }
    }

    /**
     * Mengambil data kelompok tani beserta data pivot
     *
     * @param string|int $id Id kelompok tani
     * @return array
     * @deprecated ganti dengan getById()
     */
    public function getByIdWithPivot(string|int $id): array
    {
        try {
            $kelompokTani = $this->crudRepository->getById($id);
            if (!empty($kelompokTani)) {
                return [
                    'success' => true,
                    'message' => 'Data kelompok tani berhasil diambil',
                    'data' => $kelompokTani,
                ];
            }

            return [
                'success' => false,
                'message' => 'Data kelompok tani tidak ditemukan',
                'data' => [],
            ];
        } catch (\Throwable $th) {
            Log::error('Gagal mengambil data kelompok tani beserta pivot table.', [
                'source' => __METHOD__,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengambil data kelompok tani beserta pivot table.',
                'data' => [],
            ];
        }
    }

    /**
     * Membuat data kelompok tani
     *
     * @param array $data Data kelompok tani
     * @return array
     */
    public function create(array $data): array
    {
        try {
            $kelompokTani = $this->crudRepository->create($data);

            if ($kelompokTani !== null) {
                $penyuluhIds = $data['penyuluh_terdaftar_id'] ?? [];

                if (!empty($penyuluhIds)) {
                    $penyuluhIdsToAttach = Arr::flatten($penyuluhIds);
                    $this->relationManager->attach($kelompokTani, $penyuluhIdsToAttach);
                }

                return [
                    'success' => true,
                    'message' => 'Data kelompok tani berhasil disimpan',
                    'data' => $kelompokTani,
                ];
            }

            return [
                'success' => false,
                'message' => 'Data kelompok tani gagal disimpan',
                'data' => [],
            ];
        } catch (\Throwable $th) {
            Log::error('Gagal menyimpan data kelompok tani.', [
                'source' => __METHOD__,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menyimpan data kelompok tani.',
                'data' => [],
            ];
        }
    }

    /**
     * Memperbarui data kelompok tani
     *
     * @param string|int $id Id kelompok tani
     * @param array $data Data kelompok tani yang baru
     * @return array
     */
    public function update(string|int $id, array $data): array
    {
        try {
            $kelompokTani = $this->crudRepository->update($id, [
                "nama" => $data["nama"],
                "desa_id" => $data["desa_id"],
                "kecamatan_id" => $data["kecamatan_id"],
            ]);

            if (!empty($kelompokTani)) {
                $this->relationManager->sync($kelompokTani, $data['penyuluh_terdaftar_id']);
                $penyuluhIds = $data['penyuluh_terdaftar_id'] ?? [];

                $penyuluhIdsToSync = Arr::flatten($penyuluhIds);
                $this->relationManager->sync($kelompokTani, $penyuluhIdsToSync);
                return [
                    'success' => true,
                    'message' => 'Data kelompok tani berhasil diperbarui',
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => 'Data kelompok tani gagal diperbarui',
                'data' => [],
            ];
        } catch (\Throwable $th) {
            Log::error('Gagal memperbarui data kelompok tani.', [
                'source' => __METHOD__,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memperbarui data kelompok tani.',
                'data' => [],
            ];
        }
    }

    /**
     * Menghapus data kelompok tani
     *
     * @param string|int $id Id kelompok tani
     * @return array
     */
    public function delete(string|int $id): array
    {
        try {
            $kelompokTani = $this->crudRepository->delete($id);
            if (!empty($kelompokTani)) {
                $this->relationManager->detach($kelompokTani);

                return [
                    'success' => true,
                    'message' => 'Data kelompok tani berhasil dihapus',
                    'data' => $kelompokTani,
                ];
            }

            return [
                'success' => false,
                'message' => 'Data kelompok tani gagal dihapus',
                'data' => [],
            ];

        } catch (\Throwable $th) {
            Log::error('Gagal menghapus data kelompok tani.', [
                'source' => __METHOD__,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal menghapus data kelompok tani.',
                'data' => [],
            ];
        }
    }

    /**
     * Mengambil data kelompok tani berdasarkan  id penyuluh
     *
     * @param array $id Id penyuluh
     * @return array
     */
    public function getByPenyuluhId(array $id): array
    {
        try {
            $kelompokTanis = $this->repository->getByPenyuluhId($id);
            if ($kelompokTanis->isNotEmpty()) {
                $data = $kelompokTanis->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'desa' => [
                            'id' => $item->desa->id ?? null,
                            'nama' => $item->desa->nama ?? null,
                        ],
                        'kecamatan' => [
                            'id' => $item->desa->kecamatan->id ?? null,
                            'nama' => $item->desa->kecamatan->nama ?? null,
                        ],
                        'penyuluhs' => $item->penyuluhTerdaftars->map(function ($penyuluh) {
                            return [
                                'id' => $penyuluh->id,
                                'nama' => $penyuluh->nama,
                                'no_hp' => $penyuluh->no_hp,
                                'alamat' => $penyuluh->alamat,
                            ];
                        }),
                    ];
                });

                return [
                    'success' => true,
                    'message' => 'Data kelompok tani ditemukan',
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => 'Data kelompok tani tidak ditemukan',
                'data' => [],
            ];
        } catch (\Throwable $th) {
            Log::error('Terjadi kesalahan saat mengambil data kelompok tani.', [
                'source' => __METHOD__,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Data kelompok tani tidak ditemukan',
                'data' => [],
            ];
        }
    }

    /**
     * Mengambil totol kelompok tani yang terdaftar di Sitani
     *
     * @return int Total
     * @throws \Exception
     */
    public function calculateTotal(): int
    {
        try {
            return $this->repository->calculateTotal();
        } catch (\Throwable $e) {
            Log::error('Terjadi kesalahan saat menghitung total record data', [
                'source' => __METHOD__,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'previous' => $e->getPrevious(),
            ]);
            throw new \Exception('Terjadi Kesalahan diserver saat menghitung total kelompok tani', $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * Mengambil total kelompok tani berdasarkan kecamatan id
     *
     * @return int Total
     * @throws Exception
     * @throws Throwable
     */
    public function countByKecamatanId(string|int $id): int
    {
        try {
            return $this->repository->countByKecamatanId($id);
        }catch (\Throwable $e) {
            throw new Exception('Terjadi kesalahan di server', 500);
        }
    }
}
