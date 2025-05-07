<?php

namespace App\Http\Controllers\api;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePenyuluhTerdaftarApiRequest;
use App\Http\Resources\PenyuluhTerdaftarResource;
use App\Services\PenyuluhService;
use App\Services\PenyuluhTerdaftarService;
use App\Trait\ApiResponse;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PenyuluhController extends Controller
{
    use ApiResponse;

    protected PenyuluhService $service;
    protected PenyuluhTerdaftarService $penyuluhTerdaftarService;

    public function __construct(PenyuluhService $service, PenyuluhTerdaftarService $penyuluhTerdaftarService)
    {
        $this->service = $service;
        $this->penyuluhTerdaftarService = $penyuluhTerdaftarService;
    }

    /**
     * Memperbarui data penyuluh terdaftar, digunakan oleh api pada proses pendafataran penyuluh
     *
     * @param UpdatePenyuluhTerdaftarApiRequest $request form request
     * @param string|int $id ID Penyuluh terdaftar
     * @return JsonResponse
     */
    public function update(UpdatePenyuluhTerdaftarApiRequest $request, string|int $id): JsonResponse
    {
        $validated = $request->validated();
        try {
            $this->penyuluhTerdaftarService->update($id, [
                'nama' => $validated['nama_penyuluh'],
                'alamat' => $validated['alamat_penyuluh'],
            ]);

            $penyuluh = $this->penyuluhTerdaftarService->getById($id);
            return $this->successResponse(new PenyuluhTerdaftarResource($penyuluh), 'Data Penyuluh berhasil diperbarui');
        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse('Data penyuluh tidak ditemukan.', Response::HTTP_NOT_FOUND);
        } catch (DataAccessException) {
            return $this->errorResponse('Data penyuluh gagal diperbarui', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (QueryException|\Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
