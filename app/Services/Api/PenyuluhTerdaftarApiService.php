<?php

namespace App\Services\Api;

use App\Exceptions\DataAccessException;
use App\Exceptions\ResourceNotFoundException;
use App\Repositories\Interfaces\PenyuluhTerdaftarRepositoryInterface;
use App\Services\Interfaces\PenyuluhTerdaftarApiServiceInterface;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class PenyuluhTerdaftarApiService implements PenyuluhTerdaftarApiServiceInterface
{
    protected PenyuluhTerdaftarRepositoryInterface $repository;

    public function __construct(PenyuluhTerdaftarRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     * @param string $phone
     * @return Model
     * @throws DataAccessException
     * @throws ResourceNotFoundException
     */
    public function getByPhone(string $phone): Model
    {
        try {
            $penyuluh = $this->repository->getByPhone($phone);

            if ($penyuluh === null) {
                throw new ResourceNotFoundException("Penyuluh terdaftar dengan nomor hp {$phone} tidak ditemukan.");
            }

            return $penyuluh;
        } catch (ResourceNotFoundException $e) {
            throw $e;
        } catch (QueryException $e) {
            throw new DataAccessException("Database error saat fetch data penyuluh terdaftar dengan nomor hp {$phone}.", 0, $e);
        } catch (Throwable $e) {
            throw new DataAccessException("Terjadi kesalahan tak terduga saat fetch data pernyuluh terdaftar dengan nomor hp {$phone}.", 0, $e);
        }
    }

}
