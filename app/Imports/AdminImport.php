<?php

namespace App\Imports;

use App\Models\Admin;
use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class AdminImport implements ToModel, WithValidation, SkipsOnFailure, WithHeadingRow, SkipsEmptyRows
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
        $user = User::create([
            'email' => $row['email'],
            'password' => bcrypt('sitani'),
            'is_password_set' => false
        ]);

        $user->assignRole($row['role']);

        return new Admin([
            'nama' => $row['nama_lengkap'],
            'no_hp' => $row['no_hp'],
            'alamat' => $row['alamat_lengkap'],
            'user_id' => $user->id,
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
            'nama_lengkap' => ['required', 'min:3', 'max:50', 'regex:/^[A-Za-z\s\',\.]+$/'],
            'no_hp' => ['required', 'starts_with:08', 'digits_between:11,13'],
            'alamat_lengkap' => ['required', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'exists:roles,name'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama wajib diisi!',
            'nama_lengkap.min' => 'Nama minimal terdiri dari :min karakter.',
            'nama_lengkap.max' => 'Nama maksimal terdiri dari :max karakter.',
            'nama_lengkap.regex' => 'Nama hanya boleh terdiri dari huruf, tanda petik satu, koma, titik dan spasi.',
            'no_hp.required' => 'Nomor HP wajib diisi!',
            'no_hp.starts_with' => 'Nomor HP harus dimulai dengan angka 08.',
            'no_hp.digits_between' => 'Nomor HP harus terdiri dari 11 - 13 digit.',
            'alamat_lengkap.required' => 'Alamat wajib diisi!',
            'alamat_lengkap.min' => 'Alamat minimal terdiri dari :min karakter.',
            'alamat_lengkap.max' => 'Alamat maksimal terdiri dari :max karakter.',
            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Email harus berupa alamat email yang valid.',
            'email.unique' => 'Email sudah terdaftar atau digunakan.',
            'role.required' => 'Nama role wajib diisi!',
            'role.exists' => 'Role tidak terdaftar di aplikasi.',
        ];
    }

    public function getFailures()
    {
        return $this->failuresList;
    }
}
