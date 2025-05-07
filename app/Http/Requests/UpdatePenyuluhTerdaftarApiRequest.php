<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePenyuluhTerdaftarApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['id' => $this->route('id')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:penyuluh_terdaftars,id',
            'nama_penyuluh' => 'required|min:3|max:255|regex:/^[A-Za-z\s\',\.]+$/',
            'alamat_penyuluh' => 'required|min:3|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID Penyuluh Terdaftar wajib diisi.',
            'id.exists' => 'Penyuluh Terdaftar Tidak Ditemukan',
            'nama_penyuluh.required' => 'Nama Penyuluh wajib diisi.',
            'nama_penyuluh.min' => 'Nama Penyuluh Minimal 3 karakter.',
            'nama_penyuluh.max' => 'Nama Penyuluh Maksimal 255 karakter.',
            'nama_penyuluh.regex' => 'Nama penyuluh hanya boleh terdiri dari huruf, tanda petik satu, koma, titik dan spasi.',
            'alamat_penyuluh.required' => 'Alamat Penyuluh wajib diisi.',
            'alamat_penyuluh.min' => 'Alamat Penyuluh Minimal 3 karakter.',
            'alamat_penyuluh.max' => 'Alamat Penyuluh Maksimal 255 karakter.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();

        throw new HttpResponseException(
            response()->json([
                'message' => 'Validasi gagal',
                'errors' => $errors
            ], 422)
        );
    }
}
