<?php

namespace App\Providers;

use App\Repositories\AdminRepository;
use App\Repositories\BibitRepository;
use App\Repositories\DesaRepository;
use App\Repositories\Interfaces\AuthInterface;
use App\Repositories\Interfaces\Base\BaseRepositoryInterface;
use App\Repositories\Interfaces\BibitRepositoryInterface;
use App\Repositories\Interfaces\DesaRepositoryInterface;
use App\Repositories\Interfaces\KelompokTaniRepositoryInterface;
use App\Repositories\Interfaces\KomoditasRepositoryInterface;
use App\Repositories\Interfaces\LaporanBibitRepositoryInterface;
use App\Repositories\Interfaces\ManyRelationshipManagement;
use App\Repositories\Interfaces\NotificationInterface;
use App\Repositories\Interfaces\PenyuluhRepositoryInterface;
use App\Repositories\Interfaces\PenyuluhTerdaftarRepositoryInterface;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\Repositories\KecamatanRepository;
use App\Repositories\KelompokTaniRepository;
use App\Repositories\KomoditasRepository;
use App\Repositories\LaporanBibitBibitRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\PenyuluhRepository;
use App\Repositories\PenyuluhTerdaftarRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\AdminService;
use App\Services\Api\BibitApiService;
use App\Services\Api\KelompokTaniApiService;
use App\Services\Api\KomoditasApiService;
use App\Services\Api\LaporanBibitApiService;
use App\Services\Api\PenyuluhTerdaftarApiService;
use App\Services\BibitService;
use App\Services\DesaService;
use App\Services\Interfaces\AdminServiceInterface;
use App\Services\Interfaces\BibitApiServiceInterface;
use App\Services\Interfaces\BibitServiceInterface;
use App\Services\Interfaces\DesaServiceInterface;
use App\Services\Interfaces\KecamatanServiceInterface;
use App\Services\Interfaces\KelompokTaniApiServiceInterface;
use App\Services\Interfaces\KelompokTaniServiceInterface;
use App\Services\Interfaces\KomoditasApiServiceInterface;
use App\Services\Interfaces\KomoditasServiceInterface;
use App\Services\Interfaces\LaporanBibitApiServiceInterface;
use App\Services\Interfaces\LaporanBibitServiceInterface;
use App\Services\Interfaces\PenyuluhServiceInterface;
use App\Services\Interfaces\PenyuluhTerdaftarApiServiceInterface;
use App\Services\Interfaces\PenyuluhTerdaftarServiceInterface;
use App\Services\Interfaces\RoleServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\KecamatanService;
use App\Services\KelompokTaniService;
use App\Services\KomoditasService;
use App\Services\LaporanBibitService;
use App\Services\NotificationService;
use App\Services\PenyuluhService;
use App\Services\PenyuluhTerdaftarService;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Repository
        $this->app->when(KecamatanService::class)->needs(BaseRepositoryInterface::class)->give(KecamatanRepository::class);
        $this->app->when(DesaService::class)->needs(DesaRepositoryInterface::class)->give(DesaRepository::class);
        $this->app->when(KomoditasService::class)->needs(KomoditasRepositoryInterface::class)->give(KomoditasRepository::class);
        $this->app->when(BibitService::class)->needs(BibitRepositoryInterface::class)->give(BibitRepository::class);
        $this->app->when(PenyuluhTerdaftarService::class)->needs(PenyuluhTerdaftarRepositoryInterface::class)->give(PenyuluhTerdaftarRepository::class);
        $this->app->when(KelompokTaniService::class)->needs(KelompokTaniRepositoryInterface::class)->give(KelompokTaniRepository::class);
        $this->app->when(KelompokTaniService::class)->needs(ManyRelationshipManagement::class)->give(KelompokTaniRepository::class);
        $this->app->when(LaporanBibitService::class)->needs(LaporanBibitRepositoryInterface::class)->give(LaporanBibitBibitRepository::class);
        $this->app->when(AdminService::class)->needs(BaseRepositoryInterface::class)->give(AdminRepository::class);
        $this->app->when(UserService::class)->needs(AuthInterface::class)->give(UserRepository::class);
        $this->app->when(RoleService::class)->needs(RoleRepositoryInterface::class)->give(RoleRepository::class);
        $this->app->when(PenyuluhService::class)->needs(PenyuluhRepositoryInterface::class)->give(PenyuluhRepository::class);
        $this->app->when(NotificationService::class)->needs(NotificationInterface::class)->give(NotificationRepository::class);

        //Service web
        $this->app->bind(BibitServiceInterface::class, BibitService::class);
        $this->app->bind(KomoditasServiceInterface::class, KomoditasService::class);
        $this->app->bind(KecamatanServiceInterface::class, KecamatanService::class);
        $this->app->bind(DesaServiceInterface::class, DesaService::class);
        $this->app->bind(PenyuluhTerdaftarServiceInterface::class, PenyuluhTerdaftarService::class);
        $this->app->bind(AdminServiceInterface::class, AdminService::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(KelompokTaniServiceInterface::class, KelompokTaniService::class);
        $this->app->bind(LaporanBibitServiceInterface::class, LaporanBibitService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);

        //Service API
        // Bibit
        $this->app->bind(BibitApiServiceInterface::class, BibitApiService::class);
        $this->app->when(BibitApiService::class)->needs(BibitRepositoryInterface::class)->give(BibitRepository::class);
        // Komoditas
        $this->app->bind(KomoditasApiServiceInterface::class, KomoditasApiService::class);
        $this->app->when(KomoditasApiService::class)->needs(KomoditasRepositoryInterface::class)->give(KomoditasRepository::class);
        // Penyuluh Terdaftar
        $this->app->bind(PenyuluhTerdaftarApiServiceInterface::class, PenyuluhTerdaftarApiService::class);
        $this->app->when(PenyuluhTerdaftarApiService::class)->needs(PenyuluhTerdaftarRepositoryInterface::class)->give(PenyuluhTerdaftarRepository::class);
        // Kelompok Tani
        $this->app->bind(KelompokTaniApiServiceInterface::class, KelompokTaniApiService::class);
        $this->app->when(KelompokTaniApiService::class)->needs(KelompokTaniRepositoryInterface::class)->give(KelompokTaniRepository::class);
        // Laporan Bibit
        $this->app->bind(LaporanBibitApiServiceInterface::class, LaporanBibitApiService::class);
        $this->app->when(LaporanBibitApiService::class)->needs(LaporanBibitRepositoryInterface::class)->give(LaporanBibitBibitRepository::class);
        // User
        $this->app->bind(UserServiceInterface::class, UserService::class);
        //Penyuluh
        $this->app->bind(PenyuluhServiceInterface::class, PenyuluhService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (\App::environment('production')){
            \URL::forceScheme('https');
        }
    }
}
