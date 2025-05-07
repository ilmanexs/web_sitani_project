<?php

namespace App\Repositories\Interfaces;

use App\Models\LaporanKondisi;
use App\Repositories\Interfaces\Base\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface LaporanBibitRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Mengambil data laporan berdasarkan kecamatan id
     *
     * @param string|int $kecamatanId Kecamatan id
     * @return Collection|array Koleksi laporan
     */
    public function getByKecamatanId(string|int $kecamatanId): Collection|array;

    /**
     * Mengambil total laporan bibit yang masuk ke aplikasi secara keseluruhan
     *
     * @return int Total Data laporan
     */
    public function getTotal(): int;

    /**
     * Menghitung total penggunaan bibit(berkualitas, tidak berkualitas, dan menunggu verifikasi)
     *
     * @param int|null $kecamatanId Kecamatan id, set null untuk mengambil total secara keseluruhan
     * @return array Hasil rekap Penggunaan Bibit(berkualitas, tidak berkualitas, dan laporan yang masih pending)
     */
    public function getLaporanStatusCounts(?int $kecamatanId = null): array;

    /**
     * Menyimpan detail laporan bibit
     *
     * @param array $data Data detail laporan
     * @return Model|null
     */
    public function insertDetailLaporan(array $data): ?Model;

    /**
     * Mengambil total laporan bibit pada kecamatan tertentu.
     *
     * @param string|int $kecamatanId Kecamatan id
     * @return int Total
     */
    public function getTotalByKecamatanId(string|int $kecamatanId): int;

}
