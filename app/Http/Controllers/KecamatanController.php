<?php

namespace App\Http\Controllers;

use App\Exports\template\KecamatanTemplate;
use App\Http\Requests\FileExcelRequest;
use App\Http\Requests\KecamatanRequest;
use App\Services\Interfaces\KecamatanServiceInterface;
use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ImportFailedException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class KecamatanController extends Controller
{
    protected KecamatanServiceInterface $kecamatanService;

    public function __construct(KecamatanServiceInterface $kecamatanService)
    {
        $this->kecamatanService = $kecamatanService;
    }

    public function index(): View
    {
        try {
            $kecamatans = $this->kecamatanService->getAll(true);
        } catch (DataAccessException $e) {
            $kecamatans = collect();
            session()->flash('error', 'Gagal memuat data kecamatan.');
        }

        return view('pages.kecamatan.index', compact('kecamatans'));
    }

    public function create(): View
    {
        return view('pages.kecamatan.create');
    }

    public function store(KecamatanRequest $request): RedirectResponse
    {
        try {
            $this->kecamatanService->create($request->validated());
            return redirect()->route('kecamatan.index')->with('success', 'Data Berhasil disimpan');
        } catch (DataAccessException $e) {
            return redirect()->back()->withInput()->with('error', 'Data Gagal disimpan. Silakan coba lagi.');
        }
    }

    public function edit(string $id): View
    {
        try {
            $kecamatan = $this->kecamatanService->getById($id);
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            abort(500, 'Terjadi kesalahan saat memuat data kecamatan untuk edit. Silakan coba lagi.');
        }

        return view('pages.kecamatan.update', compact('kecamatan'));
    }

    public function update(KecamatanRequest $request, string $id): RedirectResponse
    {
        try {
            $this->kecamatanService->update($id, $request->validated());
            return redirect()->route('kecamatan.index')->with('success', 'Data berhasil diperbarui');
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->route('kecamatan.index')->with('error', 'Data gagal diperbarui. Silakan coba lagi.');
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->kecamatanService->delete($id);
            return redirect()->route('kecamatan.index')->with('success', 'Data berhasil dihapus');
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->route('kecamatan.index')->with('error', 'Data gagal dihapus. Silakan coba lagi.');
        }
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(new KecamatanTemplate(), 'kecamatan_template.xlsx');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh template.');
        }
    }

    public function import(FileExcelRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $failures = $this->kecamatanService->import($data['file']);

            if (!empty($failures)) {
                return redirect()->route('kecamatan.index')->with([
                    'error' => 'Data berhasil diimport, namun ada beberapa data yang gagal diproses.',
                    'failures' => $failures
                ]);
            }

            return redirect()->route('kecamatan.index')->with('success', 'Data berhasil diimport');

        } catch (ImportFailedException $e) {
            return redirect()->route('kecamatan.index')->with([
                'error' => $e->getMessage(),
                'failures' => $e->getFailures()
            ]);
        } catch (DataAccessException $e) {
            return redirect()->route('kecamatan.index')->with('error', 'Terjadi kesalahan database saat mengimport data.');
        } catch (\Throwable $e) {
            return redirect()->route('kecamatan.index')->with('error', 'Terjadi kesalahan tak terduga saat mengimport data.');
        }
    }

    public function export()
    {
        try {
            $exporter = $this->kecamatanService->export();
            return Excel::download($exporter, 'kecamatan.xlsx');
        } catch (DataAccessException $e) {
            return redirect()->back()->with('error', 'Gagal menyiapkan data untuk export.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh data export.');
        }
    }
}
