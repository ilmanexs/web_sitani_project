<?php

namespace App\Imports;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\KelompokTani;
use App\Models\PenyuluhTerdaftar;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class KelompokTaniImport implements ToCollection, WithValidation, SkipsOnFailure, WithHeadingRow, WithStartRow, SkipsEmptyRows
{
    use SkipsFailures;

    private $failuresList = [];

    /**
     * Untuk handle proses import data
     *
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $originalRowNumber = $this->startRow() + $index;

            $namaKelompok = $row['nama_kelompok_tani'] ?? null;
            $desaNama = $row['desa'] ?? null;
            $kecamatanNama = $row['kecamatan'] ?? null;
            $noHpPenyuluhString = $row['no_hp_penyuluh'] ?? '';

            $errors = [];

            $kecamatan = null;
            if (!empty($kecamatanNama)) {
                $kecamatan = Kecamatan::where('nama', $kecamatanNama)->first();
                if (!$kecamatan) {
                    $errors['kecamatan'] = ['Kecamatan "' . $kecamatanNama . '" tidak ditemukan dalam database.'];
                }
            } else {
                $errors['kecamatan'] = ['Nama kecamatan wajib diisi.'];
            }

            $desa = null;
            if (empty($errors['kecamatan'])) {
                if (!empty($desaNama)) {
                    $desa = Desa::where('nama', $desaNama)
                        ->where('kecamatan_id', $kecamatan->id)
                        ->first();
                    if (!$desa) {
                        $errors['desa'] = ['Desa "' . $desaNama . '" tidak terdaftar di Kecamatan "' . $kecamatanNama . '".'];
                    }
                } else {
                    $errors['desa'] = ['Nama desa wajib diisi.'];
                }
            }

            if (empty($errors['kecamatan'])) {
                $noHpArray = array_map('trim', explode(',', $noHpPenyuluhString));
                $noHpArray = array_filter($noHpArray);

                if (!empty($noHpArray)) {
                    $penyuluhTerdaftars = PenyuluhTerdaftar::whereIn('no_hp', $noHpArray)
                        ->where('kecamatan_id', $kecamatan->id)
                        ->get();

                    $foundNoHp = $penyuluhTerdaftars->pluck('no_hp')->toArray();

                    $missPenyuluh = array_diff($noHpArray, $foundNoHp);

                    if (!empty($missPenyuluh)) {
                        $errors['no_hp_penyuluh'] = ['Nomor HP Penyuluh (' . implode(', ', $missPenyuluh) . ') tidak terdaftar di Kecamatan "' . $kecamatanNama . '".'];
                    }
                }
            }


            if (!empty($errors)) {
                foreach ($errors as $attribute => $messages) {
                    $this->failuresList[] = new Failure($originalRowNumber, $attribute, $messages, $row->toArray());
                }
                continue;
            }

            try {
                $kelompokTani = KelompokTani::create([
                    'nama' => $namaKelompok,
                    'desa_id' => $desa->id,
                    'kecamatan_id' => $kecamatan->id,
                ]);

                if (!empty($noHpArray)) {
                    $penyuluhIds = PenyuluhTerdaftar::whereIn('no_hp', $noHpArray)
                        ->where('kecamatan_id', $kecamatan->id)
                        ->pluck('id')
                        ->toArray();

                    if (!empty($penyuluhIds)) {
                        $kelompokTani->penyuluhTerdaftars()->attach($penyuluhIds);
                    }
                }
            } catch (\Exception $e) {
                $this->failuresList[] = new Failure($originalRowNumber, 'process', ['Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()], $row->toArray());
            }
        }
    }

    /**
     * Rule/Aturan data
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama_kelompok_tani' => ['required', 'string', 'min:3', 'max:255'],
            'desa' => ['required', 'string'],
            'kecamatan' => ['required', 'string'],
            'no_hp_penyuluh' => ['nullable', 'string'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_kelompok_tani.required' => 'Nama kelompok tani wajib diisi.',
            'nama_kelompok_tani.min' => 'Nama kelompok tani minimal 3 karakter.',
            'nama_kelompok_tani.max' => 'Nama kelompok tani maksimal 255 karakter.',
            'nama_kelompok_tani.string' => 'Nama kelompok tani harus berupa string.',
            'desa.required' => 'Nama Desa wajib diisi.',
            'kecamatan.required' => 'Kecamatan wajib diisi.',
        ];
    }

    /**
     * Untuk mendapatkan kesalahan validasi
     *
     * @param Failure ...$failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failuresList[] = $failure;
        }
    }

    /**
     * Getter untuk mendapatkan data kesalahan yang terjadi
     *
     * @return Collection
     */
    public function getFailures()
    {
        return collect($this->failuresList);
    }


    /**
     * Data di mulai pada row 2
     *
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Mengatur row 1 sebagai heading
     *
     * @return int
     */
    public function headingRow(): int
    {
        return 1;
    }
}
