<?php

namespace App\Http\Controllers\api;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\KomoditasResource;
use App\Services\Interfaces\KomoditasApiServiceInterface;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KomoditasController extends Controller
{
    use ApiResponse;

    protected KomoditasApiServiceInterface $service;

    public function __construct(KomoditasApiServiceInterface $service)
    {
        $this->service = $service;
    }

    public function getAll(): JsonResponse
    {
        try {
            $datas = $this->service->getAll();
            return $this->successResponse($datas->toArray(), 'Data komoditas berhasil diambil');
        } catch (DataAccessException $e) {
            return $this->errorResponse('Gagal fetch data komoditas.', 500);
        } catch (\Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.', 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $komoditas = $this->service->getById($id);
            return $this->successResponse($komoditas->toArray(), 'Data komoditas berhasil diambil');
        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (DataAccessException $e) {
            return $this->errorResponse('Gagal fetch data komoditas.', 500);
        } catch (\Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.', 500);
        }
    }


    public function getMusim(): JsonResponse
    {
        try {
            $musimData = $this->service->GetMusim();
            return $this->successResponse($musimData->toArray(), 'Data musim komoditas berhasil diambil');
        } catch (DataAccessException $e) {
            return $this->errorResponse('Gagal fetch data musim tiap komoditas.', 500);
        } catch (\Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server', 500);
        }
    }

    public function calculateTotal(): JsonResponse
    {
        try {
            $total = $this->service->getTotal();
            return $this->successResponse(['total' => $total], 'Total komoditas berhasil diambil');
        } catch (DataAccessException $e) {
            return $this->errorResponse('Gagal menghitung total komoditas.', 500);
        } catch (\Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.', 500);
        }
    }

    public function getAllWithBibit(Request $request): JsonResponse
    {
        try {
            $criteria = $request->query();
            $komoditas = $this->service->getAll(true, $criteria);
            return $this->successResponse(KomoditasResource::collection($komoditas), 'Data komoditas berhasil diambil');
        } catch (DataAccessException $e) {
            return $this->errorResponse('Gagal fetch data komoditas.', 500);
        } catch (\Throwable $e) {
            Log::info($e->getMessage());
            return $this->errorResponse('Terjadi kesalahan di server.', 500);
        }
    }
}
