<?php

namespace App\Http\Controllers;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Exports\template\BibitTemplate;
use App\Http\Requests\BibitRequest;
use App\Http\Requests\FileExcelRequest;
use App\Services\Interfaces\BibitServiceInterface;
use App\Services\Interfaces\KomoditasServiceInterface;
use App\Exceptions\ImportFailedException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class BibitBerkualitasController extends Controller
{
    protected BibitServiceInterface $bibitService;

    public function __construct(BibitServiceInterface $bibitService)
    {
        $this->bibitService = $bibitService;
    }

    public function index(): View
    {
        try {
            $datas = $this->bibitService->getAll(true);
        } catch (DataAccessException $e) {
            $datas = collect();
            session()->flash('error', 'Gagal memuat data bibit.');
        }
        return view('pages.bibit.index', compact('datas'));
    }

    public function create(KomoditasServiceInterface $komoditasService): View
    {
        try {
            $datas = $komoditasService->getAll();
        } catch (DataAccessException $e) {
            $datas = collect();
            session()->flash('error', 'Gagal memuat data komoditas.');
        }
        return view('pages.bibit.create', compact('datas'));
    }

    public function store(BibitRequest $request): RedirectResponse
    {
        try {
            $this->bibitService->create($request->validated());
            return redirect()->route('bibit.index')->with('success', 'Data berhasil disimpan');
        } catch (DataAccessException $e) {
            return redirect()->route('bibit.index')->with('error', 'Data gagal disimpan. Silakan coba lagi.');
        }
    }

    public function edit(string $id, KomoditasServiceInterface $komoditasService): View
    {
        try {
            $bibit = $this->bibitService->getById($id);
            $komoditas = $komoditasService->getAll();
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            abort(500, 'Terjadi kesalahan saat memuat data untuk edit. Silakan coba lagi.');
        }

        return view('pages.bibit.update', compact('bibit', 'komoditas'));
    }

    public function update(BibitRequest $request, string $id): RedirectResponse
    {
        try {
            $this->bibitService->update($id, $request->validated());
            return redirect()->route('bibit.index')->with('success', 'Data berhasil diperbarui');
        } catch (DataAccessException $e) {
            return redirect()->route('bibit.index')->with('error', 'Data gagal diperbarui. Silakan coba lagi.');
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->bibitService->delete($id);
            return redirect()->route('bibit.index')->with('success', 'Data berhasil dihapus');
        } catch (DataAccessException $e) {
            return redirect()->route('bibit.index')->with('error', 'Data gagal dihapus. Silakan coba lagi.');
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(new BibitTemplate(), 'bibit_berkualitas_template.xlsx');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh template.');
        }
    }

    public function import(FileExcelRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $failures = $this->bibitService->import($data['file']);

            if (!empty($failures)) {
                return redirect()->route('bibit.index')->with([
                    'success' => 'Data berhasil diimport, namun ada beberapa data yang gagal diproses.',
                    'failures' => $failures
                ]);
            }

            return redirect()->route('bibit.index')->with('success', 'Data berhasil diimport');

        } catch (ImportFailedException $e) {
            return redirect()->route('bibit.index')->with([
                'error' => $e->getMessage(),
                'failures' => $e->getFailures()
            ]);
        } catch (DataAccessException $e) {
            return redirect()->route('bibit.index')->with('error', 'Terjadi kesalahan database saat mengimport data.');
        } catch (\Throwable $e) {
            return redirect()->route('bibit.index')->with('error', 'Terjadi kesalahan tak terduga saat mengimport data.');
        }
    }

    public function export()
    {
        try {
            $exporter = $this->bibitService->export();
            return Excel::download($exporter, 'bibit_berkualitas.xlsx');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh data export.');
        }
    }
}
