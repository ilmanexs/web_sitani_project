<?php

namespace App\Http\Controllers;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Requests\LaporanBantuanAlat;
use App\Services\Interfaces\LaporanBantuanAlatServiceInterface;

use Illuminate\Http\Request;

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
}
