<?php

namespace App\Observers;

use App\Models\LaporanKondisi;
use App\Models\LaporanKondisiDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LaporanBibitObserver
{
    /**
     * Handle the LaporanKondisi "created" event.
     */
    public function created(LaporanKondisi $laporanKondisi): void
    {

    }

    /**
     * Handle the LaporanKondisi "updated" event.
     */
    public function updated(LaporanKondisi $laporanKondisi): void
    {
        //
    }

    /**
     * Handle the LaporanKondisi "deleted" event.
     */
    public function deleted(LaporanKondisi $laporanKondisi): void
    {

    }

    /**
     * Handle the LaporanKondisi "restored" event.
     */
    public function restored(LaporanKondisi $laporanKondisi): void
    {
        //
    }

    /**
     * Handle the LaporanKondisi "force deleted" event.
     */
    public function forceDeleted(LaporanKondisi $laporanKondisi): void
    {
        //
    }
}
