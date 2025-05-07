<?php

namespace App\Imports;

use App\Models\Kecamatan;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class KecamatanImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow, SkipsEmptyRows
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
        return new Kecamatan([
            'nama' => $row['nama'],
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
            'nama' => ['required', 'min:3', 'max:50', 'regex:/^[A-Za-z\s]+$/'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama.required' => 'Nama harus diisi.',
            'nama.min' => 'Nama tidak boleh kurang dari 3 karakter.',
            'nama.max' => 'Nama tidak boleh lebih dari 50 karakter.',
            'nama.regex' => 'Nama hanya boleh huruf dan spasi.',
        ];
    }

    public function getFailures()
    {
        return $this->failuresList;
    }
}
