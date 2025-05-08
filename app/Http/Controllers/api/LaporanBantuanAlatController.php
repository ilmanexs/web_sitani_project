<?php

namespace App\Http\Controllers\api;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LaporanBantuanAlat;
use App\Http\Resources\LaporanBantuanAlatResource;
use App\Services\Interfaces\PermintaanAlatApiServiceInterface;
use App\Trait\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LaporanBantuanAlatController extends Controller
{
    use ApiResponse;

    protected PermintaanAlatApiServiceInterface $service;

    public function __construct(PermintaanAlatApiServiceInterface $service)
    {
        $this->service = $service;
    }

    public function create(LaporanBantuanAlat $request)
    {
        $validated = $request->validated();
        try {
            $laporan = $this->service->create($validated);
            return $this->successResponse(new LaporanBantuanAlatResource($laporan),'Laporan Berhasil disimpan', 201);
        } catch (DataAccessException $e) {
            return $this->errorResponse('Gagal menyimpan Laporan', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAllByKecamatanId($id)
    {
        try {
            $laporans = $this->service->getAllByKecamatanId($id);
            return $this->successResponse(LaporanBantuanAlatResource::collection($laporans), 'Laporan Bantuan Alat berhasil diambil');
        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse('Laporan Bantuan Alat tidak ditemukan', Response::HTTP_NOT_FOUND);
        } catch (DataAccessException $e) {
            return $this->errorResponse('Gagal mengambil laporan bantuan alat.', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getTotalByKecamatanId($id)
    {
        try {
            $laporans = $this->service->getTotalByKecamatanId($id);
            return $this->successResponse($laporans, 'Total laporan bantuan alat berhasil diambil');
        } catch (DataAccessException $e) {
            return $this->errorResponse('Gagal mengambil total laporan bantuan alat', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getStatsTotalByKecamatanId($id)
    {
        try {
            $total = $this->service->getStatsByKecamatanId($id);
            return $this->successResponse($total, 'Total laporan bantuan alat berhasil diambil');
        } catch (DataAccessException $e) {
            return $this->errorResponse('Gagal mengambil total laporan bantuan alat berdasarkan statusnya', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
