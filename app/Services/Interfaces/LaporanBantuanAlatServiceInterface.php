<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\Base\BaseServiceInterface;

interface LaporanBantuanAlatServiceInterface extends BaseServiceInterface
{
    /**
     * Export data laporan permintaan hibah alat dalam bentuk excel
     *
     * @return mixed
     */
    public function export();

}
