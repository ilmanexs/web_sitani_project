<?php

namespace App\Http\Controllers;

use App\Repositories\LaporanBantuanAlatRepository;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected DashboardService $service;
    protected LaporanBantuanAlatRepository $alatRepo;

    public function __construct(DashboardService $service , LaporanBantuanAlatRepository $alatRepo)
    {
        $this->service = $service;
        $this->alatRepo = $alatRepo;
    }

    public function index(Request $request)
    {
        $stats = $this->service->getStats();
        $totalBibit = $stats['total_bibit'];
        $totalKomoditas = $stats['total_komoditas'];
        $totalPenyuluhTerdaftar = $stats['total_penyuluh_terdaftar'];
        $totalKelompokTani = $stats['total_kelompok_tani'];
        $totalLapBibit = $stats['total_lap_bibit'];
        $percBelumPakaiApp = $stats['percent_penyuluh_belum_pakai_app'];
        $percPakaiApp = $stats['percent_penyuluh_pakai_app'];
        $statsBibit = $stats['stats_bibit'];

        $selectedYear = now()->year;

        $years = $this->alatRepo->getTahunTersedia();
        $totalPermintaanHibah = $this->alatRepo->countPermintaanHibah($selectedYear) ?? 0;
        $totalDiterima = $this->alatRepo->countDiterima($selectedYear) ?? 0;
        $totalDitolak = $this->alatRepo->countDitolak($selectedYear) ?? 0;

        return view('pages.dashboard', compact(
            'totalBibit', 'totalKomoditas', 'totalPenyuluhTerdaftar', 'totalKelompokTani',
            'totalLapBibit', 'percBelumPakaiApp', 'percPakaiApp', 'statsBibit',
            'totalPermintaanHibah', 'totalDiterima', 'totalDitolak', 'selectedYear', 'years'
        ));
    }
}
