<?php

namespace App\Http\Controllers;

use App\Exports\template\PenyuluhTerdaftarTemplate;
use App\Http\Requests\FileExcelRequest;
use App\Http\Requests\PenyuluhTerdaftarRequest;
use App\Services\Interfaces\KecamatanServiceInterface;
use App\Services\Interfaces\PenyuluhTerdaftarServiceInterface;
use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ImportFailedException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class PenyuluhTerdaftarController extends Controller
{
    protected PenyuluhTerdaftarServiceInterface $penyuluhService;
    protected KecamatanServiceInterface $kecamatanService;

    public function __construct(PenyuluhTerdaftarServiceInterface $penyuluhService, KecamatanServiceInterface $kecamatanService)
    {
        $this->penyuluhService = $penyuluhService;
        $this->kecamatanService = $kecamatanService;
    }

    public function index(): View
    {
        try {
            $penyuluhs = $this->penyuluhService->getAll(true);
        } catch (DataAccessException $e) {
            $penyuluhs = collect();
            session()->flash('error', 'Gagal memuat data penyuluh terdaftar.');
        }

        return view('pages.penyuluh_terdaftar.index', compact('penyuluhs'));
    }

    public function create(): View
    {
        try {
            $kecamatans = $this->kecamatanService->getAll();
        } catch (DataAccessException $e) {
            $kecamatans = collect();
            session()->flash('error', 'Gagal memuat data kecamatan.');
        }

        return view('pages.penyuluh_terdaftar.create', compact('kecamatans'));
    }

    public function store(PenyuluhTerdaftarRequest $request): RedirectResponse
    {
        try {
            $this->penyuluhService->create($request->validated());
            return redirect()->route('penyuluh-terdaftar.index')->with('success', 'Data berhasil disimpan');
        } catch (DataAccessException $e) {
            return redirect()->back()->withInput()->with('error', 'Data gagal disimpan. Silakan coba lagi.');
        }
    }

    public function edit(string $id): View
    {
        try {
            $penyuluh = $this->penyuluhService->getById($id);
            $kecamatans = $this->kecamatanService->getAll();
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            abort(500, 'Terjadi kesalahan saat memuat data penyuluh terdaftar atau kecamatan untuk edit. Silakan coba lagi.');
        }

        return view('pages.penyuluh_terdaftar.update', compact('penyuluh', 'kecamatans'));
    }

    public function update(PenyuluhTerdaftarRequest $request, string $id): RedirectResponse
    {
        try {
            $this->penyuluhService->update($id, $request->validated());
            return redirect()->route('penyuluh-terdaftar.index')->with('success', 'Data berhasil diperbarui');
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->route('penyuluh-terdaftar.index')->with('error', 'Data gagal diperbarui. Silakan coba lagi.');
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->penyuluhService->delete($id);
            return redirect()->route('penyuluh-terdaftar.index')->with('success', 'Data berhasil dihapus');
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->route('penyuluh-terdaftar.index')->with('error', 'Data gagal dihapus. Silakan coba lagi.');
        }
    }

    public function getByKecamatanId(string $id): JsonResponse
    {
        try {
            $penyuluhs = $this->penyuluhService->getByKecamatanId($id);
            return response()->json($penyuluhs);
        } catch (DataAccessException $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat mengambil data penyuluh terdaftar.'], 500);
        } catch (ResourceNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Terjadi kesalahan tak terduga.'], 500);
        }
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(new PenyuluhTerdaftarTemplate(), 'penyuluh_terdaftar_template.xlsx');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh template.');
        }
    }

    public function import(FileExcelRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $failures = $this->penyuluhService->import($data['file']);

            if (!empty($failures)) {
                return redirect()->route('penyuluh-terdaftar.index')->with([
                    'error' => 'Data berhasil diimport, namun ada beberapa data yang gagal diproses.',
                    'failures' => $failures
                ]);
            }

            return redirect()->route('penyuluh-terdaftar.index')->with('success', 'Data berhasil diimport');

        } catch (ImportFailedException $e) {
            return redirect()->route('penyuluh-terdaftar.index')->with([
                'error' => $e->getMessage(),
                'failures' => $e->getFailures()
            ]);
        } catch (DataAccessException $e) {
            return redirect()->route('penyuluh-terdaftar.index')->with('error', 'Terjadi kesalahan database saat mengimport data.');
        } catch (\Throwable $e) {
            return redirect()->route('penyuluh-terdaftar.index')->with('error', 'Terjadi kesalahan tak terduga saat mengimport data.');
        }
    }

    public function export()
    {
        try {
            $exporter = $this->penyuluhService->export();
            return Excel::download($exporter, 'penyuluh_terdaftar.xlsx');
        } catch (DataAccessException $e) {
            return redirect()->back()->with('error', 'Gagal menyiapkan data untuk export.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh data export.');
        }
    }
}
