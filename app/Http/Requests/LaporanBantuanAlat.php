<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaporanBantuanAlat extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'kelompok_tani_id' => 'required|exists:kelompok_tanis,id',
            'penyuluh_id' => 'required|exists:penyuluhs,id',
            'alat_diminta' => ['required', 'regex:/^[a-zA-Z0-9\s]+$/'],
            'nama_ketua' => 'required|string|max:255',
            'no_hp_ketua' => 'required|string|max:255',
            'npwp' => 'required|string|max:255',
            'email_kelompok_tani' => 'required|email|max:255',
            'password_email' => 'required|string|max:255',
            'path_proposal' => 'required|file|mimes:pdf|max:500',
            'path_ktp_ketua' => 'required|file|mimes:jpg,jpeg|max:2048',
            'path_badan_hukum' => 'required|file|mimes:pdf|max:500',
            'path_piagam' => 'required|file|mimes:pdf|max:500',
            'path_surat_domisili' => 'required|file|mimes:pdf|max:500',
            'path_foto_lokasi' => 'required|file|mimes:jpg,jpeg|max:2048',
            'path_ktp_sekretaris' => 'required|file|mimes:jpg,jpeg|max:2048',
            'path_ktp_ketua_upkk' => 'required|file|mimes:jpg,jpeg|max:2048',
            'path_ktp_anggota1' => 'required|file|mimes:jpg,jpeg|max:2048',
            'path_ktp_anggota2' => 'required|file|mimes:jpg,jpeg|max:2048',

        ];
    }

    public function messages(): array
    {
        return [
            'kelompok_tani_id.exists' => 'Kelompok tani tidak ditemukan',
            'penyuluh_id.exists' => 'Penyuluh tidak ditemukan',
            'alat_diminta.regex' => 'Alat diminta hanya boleh berisi huruf, angka, dan spasi',
            'path_proposal.mimes' => 'Proposal harus berupa file PDF , dan ukuran file tidak boleh lebih dari 500kb',
            'path_badan_hukum.mimes' => 'Badan Hukum harus berupa file PDF , dan ukuran file tidak boleh lebih dari 500kb',
            'path_piagam.mimes' => 'Piagam harus berupa file PDF , dan ukuran file tidak boleh lebih dari 500kb',
            'path_surat_domisili.mimes' => 'Surat Domisili harus berupa file PDF , dan ukuran file tidak boleh lebih dari 500kb',
            '*.mimes' => 'File harus berupa JPG atau JPEG',
        ];
    }
}
