<?php

namespace App\Repositories;

use App\Exceptions\DataAccessException;
use App\Models\OtpCode;
use App\Models\User;
use App\Repositories\Interfaces\AuthInterface;
use App\Trait\LoggingError;
use Illuminate\Database\QueryException;
use Random\RandomException;
use Throwable;

class UserRepository implements AuthInterface
{
    use LoggingError;

    /**
     * @inheritDoc
     * @param array $conditions
     * @param array $withRelations
     * @return User|null
     * @throws Throwable
     */
    public function findUser(array $conditions, array $withRelations = []): ?User
    {
        try {
            $query = User::query();

            foreach ($conditions as $field => $value) {
                $query->where($field, $value);
            }

            $query->select(['id', 'email', 'created_at', 'is_password_set']);

            if (!empty($withRelations)) {
                foreach ($withRelations as $relation => $columns) {
                    $query->with([$relation => function ($q) use ($columns) {
                        $q->select($columns);
                    }]);
                }
            }

            return $query->first();
        } catch (QueryException $e) {
            $this->LogSqlException($e, $conditions);
            throw $e;
        } catch (Throwable $e) {
            $this->LogGeneralException($e, $conditions);
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @param User $user
     * @param string $password
     * @return bool
     * @throws Throwable
     */
    public function resetPassword(User $user, string $password): bool
    {
        try {
            $user->password = $password;
            $user->is_password_set = true;
            $result = $user->save();
            if (!$result) {
                $this->LogGeneralException(new \Exception('password gagal disimpan'));
            }
            return $result;
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['user_id' => $user->id]);
            throw $e;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @param User $user
     * @return string
     * @throws DataAccessException
     * @throws Throwable
     * @throws RandomException
     */
    public function generateAndSaveOtp(User $user): string
    {
        try {
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $this->invalidateOtps($user);

            $otp = OtpCode::create([
                'user_id'    => $user->id,
                'code'       => $code,
                'expires_at' => now()->addMinutes((int) config('otp.expires_in_minutes')),
            ]);

            if (!$otp) {
                throw new DataAccessException('Gagal generate OTP');
            }

            return $code;
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['user_id' => $user->id]);
            throw $e;
        } catch (DataAccessException|Throwable $e) {
          throw $e;
        }
    }

    /**
     * @inheritDoc
     * @param User $user
     * @param string $code
     * @return bool
     * @throws Throwable
     */
    public function validateOtp(User $user, string $code): bool
    {
        try {
            $otp = OtpCode::where('user_id', $user->id)
                ->where('code', $code)
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            return $otp !== null;
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['user_id' => $user->id]);
            throw $e;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @param User $user
     * @return void
     * @throws Throwable
     */
    public function invalidateOtps(User $user): void
    {
        try {
            OtpCode::where('user_id', $user->id)->delete();
        } catch (QueryException $e) {
            $this->LogSqlException($e, ['user_id' => $user->id]);
            throw $e;
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
