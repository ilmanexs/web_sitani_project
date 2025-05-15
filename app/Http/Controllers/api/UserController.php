<?php

namespace App\Http\Controllers\api;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\Interfaces\UserServiceInterface;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ApiResponse;

    protected UserServiceInterface $service;

    public function __construct(UserServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Mengambil data pengguna berdasarkan id
     *
     * @param string|int $id Id pengguna
     * @return JsonResponse
     */
    public function getProfile(string|int $id): JsonResponse
    {
        try {
            $user = $this->service->findUser(['id' => $id], [
                'penyuluh' => [
                    'id', 'user_id', 'penyuluh_terdaftar_id'
                ],
                'penyuluh.penyuluhTerdaftar' => [
                    'id', 'nama', 'no_hp', 'alamat', 'kecamatan_id',
                ],
                'penyuluh.penyuluhTerdaftar.kecamatan' => [
                    'id', 'nama',
                ]
            ]);

            if ($user && !$user->hasRole('penyuluh')) {
                return $this->errorResponse('Akun anda tidak terdaftar sebagai penyuluh', JsonResponse::HTTP_UNAUTHORIZED);
            }
            return $this->successResponse(new UserResource($user), 'Data Profil berhasil diambil');
        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse('User tidak ditemukan', 404);
        } catch (DataAccessException $e) {
            return $this->errorResponse('Terjadi kesalahan di di server');
        }
    }
}
