<?php

namespace App\Services;

use App\Events\OtpGenerated;
use App\Exceptions\DataAccessException;
use App\Exceptions\InvalidOtpException;
use App\Exceptions\ResourceNotFoundException;
use App\Models\User;
use App\Repositories\Interfaces\AuthInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Trait\LoggingError;
use Exception;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;


class UserService implements UserServiceInterface
{
    use LoggingError;

    protected AuthInterface $repository;

    public function __construct(AuthInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     * @param array $conditions
     * @param array $relations
     * @return User|null
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function findUser(array $conditions, array $relations = []): ?User
    {
        try {
            $user = $this->repository->findUser($conditions, $relations);
            if ($user === null) {
                throw new ResourceNotFoundException('User tidak ditemukan');
            }
            return $user;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat mencari data pengguna.', 0, $e);
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new DataAccessException('Terjadi kesalahan tak terduga saat mencari data pengguna.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param User $user
     * @param string $password
     * @return bool
     * @throws DataAccessException
     */
    public function resetPassword(User $user, string $password): bool
    {
        try {
            $result = $this->repository->resetPassword($user, bcrypt($password));

            if (!$result) {
                throw new DataAccessException('Gagal menyimpan kata sandi pengguna di repository.');
            }

            return true;
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['user_id' => $user->id]);
            throw new DataAccessException('Database error saat memperbarui kata sandi pengguna.', 0, $e);
        } catch (DataAccessException $e) {
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['user_id' => $user->id]);
            throw new DataAccessException('Terjadi kesalahan tak terduga saat memperbarui kata sandi pengguna', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param User $user
     * @return string
     * @throws DataAccessException
     */
    public function sendOtpToEmail(User $user): string
    {
        try {
            $code = $this->repository->generateAndSaveOtp($user);
            event(new OtpGenerated($user, $code));
            return $code;
        } catch (QueryException $e) {
            throw new DataAccessException('Database error saat generate dan kirim kode OTP', 0, $e);
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['user_id' => $user->id]);
            throw new DataAccessException('Terjadi kesalahan tak terduga saat generate dan kirim kode OTP', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param User $user
     * @param array $data
     * @return bool
     * @throws DataAccessException
     * @throws InvalidOtpException
     */
    public function verifyOtp(User $user, array $data): bool
    {
        try {

            $code = implode('', $data['otp']);

            $result = $this->repository->validateOtp($user, $code);

            if (!$result) {
                throw new InvalidOtpException('Kode OTP tidak valid.', Response::HTTP_UNAUTHORIZED);
            }
            $this->invalidateOtps($user);
            return true;
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['user_id' => $user->id]);
            throw new DataAccessException('Database error saat memvalidasi kode OTP', 0, $e);
        } catch (Exception $e) {
            $this->LogGeneralException($e, ['user_id' => $user->id]);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['user_id' => $user->id]);
            throw new DataAccessException('Terjadi kesalahan tak terduga saat memvalidasi kode OTP', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param User $user
     * @return void
     * @throws DataAccessException
     */
    public function invalidateOtps(User $user): void
    {
        try {
            $this->repository->invalidateOtps($user);
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['user_id' => $user->id]);
            throw new DataAccessException('Database error saat menghapus kode OTP', 0, $e);
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['user_id' => $user->id]);
            throw new DataAccessException('Terjadi kesalahan tak terduga saat menghapus kode OTP', 0, $e);
        }
    }

    /**
     * @inheritDoc
     * @param string $email
     * @param string $newPassword
     * @param bool $invalidateOtp
     * @return bool
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function processPasswordResetFlow(string $email, string $newPassword, bool $invalidateOtp = false): bool
    {
        try {
            $user = $this->findUser(['email' => $email]);

            if ($invalidateOtp) {
                $this->invalidateOtps($user);
            }
            return $this->resetPassword($user, $newPassword);

        } catch (ResourceNotFoundException $e) {
            $this->LogNotFoundException($e, ['email' => $email], 'User tidak ditemukan untuk reset password flow.');
            throw $e;
        } catch (DataAccessException $e) {
            $this->LogGeneralException($e, ['email' => $email], 'Terjadi kesalahan data pada proses reset password flow.');
            throw new DataAccessException('Terjadi kesalahan pada proses reset password.');
        } catch (Throwable $e) {
            $this->LogGeneralException($e, ['email' => $email], 'Terjadi kesalahan tak terduga pada proses reset password flow.');
            throw new DataAccessException('Terjadi kesalahan tak terduga pada proses reset password.');
        }
    }
}
