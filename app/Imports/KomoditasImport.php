<?php

namespace App\Imports;

use App\Models\Komoditas;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class KomoditasImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow, SkipsEmptyRows
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
        return new Komoditas([
            'nama' => $row['nama'],
            'musim' => $row['musim'],
            'deskripsi' => $row['deskripsi'],
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'min:4', 'max:50', 'regex:/^[A-Za-z\s]+$/'],
            'musim' => ['required', 'numeric', 'min:1', 'max:10'],
            'deskripsi' => ['nullable', 'max:255'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama.required' => 'Nama harus diisi.',
            'musim.required' => 'Musim harus diisi.',
            'musim.numeric' => 'Musim harus berupa angka.',
            'musim.max' => 'Musim tidak boleh lebih dari 10.',
            'musim.min' => 'Musim minimal harus 1.',
        ];
    }

    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->failuresList[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
            ];
        }
    }

    public function getFailures()
    {
        return $this->failuresList;
    }
}
