<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\Interfaces\RoleServiceInterface;
use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Throwable;

class RoleController extends Controller
{
    protected RoleServiceInterface $service;

    public function __construct(RoleServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(): View
    {
        try {
            $roles = $this->service->getAll(true);
        } catch (DataAccessException $e) {
            $roles = collect();
            session()->flash('error', 'Gagal memuat data role.');
        } catch (Throwable $e) {
            $roles = collect();
            session()->flash('error', 'Terjadi kesalahan tak terduga.');
        }

        return view('pages.role.index', compact('roles'));
    }

    public function create(): View
    {
        try {
            $permissions = Permission::all();
        } catch (Throwable $e) {
            $permissions = collect();
            session()->flash('error', 'Gagal memuat daftar permissions.');
        }

        return view('pages.role.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->service->createWithPermissions([
                'name' => $validated['name'],
                'guard_name' => 'web',
            ], $validated['permissions'] ?? []);


            return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dibuat');
        } catch (DataAccessException $e) {
            return back()->withErrors(['error' => $e->getMessage() ?? 'Gagal membuat role.'])->withInput();
        } catch (Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan tak terduga saat membuat role.'])->withInput();
        }
    }

    public function edit($id): View
    {
        try {
            $role = $this->service->getById($id);

            $permissions = Permission::all();

        } catch (ResourceNotFoundException $e) {
            abort(404, $e->getMessage());
        } catch (DataAccessException $e) {
            abort(500, 'Terjadi kesalahan saat memuat data role atau permissions untuk edit. Silakan coba lagi.');
        } catch (Throwable $e) {
            abort(500, 'Terjadi kesalahan tak terduga.');
        }

        return view('pages.role.update', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, $id): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->service->updateRoleAndPermissions($id, [
                'name' => $validated['name'],
                'guard_name' => 'web',
            ], $validated['permissions'] ?? []);


            return redirect()->route('admin.roles.index')->with('success', 'Role berhasil diupdate');
        } catch (ResourceNotFoundException $e) {
            return redirect()->route('admin.roles.index')->with('error', $e->getMessage());
        } catch (DataAccessException $e) {
            return back()->withErrors(['error' => $e->getMessage() ?? 'Gagal memperbarui role.'])->withInput();
        } catch (Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan tak terduga saat memperbarui role.'])->withInput();
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $this->service->delete($id);

            return redirect()->route('admin.roles.index')->with('success', 'Role berhasil dihapus');
        } catch (ResourceNotFoundException $e) {
            return redirect()->route('admin.roles.index')->with('error', $e->getMessage());
        } catch (DataAccessException $e) {
            return back()->withErrors(['error' => $e->getMessage() ?? 'Gagal menghapus role.']);
        } catch (Throwable $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan tak terduga saat menghapus role.']);
        }
    }
}
