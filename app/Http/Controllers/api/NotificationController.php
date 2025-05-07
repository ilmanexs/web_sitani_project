<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FcmRequest;
use App\Services\Interfaces\NotificationServiceInterface;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use ApiResponse;

    protected NotificationServiceInterface $service;

    public function __construct(NotificationServiceInterface $service)
    {
        $this->service = $service;
    }

    public function storeFcmToken(FcmRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = Auth::user();

        if ($user) {
            try {
                $user->fcm_token = $validated['fcm_token'];
                $user->save();
                return $this->successResponse('', 'FCM token berhasil disimpan');
            } catch (\Exception $e) {
                return $this->errorResponse('FCM token gagal disimpan', 500);
            }
        }
        return $this->errorResponse('Unauthorized', 401);
    }

    public function getUserNotification(Request $request): ?JsonResponse
    {
        try {
            $notifications = $this->service->getUserNotification($request->user());
            return $this->successResponse($notifications, 'Data notifikasi berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }

    public function markAsReadNotification(Request $request, string|int $id): ?JsonResponse
    {
        try {
            $result = $this->service->markAsRead($request->user(), $id);
            return $this->successResponse(['is_read' => $result], 'Notifikasi telah ditandai sudah dibaca');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }

    public function destroy(Request $request, string|int $id): ?JsonResponse
    {
        try {
            $result = $this->service->deleteNotification($request->user(), $id);
            return $this->successResponse(['is_deleted' => $result], 'Notifikasi berhasil dihapus');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getCode());
        }
    }
}
