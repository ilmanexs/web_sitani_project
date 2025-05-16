<?php

namespace App\Exports;

use App\Models\LaporanBantuanAlat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanBantuanAlatExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return LaporanBantuanAlat::with([
            'kelompokTani:id,nama',
            'penyuluh:id,user_id',
            'penyuluh.user:id,email',
            'laporanBantuanAlatDetail:permintaan_bantuan_alat_id,nama_ketua,no_hp_ketua,npwp,email_kelompok_tani,password_email',
        ])->get()
            ->map(function ($laporan) {
                return [
                    'nama_kelompok_tani' => $laporan->kelompokTani->nama ?? '-',
                    'email_penyuluh' => $laporan->penyuluh->user->email ?? '-',
                    'nama_alat' => $laporan->alat_diminta ?? '-',
                    'nama_ketua' => $laporan->laporanBantuanAlatDetail->first()->nama_ketua ?? '-',
                    'no_hp_ketua' => $laporan->laporanBantuanAlatDetail->first()->no_hp_ketua ?? '-',
                    'npwp' => $laporan->laporanBantuanAlatDetail->first()->npwp ?? '-',
                    'email' => $laporan->laporanBantuanAlatDetail->first()->email_kelompok_tani ?? '-',
                    'password' => $laporan->laporanBantuanAlatDetail->first()->password_email ?? '-',
                    'status' => match ($laporan->status) {
                        "1" => 'Disetujui',
                        "0" => 'Ditolak',
                        "2" => 'Pending',
                        default => $laporan->status ?? '-',
                    },
                    'tanggal' => $laporan->created_at ? $laporan->created_at->format('Y-m-d H:i:s') : '-',
                ];
            });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nama Kelompok Tani',
            'Email Penyuluh',
            'Nama Alat',
            'Nama Ketua',
            'No Hp Ketua',
            'NPWP',
            'Email',
            'Password',
            'Status',
            'Tanggal',
        ];
    }
}
