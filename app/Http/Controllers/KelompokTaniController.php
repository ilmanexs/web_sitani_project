<?php

namespace App\Http\Controllers;

use App\Exports\template\KelompokTaniTemplate;
use App\Http\Requests\FileExcelRequest;
use App\Http\Requests\KelompokTaniRequest;
use App\Services\Interfaces\KecamatanServiceInterface;
use App\Services\Interfaces\KelompokTaniServiceInterface;
use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ImportFailedException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class KelompokTaniController extends Controller
{
    protected KelompokTaniServiceInterface $kelompokTaniService;

    public function __construct(KelompokTaniServiceInterface $kelompokTaniService)
    {
        $this->kelompokTaniService = $kelompokTaniService;
    }

    public function index(): View
    {
        try {
            $kelompokTanis = $this->kelompokTaniService->getAll(true);
        } catch (DataAccessException $e) {
            $kelompokTanis = collect();
            session()->flash('error', 'Gagal memuat data Kelompok Tani.');
        } catch (Throwable $e) {
            $kelompokTanis = collect();
            session()->flash('error', 'Terjadi kesalahan tak terduga.');
        }

        return view('pages.kelompok_tani.index', compact('kelompokTanis'));
    }

    public function create(KecamatanServiceInterface $kecamatanService): View
    {
        try {
            $kecamatans = $kecamatanService->getAll();
        } catch (DataAccessException $e) {
            $kecamatans = collect();
            session()->flash('error', 'Gagal memuat data kecamatan.');
        } catch (Throwable $e) {
            $kecamatans = collect();
            session()->flash('error', 'Terjadi kesalahan tak terduga saat memuat kecamatan.');
        }

        return view('pages.kelompok_tani.create', compact('kecamatans'));
    }

    public function store(KelompokTaniRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->kelompokTaniService->create($validated);

            return redirect()->route('kelompok-tani.index')->with('success', 'Data berhasil disimpan');
        } catch (DataAccessException $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data Kelompok Tani. Silakan coba lagi.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan tak terduga saat menyimpan data.');
        }
    }

    public function edit(string $id, KecamatanServiceInterface $kecamatanService): View
    {
        try {
            $kelompokTanis = $this->kelompokTaniService->getById($id);
            $kecamatans = $kecamatanService->getAll();
        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            abort(500, 'Terjadi kesalahan saat memuat data Kelompok Tani atau kecamatan untuk edit. Silakan coba lagi.');
        } catch (Throwable $e) {
            abort(500, 'Terjadi kesalahan tak terduga.');
        }

        return view('pages.kelompok_tani.update', compact('kelompokTanis', 'kecamatans'));
    }

    public function update(KelompokTaniRequest $request, string $id): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->kelompokTaniService->update($id, $validated);

            return redirect()->route('kelompok-tani.index')->with('success', 'Data berhasil diperbarui');
        } catch (ResourceNotFoundException $e) {
            return redirect()->route('kelompok-tani.index')->with('error', $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data Kelompok Tani. Silakan coba lagi.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan tak terduga saat memperbarui data.');
        }
    }

    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->kelompokTaniService->delete($id);
            return redirect()->route('kelompok-tani.index')->with('success', 'Data berhasil dihapus');
        } catch (ResourceNotFoundException $e) {
            return redirect()->route('kelompok-tani.index')->with('error', $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->route('kelompok-tani.index')->with('error', 'Gagal menghapus data Kelompok Tani. Silakan coba lagi.');
        } catch (Throwable $e) {
            return redirect()->route('kelompok-tani.index')->with('error', 'Terjadi kesalahan tak terduga saat menghapus data.');
        }
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(new KelompokTaniTemplate(), 'kelompok_tani_template.xlsx');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh template.');
        }
    }

    public function import(FileExcelRequest $request): RedirectResponse
    {
        $request->validated();

        try {
            $this->kelompokTaniService->import($request->file('file'));

            return back()
                ->with('success', 'Data Kelompok Tani berhasil diimpor seluruhnya.');
        } catch (ImportFailedException $e) {
            $failuresCollection = $e->getFailures();

            $formattedFailures = $failuresCollection->map(function (Failure $failure) {
                return [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            })->toArray();
            return back()
                ->with('error', 'Import selesai dengan beberapa baris data bermasalah.')
                ->with('failures', $formattedFailures);
        } catch (DataAccessException $e) {
            return back()
                ->with('error', 'Terjadi kesalahan database saat mengimport data.');
        } catch (Throwable $e) {
            return back()
                ->with('error', 'Terjadi kesalahan tak terduga saat mengimpor data.');
        }
    }

    public function export()
    {
        try {
            $exporter = $this->kelompokTaniService->export();
            return Excel::download($exporter, 'kelompok_tani.xlsx');
        } catch (DataAccessException $e) {
            return redirect()->back()->with('error', 'Gagal menyiapkan data untuk export.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh data export.');
        }
    }
}
