<?php

namespace App\Http\Controllers;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
//use App\Http\Requests\LaporanBantuanAlat;
use App\Models\LaporanBantuanAlat;
use App\Services\Interfaces\LaporanBantuanAlatServiceInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;


class LaporanBantuanAlatController extends Controller
{
    protected LaporanBantuanAlatServiceInterface $laporanService;

    public function __construct(LaporanBantuanAlatServiceInterface $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    public function index()
    {
        try {
            $laporans = $this->laporanService->getAll(true);
        } catch (DataAccessException $e) {
            $laporans = collect();
            session()->flash('error', 'Gagal memuat data laporan bantuan alat.');
        }

        return view('pages.laporan_alat.index', compact('laporans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LaporanBantuanAlat $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $laporan = $this->laporanService->getById($id);
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            abort(500, 'Terjadi kesalahan saat memuat data laporan bantuan alat untuk edit. Silakan coba lagi.');;
        }

        return view('pages.laporan_alat.verifikasi-alat', compact('laporan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required'
        ], ['status.required' => 'Silahkan Pilih Status Verifikasi Laporan Bantuan Alat!']);
        try {
            $this->laporanService->update($id, ['status' => $request->status]);
            return redirect()->route('laporan-alat.index')->with('success', 'Laporan berhasil diverifikasi');
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data laporan bantuan alat. Silakan coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->laporanService->delete($id);
            return redirect()->route('laporan-alat.index')->with('success', 'Data berhasil dihapus');
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->route('laporan-alat.index')->with('error', 'Gagal menghapus data laporan bantuan alat. Silakan coba lagi.');
        }
    }
    public function downloadZip($id)
    {
        $laporan = LaporanBantuanAlat::with('LaporanBantuanAlatDetail')->findOrFail($id);

        $zip = new ZipArchive();
        $zipFileName = 'laporan_' . $id . '.zip';
        $zipPath = storage_path('app/public/tmp/' . $zipFileName);

        // Buat folder sementara jika belum ada
        if (!file_exists(storage_path('app/public/tmp'))) {
            mkdir(storage_path('app/public/tmp'), 0777, true);
        }

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = [];

            // Dokumen PDF
            $files['Badan_Hukum.pdf'] = $laporan->LaporanBantuanAlatDetail->path_badan_hukum ?? null;
            $files['Proposal.pdf'] = $laporan->path_proposal ?? null;
            $files['Piagam_Pengesahan.pdf'] = $laporan->LaporanBantuanAlatDetail->path_piagam ?? null;
            $files['Surat_Domisili.pdf'] = $laporan->LaporanBantuanAlatDetail->path_surat_domisili ?? null;

            // Gambar
            $files['Foto_Lokasi.jpg'] = $laporan->LaporanBantuanAlatDetail->path_foto_lokasi ?? null;
            $files['KTP_Sekretaris.jpg'] = $laporan->LaporanBantuanAlatDetail->path_ktp_sekretaris ?? null;
            $files['KTP_Ketua_UPKK.jpg'] = $laporan->LaporanBantuanAlatDetail->path_ktp_ketua_upkk ?? null;
            $files['KTP_Anggota_1.jpg'] = $laporan->LaporanBantuanAlatDetail->path_ktp_anggota1 ?? null;
            $files['KTP_Anggota_2.jpg'] = $laporan->LaporanBantuanAlatDetail->path_ktp_anggota2 ?? null;
            $files['KTP_Ketua.jpg'] = $laporan->LaporanBantuanAlatDetail->path_ktp_ketua ?? null;

            foreach ($files as $fileName => $path) {
                if ($path && Storage::disk('public')->exists($path)) {
                    $fullPath = Storage::disk('public')->path($path);
                    $zip->addFile($fullPath, $fileName);
                }
            }

            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Tidak dapat membuat file ZIP.');
    }

    public function export()
    {
        try {
            $exporter = $this->laporanService->export();
            return Excel::download($exporter, 'Laporan hibah.xlsx');
        } catch (DataAccessException $e) {
            return redirect()->back()->with('error', 'Gagal menyiapkan data untuk export.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh data export.');
        }
    }

}
