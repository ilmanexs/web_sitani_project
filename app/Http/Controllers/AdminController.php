<?php

namespace App\Http\Controllers;

use App\Exceptions\DataAccessException;
use App\Exceptions\ImportFailedException;
use App\Exceptions\ResourceNotFoundException;
use App\Exports\template\AdminTemplate;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\FileExcelRequest;
use App\Http\Requests\ProfileRequest;
use App\Services\Interfaces\AdminServiceInterface;
use App\Services\Interfaces\RoleServiceInterface;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class AdminController extends Controller
{
    protected AdminServiceInterface $service;

    public function __construct(AdminServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        try {
            $admins = $this->service->getAll();
        } catch (DataAccessException $e) {
            $admins = collect();
            session()->flash('error', 'Gagal memuat data admin.');
        }

        return view('pages.admin.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(RoleServiceInterface $roleService): View
    {
        try {
            $roles = $roleService->getAll();
        } catch (DataAccessException $e) {
            $roles = collect();
            session()->flash('error', 'Gagal memuat data roles.');
        } catch (Throwable $e) {
            $roles = collect();
            session()->flash('error', 'Terjadi kesalahan tak terduga saat memuat roles.');
        }
        return view('pages.admin.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request): RedirectResponse
    {
        try {
            $this->service->create($request->validated());
            return redirect()->route('admin.index')->with('success', 'Data berhasil disimpan');
        } catch (DataAccessException $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data admin. Silakan coba lagi.');
        } catch (Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan tak terduga saat menyimpan data.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, RoleServiceInterface $roleService): View
    {
        try {
            $admin = $this->service->getById($id);

            $roles = $roleService->getAll();

        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            abort(500, 'Terjadi kesalahan saat memuat data admin atau roles untuk edit. Silakan coba lagi.');
        } catch (Throwable $e) {
            abort(500, 'Terjadi kesalahan tak terduga.');
        }

        return view('pages.admin.update', compact('admin', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $request, $id): RedirectResponse
    {
        $validated = $request->validated();
        try {
            $this->service->update($id, $validated);
            return redirect()->route('admin.index')->with('success', 'Data admin berhasil diperbarui');
        } catch (ResourceNotFoundException $e) {
            return redirect()->route('admin.index')->with('error', $e->getMessage());
        } catch (DataAccessException $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui data admin. Silakan coba lagi.'])->withInput();
        } catch (Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan tak terduga saat memperbarui data.'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->service->delete($id);
            return redirect()->route('admin.index')->with('success', 'Data berhasil dihapus');
        } catch (ResourceNotFoundException $e) {
            return redirect()->route('admin.index')->with('error', $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->route('admin.index')->with('error', 'Gagal menghapus data admin. Silakan coba lagi.');
        } catch (Throwable $e) {
            return redirect()->route('admin.index')->with('error', 'Terjadi kesalahan tak terduga saat menghapus data.');
        }
    }

    public function viewProfile(): View
    {
        $user = \Auth::user();
        $user->load('admin:id,user_id,nama,no_hp,alamat');
        return view('pages.profile.profile', compact('user'));
    }

    public function updateProfile(ProfileRequest $request, $id): ?RedirectResponse
    {
        $validated = $request->validated();
        try {
            $this->service->update($id, $validated);
            return redirect()->back()->with('success', 'Data profile berhasil diperbarui');
        } catch (ResourceNotFoundException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (DataAccessException $e) {
            return redirect()->back()->with('error', 'Data profile gagal diperbarui. Silakan coba lagi.');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan tak terduga saat memperbarui profile.');
        }
    }

    public function downloadTemplate()
    {
        try {
            return Excel::download(new AdminTemplate(), 'data_admin_template.xlsx');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh template.');
        }
    }

    public function import(FileExcelRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $failures = $this->service->import($data['file']);

            if (!empty($failures)) {
                return redirect()->route('admin.index')->with([
                    'error' => 'Data berhasil diimport, namun ada beberapa data yang gagal diproses.',
                    'failures' => $failures
                ]);
            }

            return redirect()->route('admin.index')->with('success', 'Data berhasil diimport');

        } catch (ImportFailedException $e) {
            return redirect()->route('admin.index')->with([
                'error' => $e->getMessage(),
                'failures' => $e->getFailures()
            ]);
        } catch (DataAccessException $e) {
            return redirect()->route('admin.index')->with('error', 'Terjadi kesalahan database saat mengimport data.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.index')->with('error', 'Terjadi kesalahan tak terduga saat mengimport data.');
        }
    }

    public function export()
    {
        try {
            $exporter = $this->service->export();
            return Excel::download($exporter, 'data_admin.xlsx');
        } catch (DataAccessException $e) {
            return redirect()->back()->with('error', 'Gagal menyiapkan data untuk export.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh data export.');
        }
    }
}
