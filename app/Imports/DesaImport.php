<?php

namespace App\Imports;

use App\Models\Desa;
use App\Models\Kecamatan;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class DesaImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow, SkipsEmptyRows
{
    use SkipsFailures;
    private $failuresList = [];

    /**
    * @param array $row
    *
    * @return Model|null
    */
    public function model(array $row)
    {
        $kecamatan = Kecamatan::where('nama', $row['nama_kecamatan'])->first();

        if (!$kecamatan) {
            return null;
        }

        return new Desa([
            'nama' => $row['nama_desa'],
            'kecamatan_id' => $kecamatan->id,
        ]);
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failuresList[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
            ];
        }
    }

    public function rules(): array
    {
        return [
            'nama_desa' => ['required', 'min:3', 'max:50', 'regex:/^[A-Za-z\s]+$/'],
            'nama_kecamatan' => ['required', 'exists:kecamatans,nama'],
        ];
    }

    public function getFailures()
    {
        return $this->failuresList;
    }

    public function customValidationMessages(): array
    {
        return [
            'nama_desa.required' => 'Nama Desa harus diisi.',
            'nama_desa.min' => 'Nama Desa minimal 3 karakter.',
            'nama_desa.max' => 'Nama Desa maksimal 50 karakter.',
            'nama_desa.regex' => 'Nama desa hanya boleh huruf dan angka',
            'nama_kecamatan.required' => 'Nama Kecamatan harus diisi.',
            'nama_kecamatan.exists' => 'Nama Kecamatan Tidak ditemukan atau terdaftar.',
        ];
    }
}
