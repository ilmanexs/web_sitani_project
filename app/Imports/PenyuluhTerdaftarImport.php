<?php

namespace App\Imports;

use App\Models\Kecamatan;
use App\Models\PenyuluhTerdaftar;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class PenyuluhTerdaftarImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow, SkipsEmptyRows
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
        $kecamatan = Kecamatan::whereRaw('LOWER(nama) = ?', [strtolower($row['kecamatan'])])->first();

        if (!$kecamatan) {
            return null;
        }

        return new PenyuluhTerdaftar([
            'nama' => $row['nama_penyuluh'],
            'no_hp' => $row['no_hp'],
            'alamat' => $row['alamat_lengkap'],
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
            'nama_penyuluh' => ['required', 'min:3', 'max:100', 'regex:/^[A-Za-z\s\',\.]+$/'],
            'no_hp' => ['required', 'digits_between:11,13', 'starts_with:08'],
            'alamat_lengkap' => ['required', 'min:3', 'max:150'],
            'kecamatan' => ['required', 'exists:kecamatans,nama'],
        ];
    }

    public function getFailures()
    {
        return $this->failuresList;
    }

    public function customValidationMessages(): array
    {
        return [
            'nama_penyuluh.required' => 'Nama tidak boleh kosong',
            'nama_penyuluh.min' => 'Nama tidak boleh kurang dari 3 karakter',
            'nama_penyuluh.max' => 'Nama tidak boleh lebih dari 100 karakter',
            'nama_penyuluh.regex' => 'Nama hanya boleh terdiri dari huruf, tanda petik satu, koma, titik dan spasi.',
            'no_hp.required' => 'No. Hp tidak boleh kosong',
            'no_hp.starts_with' => 'No Hp harus menggunakan format 08xxx',
            'no_hp.digits_between' => 'No Hp harus terdiri dari 11 - 13 digit',
            'alamat_lengkap.required' => 'Alamat tidak boleh kosong',
            'alamat_lengkap.min' => 'Alamat tidak boleh kurang dari 3 karakter',
            'alamat_lengkap.max' => 'Alamat tidak boleh lebih dari 150 karakter',
            'kecamatan.required' => 'Kecamatan tidak boleh kosong',
            'kecamatan.exists' => 'Kecamatan tidak terdaftar',
        ];
    }
}
