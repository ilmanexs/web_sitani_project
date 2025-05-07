<?php

namespace App\Http\Controllers;

use App\Exports\DesaExport;
use App\Exports\template\DesaTemplate;
use App\Http\Requests\DesaRequest;
use App\Http\Requests\FileExcelRequest;
use App\Services\Interfaces\DesaServiceInterface;
use App\Services\Interfaces\KecamatanServiceInterface;
use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ImportFailedException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class DesaController extends Controller
{
    protected DesaServiceInterface $desaService;
    protected KecamatanServiceInterface $kecamatanService;

    public function __construct(DesaServiceInterface $desaService, KecamatanServiceInterface $kecamatanService)
    {
        $this->desaService = $desaService;
        $this->kecamatanService = $kecamatanService;
    }

    public function index(): View
    {
        try {
            $desas = $this->desaService->getAll(true);
        } catch (DataAccessException $e) {
            $desas = collect();
            session()->flash('error', 'Gagal memuat data desa.');
        }

        return view('pages.desa.index', compact('desas'));
    }

    public function create(): View
    {
        try {
            $kecamatans = $this->kecamatanService->getAll();
        } catch (DataAccessException $e) {
            $kecamatans = collect();
            session()->flash('error', 'Gagal memuat data kecamatan.');
        }

        return view('pages.desa.create', compact('kecamatans'));
    }

    public function store(DesaRequest $request): RedirectResponse
    {
        try {
            $this->desaService->create($request->validated());
            return redirect()->route('desa.index')->with('success', 'Data berhasil disimpan');
        } catch (DataAccessException $e) {
            return redirect()->back()->withInput()->with('error', 'Data gagal disimpan. Silakan coba lagi.');
        }
    }

    public function edit(string $id): View
    {
        try {
            $desa = $this->desaService->getById($id);
            $kecamatans = $this->kecamatanService->getAll();
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            abort(500, 'Terjadi kesalahan saat memuat data desa atau kecamatan untuk edit. Silakan coba lagi.');
        }

        return view('pages.desa.update', compact('kecamatans', 'desa'));
    }

    public function update(DesaRequest $request, string $id): RedirectResponse
    {
        try {
            $this->desaService->update($id, $request->validated());
            return redirect()->route('desa.index')->with('success', 'Data berhasil diperbarui');
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->route('desa.index')->with('error', 'Gagal memperbarui data desa. Silakan coba lagi.');
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->desaService->delete($id);
            return redirect()->route('desa.index')->with('success', 'Data berhasil dihapus');
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->route('desa.index')->with('error', 'Data gagal dihapus. Silakan coba lagi.');
        }
    }

    public function getByKecamatanId(string $id): JsonResponse
    {
        try {
            $desas = $this->desaService->getByKecamatanId($id);
            return response()->json($desas);
        } catch (DataAccessException $e) {
            return response()->json(['message' => 'Terjadi kesalahan saat mengambil data desa.'], 500);
        } catch (ResourceNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Terjadi kesalahan tak terduga.'], 500);
        }
    }


    public function downloadTemplate()
    {
        try {
            return Excel::download(new DesaTemplate(), 'desa_template.xlsx');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh template.');
        }
    }

    public function import(FileExcelRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $failures = $this->desaService->import($data['file']);

            if (!empty($failures)) {
                return redirect()->route('desa.index')->with([
                    'error' => 'Data berhasil diimport, namun ada beberapa data yang gagal diproses.',
                    'failures' => $failures
                ]);
            }

            return redirect()->route('desa.index')->with('success', 'Data berhasil diimport');

        } catch (ImportFailedException $e) {
            return redirect()->route('desa.index')->with([
                'error' => $e->getMessage(),
                'failures' => $e->getFailures()
            ]);
        } catch (DataAccessException $e) {
            return redirect()->route('desa.index')->with('error', 'Terjadi kesalahan database saat mengimport data.');
        } catch (\Throwable $e) {
            return redirect()->route('desa.index')->with('error', 'Terjadi kesalahan tak terduga saat mengimport data.');
        }
    }

    public function export()
    {
        try {
            $exporter = $this->desaService->export();
            return Excel::download($exporter, 'desa.xlsx');
        } catch (DataAccessException $e) {
            return redirect()->back()->with('error', 'Gagal menyiapkan data untuk export.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh data export.');
        }
    }
}
