<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LaporanBibitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'kelompok_tani_id' => ['required', 'exists:kelompok_tanis,id'],
                'komoditas_id' => ['required', 'exists:komoditas,id'],
                'penyuluh_id' => ['required', 'exists:penyuluhs,id'],
                'luas_lahan' => ['required', 'numeric', 'min:0'],
                'estimasi_panen' => ['required', 'date_format:Y-m-d', 'after:today'],
                'jenis_bibit' => ['required', 'string', 'max:255'],
                'foto_bibit' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
                'lokasi_lahan' => ['required', 'string', 'max:255'],
                'foto_lokasi' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            ];
        }

        if ($this->isMethod('PUT')) {
            return [
                'status' => ['required'],
            ];
        }

        return [];
    }

    public function messages(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'kelompok_tani_id.required' => 'Kelompok Tani wajib diisi.',
                'kelompok_tani_id.exists' => 'Kelompok Tani tidak valid.',
                'komoditas_id.required' => 'Komoditas wajib diisi.',
                'komoditas_id.exists' => 'Komoditas tidak valid.',
                'penyuluh_id.required' => 'Penyuluh wajib diisi.',
                'penyuluh_id.exists' => 'Penyuluh tidak valid.',
                'luas_lahan.required' => 'Luas Lahan wajib diisi.',
                'luas_lahan.numeric' => 'Luas Lahan harus berupa angka.',
                'luas_lahan.min' => 'Luas Lahan tidak boleh kurang dari 0.',
                'estimasi_panen.required' => 'Estimasi Panen wajib diisi.',
                'estimasi_panen.date_format' => 'Format Estimasi Panen harus YYYY-MM-DD.',
                'estimasi_panen.after' => 'Estimasi Panen harus tanggal setelah hari ini.',
                'jenis_bibit.required' => 'Jenis Bibit wajib diisi.',
                'jenis_bibit.string' => 'Jenis Bibit harus berupa teks.',
                'jenis_bibit.max' => 'Jenis Bibit maksimal 255 karakter.',
                'foto_bibit.image' => 'Foto Bibit harus berupa gambar.',
                'foto_bibit.mimes' => 'Format Foto Bibit yang didukung adalah: jpeg, png, jpg.',
                'foto_bibit.max' => 'Ukuran Foto Bibit maksimal 2MB.',
                'lokasi_lahan.required' => 'Lokasi Lahan wajib diisi.',
                'lokasi_lahan.string' => 'Lokasi Lahan harus berupa teks.',
                'lokasi_lahan.max' => 'Lokasi Lahan maksimal 255 karakter.',
                'foto_lokasi.required' => 'Foto Lokasi wajib diisi.',
                'foto_lokasi.image' => 'Foto Lokasi harus berupa gambar.',
                'foto_lokasi.mimes' => 'Format Foto Lokasi yang didukung adalah: jpeg, png, jpg.',
                'foto_lokasi.max' => 'Ukuran Foto Bibit maksimal 2MB.',
            ];
        }

        if ($this->isMethod('PUT')) {
            return [
                'status.required' => 'Silahkan pilih opsi kualitas bibit yang tersedia',
            ];
        }

        return [];
    }
}
