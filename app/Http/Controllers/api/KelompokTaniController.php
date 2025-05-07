<?php

namespace App\Http\Controllers\api;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\KelompokTaniResource;
use App\Services\Interfaces\KelompokTaniApiServiceInterface;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class KelompokTaniController extends Controller
{
    use ApiResponse;

    protected KelompokTaniApiServiceInterface $service;

    public function __construct(KelompokTaniApiServiceInterface $service)
    {
        $this->service = $service;
    }

    public function getAllByPenyuluhId(Request $request): JsonResponse
    {
        $penyuluhIds = $request->query('penyuluhIds');

        if (is_null($penyuluhIds)) {
            return $this->errorResponse('penyuluhIds tidak boleh kosong', Response::HTTP_BAD_REQUEST);
        }

        $ids = is_array($penyuluhIds) ? $penyuluhIds : explode(',', $penyuluhIds);
        $ids = array_filter($ids, static fn($id) => !empty($id));

        if (empty($ids)) {
            return $this->errorResponse('Tidak ada penyuluh ID yang valid diberikan.', Response::HTTP_BAD_REQUEST);
        }

        try {
            $kelompokTanis = $this->service->getAllByPenyuluhId($ids);
            return $this->successResponse(KelompokTaniResource::collection($kelompokTanis), 'Data kelompok tani ditemukan');
        } catch (DataAccessException $e) {
            return $this->errorResponse('Failed to fetch Kelompok Tani data.', 500);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan diserver.', 500);
        }
    }

    public function getById(string|int $id): JsonResponse
    {
        try {
            $kelompokTani = $this->service->getById($id);
            return $this->successResponse(new KelompokTaniResource($kelompokTani), 'Data kelompok tani ditemukan');
        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse('Kelompok tani tidak ditemukan', Response::HTTP_NOT_FOUND);
        } catch (DataAccessException $e) {
            return $this->errorResponse('Failed to fetch Kelompok Tani data.', 500);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan diserver.', 500);
        }
    }

    public function calculateTotal(): JsonResponse
    {
        try {
            $total = $this->service->getTotal();

            return $this->successResponse($total, 'Total Kelompok Tani berhasil diambil');
        } catch (DataAccessException $e) {
            return $this->errorResponse('Failed to calculate total Kelompok Tani.', 500);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan diserver.', 500);
        }
    }

    public function countByKecamatanId(string|int $id): JsonResponse
    {
        try {
            $total = $this->service->countByKecamatanId($id);

            return $this->successResponse(['total' => $total], 'Total Kelompok Tani berhasil diambil');
        } catch (DataAccessException $e) {
            return $this->errorResponse('Failed to count Kelompok Tani by Kecamatan ID.', 500);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan diserver.', 500);
        }
    }

    public function getAllByKecamatanId(Request $request, string|int $id): JsonResponse
    {
        try {
            $criteria = $request->query();
            $kelompokTanis = $this->service->getAllByKecamatanId($id, $criteria);
            return $this->successResponse(KelompokTaniResource::collection($kelompokTanis), 'Data kelompok tani ditemukan');
        } catch (DataAccessException $e) {
            return $this->errorResponse('Gagal fetch data kelompok tani.', 500);
        } catch (\Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.', 500);
        }
    }
}
