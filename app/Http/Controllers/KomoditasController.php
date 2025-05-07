<?php

namespace App\Http\Controllers;

use App\Exceptions\DataAccessException;
use App\Exceptions\ImportFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Requests\FileExcelRequest;
use App\Http\Requests\KomoditasRequest;
use App\Services\Interfaces\KomoditasServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class KomoditasController extends Controller
{

    protected KomoditasServiceInterface $komoditasService;

    public function __construct(KomoditasServiceInterface $komoditasService)
    {
        $this->komoditasService = $komoditasService;
    }

    public function index(): View
    {
        try {
            $datas = $this->komoditasService->getAll();
        } catch (DataAccessException $e) {
            $datas = collect();
            session()->flash('error', 'Gagal memuat data komoditas.');
        }

        return view('pages.komoditas.index', compact('datas'));
    }

    public function create(): View
    {
        return view('pages.komoditas.create');
    }

    public function store(KomoditasRequest $request): RedirectResponse
    {
        try {
            $this->komoditasService->create($request->validated());
            return redirect()->route('komoditas.index')->with('success', 'Data berhasil disimpan');
        } catch (DataAccessException $e) {
            return redirect()->route('komoditas.index')->with('error', 'Data gagal disimpan. Silakan coba lagi.');
        }
    }

    public function edit(string $id): View
    {
        try {
            $komoditas = $this->komoditasService->getById($id);
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            abort(500, 'Terjadi kesalahan saat memuat data komoditas untuk edit. Silakan coba lagi.');
        }

        return view('pages.komoditas.update', compact('komoditas'));
    }

    public function update(KomoditasRequest $request, string $id): RedirectResponse
    {
        try {
            $this->komoditasService->update($id, $request->validated());
            return redirect()->route('komoditas.index')->with('success', 'Data berhasil diperbarui');
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->route('komoditas.index')->with('error', 'Data gagal diperbarui. Silakan coba lagi.');
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->komoditasService->delete($id);
            return redirect()->route('komoditas.index')->with('success', 'Data berhasil dihapus');
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->route('komoditas.index')->with('error', 'Data Gagal dihapus. Silakan coba lagi.');
        }
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(new \App\Exports\template\KomoditasTemplate(), 'komoditas_template.xlsx');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh template.');
        }
    }

    public function import(FileExcelRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $failures = $this->komoditasService->import($data['file']);

            if (!empty($failures)) {
                return redirect()->route('komoditas.index')->with([
                    'error' => 'Data berhasil diimport, namun ada beberapa data yang gagal diproses.',
                    'failures' => $failures
                ]);
            }

            return redirect()->route('komoditas.index')->with('success', 'Data berhasil diimport');

        } catch (ImportFailedException $e) {
            return redirect()->route('komoditas.index')->with([
                'error' => $e->getMessage(),
                'failures' => $e->getFailures()
            ]);
        } catch (DataAccessException $e) {
            return redirect()->route('komoditas.index')->with('error', 'Terjadi kesalahan database saat mengimport data.');
        } catch (\Throwable $e) {
            return redirect()->route('komoditas.index')->with('error', 'Terjadi kesalahan tak terduga saat mengimport data.');
        }
    }

    public function export()
    {
        try {
            $exporter = $this->komoditasService->export();
            return Excel::download($exporter, 'komoditas.xlsx');
        } catch (DataAccessException $e) {
            return redirect()->back()->with('error', 'Gagal menyiapkan data untuk export.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh data export.');
        }
    }
}
