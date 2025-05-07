<?php

namespace App\Http\Controllers\api;

use App\Exceptions\DataAccessException;
use App\Http\Controllers\Controller;
use App\Services\Interfaces\BibitApiServiceInterface;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;

class BibitController extends Controller
{
    use ApiResponse;
    protected BibitApiServiceInterface $service;

    public function __construct(BibitApiServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Mengambil seluruh data bibit berkualitas yang terdaftar
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        try {
            $datas = $this->service->getAllApi();
            return $this->successResponse($datas->toArray(), 'Data bibit berkualitas berhasil diambil');
        } catch (DataAccessException $e) {
            return $this->errorResponse('Gagal Fetch data bibit.', 500);
        } catch (\Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.', 500);
        }
    }


    /**
     * Mengambil total bibit berkualitas
     *
     * @return JsonResponse
     */
    public function calculateTotal(): JsonResponse
    {
        try {
            $total = $this->service->getTotal();
            return $this->successResponse(['total' => $total], 'Total bibit berkualitas berhasil diambil');
        } catch (DataAccessException $e) {
            return $this->errorResponse('Gagal menghitung total bibit.', 500);
        } catch (\Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.', 500);
        }
    }
}
