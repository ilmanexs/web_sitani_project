<?php

namespace App\Exports;

use App\Models\LaporanKondisi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanBibitExport implements FromCollection, WithHeadings
{
    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        return LaporanKondisi::with([
            'kelompokTani:id,nama,desa_id,kecamatan_id',
            'kelompokTani.desa:id,nama',
            'kelompokTani.kecamatan:id,nama',
            'komoditas:id,nama',
            'penyuluh:id,user_id',
            'penyuluh.user:id,email',
            'laporanKondisiDetail:laporan_kondisi_id,jenis_bibit,luas_lahan,estimasi_panen,lokasi_lahan',
        ])
            ->get()
            ->map(function ($laporan) {
                $detail = $laporan->laporanKondisiDetail->first();
                return [
                    'nama_kelompok_tani' => $laporan->kelompokTani->nama ?? '-',
                    'komoditas' => $laporan->komoditas->nama ?? '-',
                    'nama_desa' => $laporan->kelompokTani->desa->nama ?? '-',
                    'nama_kecamatan' => $laporan->kelompokTani->kecamatan->nama ?? '-',
                    'email_penyuluh' => $laporan->penyuluh->user->email ?? '-',
                    'jenis_bibit' => $detail->jenis_bibit ?? '-',
                    'luas_lahan' => $detail->luas_lahan ?? '-',
                    'estimasi_panen' => $detail->estimasi_panen ?? '-',
                    'lokasi_lahan' => $detail->lokasi_lahan ?? '-',
                    'status' => match ($laporan->status) {
                        "1" => 'Bibit Berkualitas',
                        "0" => 'Bibit Tidak Berkualitas',
                        "2" => 'Pending',
                        default => 'Tidak Diketahui',
                    },
                    'waktu_laporan' => $laporan->created_at ? $laporan->created_at->format('Y-m-d H:i:s') : '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nama Kelompok Tani',
            'Komoditas',
            'Nama Desa',
            'Nama Kecamatan',
            'Email Penyuluh',
            'Jenis Bibit',
            'Luas Lahan/Hektar',
            'Estimasi Panen',
            'Lokasi Lahan',
            'Status',
            'Waktu Laporan',
        ];
    }
}
