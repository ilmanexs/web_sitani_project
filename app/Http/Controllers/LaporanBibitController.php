<?php

namespace App\Http\Controllers;

use App\Http\Requests\LaporanBibitRequest;
use App\Services\Interfaces\LaporanBibitServiceInterface;
use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class LaporanBibitController extends Controller
{
    protected LaporanBibitServiceInterface $laporanService;

    public function __construct(LaporanBibitServiceInterface $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    public function index(): View
    {
        try {
            $laporans = $this->laporanService->getAll(true);
        } catch (DataAccessException $e) {
            $laporans = collect();
            session()->flash('error', 'Gagal memuat data laporan bibit.');
        } catch (Throwable $e) {
            $laporans = collect();
            session()->flash('error', 'Terjadi kesalahan tak terduga.');
        }

        return view('pages.laporan_bibit.index', compact('laporans'));
    }

    public function edit(string $id): View
    {
        try {
            $laporan = $this->laporanService->getById($id);

        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            abort(500, 'Terjadi kesalahan saat memuat data laporan bibit untuk edit. Silakan coba lagi.');
        } catch (Throwable $e) {
            abort(500, 'Terjadi kesalahan tak terduga.');
        }

        return view('pages.laporan_bibit.verifikasi-bibit', compact('laporan'));
    }

    public function update(LaporanBibitRequest $request, string $id): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->laporanService->update($id, ['status' => $validated['status']]);

            return redirect()->route('laporan-bibit.index')->with('success', 'Laporan berhasil diverifikasi');
        } catch (ResourceNotFoundException $e) {
            return redirect()->route('laporan-bibit.index')->with('error', $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data laporan bibit. Silakan coba lagi.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan tak terduga saat memperbarui data.');
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->laporanService->delete($id);

            return redirect()->route('laporan-bibit.index')->with('success', 'Laporan berhasil dihapus');
        } catch (ResourceNotFoundException $e) {
            return redirect()->route('laporan-bibit.index')->with('error', $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->route('laporan-bibit.index')->with('error', 'Gagal menghapus data laporan bibit. Silakan coba lagi.');
        } catch (Throwable $e) {
            return redirect()->route('laporan-bibit.index')->with('error', 'Terjadi kesalahan tak terduga saat menghapus data.');
        }
    }

    public function export()
    {
        try {
            $exporter = $this->laporanService->export();
            return Excel::download($exporter, 'Laporan bibit.xlsx');
        } catch (DataAccessException $e) {
            return redirect()->back()->with('error', 'Gagal menyiapkan data untuk export.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh data export.');
        }
    }
}
