<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\DataAccessException;
use App\Exceptions\InvalidOtpException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\OtpCodeRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\PenyuluhRequest;
use App\Http\Requests\PhonePenyuluhRequest;
use App\Http\Resources\UserLoginResource;
use App\Services\Api\PenyuluhTerdaftarApiService;
use App\Services\Interfaces\PenyuluhServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class AuthApiController extends Controller
{
    use ApiResponse;

    protected PenyuluhServiceInterface $penyuluhService;
    protected PenyuluhTerdaftarApiService $penyuluhTerdaftarService;
    protected UserServiceInterface $userService;

    public function __construct(
        PenyuluhServiceInterface    $penyuluhService,
        PenyuluhTerdaftarApiService $penyuluhTerdaftarService,
        UserServiceInterface        $userService
    )
    {
        $this->penyuluhService = $penyuluhService;
        $this->penyuluhTerdaftarService = $penyuluhTerdaftarService;
        $this->userService = $userService;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse('Email atau password salah', Response::HTTP_UNAUTHORIZED);
            }

            $user = Auth::user();

            if (!$user || !$user->hasRole('penyuluh')) {
                JWTAuth::invalidate($token);
                return $this->errorResponse('Akun ini tidak memiliki izin untuk login sebagai penyuluh.', Response::HTTP_FORBIDDEN);
            }

            return $this->respondWithToken($token, "Login Berhasil");

        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server. Silakan coba lagi.', 500);
        }
    }

    public function validatePhone(PhonePenyuluhRequest $request): JsonResponse
    {
        $phone = $request->validated('no_hp');

        try {
            $penyuluhTerdaftar = $this->penyuluhTerdaftarService->getByPhone($phone);

            $exists = $this->penyuluhService->existsByPenyuluhTerdaftarId($penyuluhTerdaftar->id);
            if ($exists) {
                return $this->errorResponse('Nomor telah terdaftar, silahkan login,',  Response::HTTP_CONFLICT);
            }

            return $this->successResponse($penyuluhTerdaftar, 'Nomor HP terdaftar sebagai penyuluh.');

        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: Response::HTTP_NOT_FOUND);
        } catch (DataAccessException $e) {
            return $this->errorResponse('Terjadi kesalahan di server.', $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.');
        }
    }

    public function register(PenyuluhRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $penyuluh = $this->penyuluhService->create($data);
            return $this->successResponse([
                'id' => $penyuluh->user_id,
                'email' => $penyuluh->user->email,
                'created_at' => $penyuluh->created_at,
            ], 'Registrasi berhasil', Response::HTTP_CREATED);

        } catch (DataAccessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.');
        }
    }

    protected function respondWithToken(string $token, string $message): JsonResponse
    {
        $user = Auth::user()->load([
            'penyuluh:id,user_id,penyuluh_terdaftar_id',
            'penyuluh.penyuluhTerdaftar:id,nama,no_hp,alamat,kecamatan_id',
            'penyuluh.penyuluhTerdaftar.kecamatan:id,nama',
        ]);

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTFactory::getTTL() * 60,
            'user' => new UserLoginResource($user),
        ], $message);
    }

    public function sendOtp(EmailRequest $request): JsonResponse
    {
        $email = $request->validated('email');

        try {
            $user = $this->userService->findUser(['email' => $email]);

            if (!$user->hasRole('penyuluh')) {
                return $this->errorResponse("Anda tidak memiliki akses, Gunakan akun penyuluh untuk masuk", 403);
            }

            $this->userService->sendOtpToEmail($user);

            return $this->successResponse([
                'id' => $user->id,
                'email' => $email,
            ], 'Kode OTP berhasil dikirim.');

        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse("Pengguna tidak ditemukan", Response::HTTP_NOT_FOUND);
        } catch (DataAccessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan tak terduga saat mengirim OTP.');
        }
    }

    public function validateOtp(OtpCodeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $email = $request->input('email');

        try {
            $user = $this->userService->findUser(['email' => $email]);

            $this->userService->verifyOtp($user, $validated);

            return $this->successResponse(['email' => $email], 'Kode OTP valid.', Response::HTTP_ACCEPTED);

        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse('Pengguna tidak ditemukan.', $e->getCode() ?: Response::HTTP_NOT_FOUND);
        } catch (InvalidOtpException $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (DataAccessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server.');
        }
    }

    public function passwordReset(PasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $email = $request->input('email');
        $newPassword = $validated['password'];

        try {
            $this->userService->processPasswordResetFlow(
                $email,
                $newPassword,
                true
            );

            return $this->successResponse(['email' => $email], 'Password berhasil direset.');

        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse('Pengguna tidak ditemukan.', $e->getCode() ?: Response::HTTP_NOT_FOUND);
        } catch (InvalidOtpException $e) {
            return $this->errorResponse($e->getMessage(), 401);
        } catch (DataAccessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan tak terduga saat reset password.');
        }
    }

    public function updatePasswordViaProfile(PasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $email = $request->input('email');
        try {
            $this->userService->findUser(['email' => $email]);

            $newPassword = $validated['password'];
            $this->userService->processPasswordResetFlow(
                $email,
                $newPassword,
                false
            );

            return $this->successResponse(['email' => $email], 'Password berhasil diperbarui.');

        } catch (ResourceNotFoundException $e) {
            return $this->errorResponse('Pengguna tidak ditemukan.', $e->getCode() ?: Response::HTTP_NOT_FOUND);
        } catch (DataAccessException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Throwable $e) {
            return $this->errorResponse('Terjadi kesalahan di server .');
        }
    }
}
