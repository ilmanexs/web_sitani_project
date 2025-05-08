<aside id="with-navbar-sidebar"
       class="overlay [--auto-close:sm] sm:shadow-none overlay-open:translate-x-0 drawer drawer-start hidden max-w-64 sm:absolute sm:z-0 sm:flex sm:translate-x-0 pt-16"
       role="dialog" tabindex="-1">
    <div class="drawer-body px-2 pt-4">
        <ul class="menu p-0">
            @can('akses-panel.Akses ke Panel Admin')
                <li>
                    <a href="{{ route('dashboard.admin') }}">
                        <span class="icon-[tabler--home] size-5"></span>
                        Beranda
                    </a>
                </li>
            @endcan

            @canany(['bibit.lihat', 'komoditas.lihat', 'penyuluh.lihat', 'kelompok-tani.lihat'])
                <li class="space-y-0.5">
                    <a class="collapse-toggle collapse-open:bg-base-content/10" id="menu-data-pertanian"
                       data-collapse="#menu-data-pertanian-collapse">
                        <span class="icon-[ph--farm] size-5"></span>
                        Data Pertanian
                        <span
                            class="icon-[tabler--chevron-down] collapse-open:rotate-180 size-4 transition-all duration-300"></span>
                    </a>
                    <ul id="menu-data-pertanian-collapse"
                        class="collapse hidden w-auto space-y-0.5 overflow-hidden transition-[height] duration-300"
                        aria-labelledby="menu-data-pertanian">
                        @can('bibit.lihat')
                            <li>
                                <a href="{{ route('bibit.index') }}">
                                    <span class="icon-[flowbite--seedling-outline] size-5"></span>
                                    Bibit
                                </a>
                            </li>
                        @endcan
                        @can('komoditas.lihat')
                            <li>
                                <a href="{{ route('komoditas.index') }}">
                                    <span class="icon-[icon-park-outline--commodity] size-5"></span>
                                    Komoditas
                                </a>
                            </li>
                        @endcan
                        @can('penyuluh.lihat')
                            <li>
                                <a href="{{ route('penyuluh-terdaftar.index') }}">
                                    <span class="icon-[ph--user] size-5"></span>
                                    Penyuluh
                                </a>
                            </li>
                        @endcan
                        @can('kelompok-tani.lihat')
                            <li>
                                <a href="{{ route('kelompok-tani.index') }}">
                                    <span class="icon-[fluent--group-24-regular] size-5"></span>
                                    Kelompok Tani
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @canany(['desa.lihat', 'kecamatan.lihat'])
                <li class="space-y-0.5">
                    <a class="collapse-toggle collapse-open:bg-base-content/10" id="menu-data-wilayah"
                       data-collapse="#menu-data-wilayah-collapse">
                        <span class="icon-[oui--vis-map-region] size-5"></span>
                        Data Wilayah
                        <span
                            class="icon-[tabler--chevron-down] collapse-open:rotate-180 size-4 transition-all duration-300"></span>
                    </a>
                    <ul id="menu-data-wilayah-collapse"
                        class="collapse hidden w-auto space-y-0.5 overflow-hidden transition-[height] duration-300"
                        aria-labelledby="menu-data-wilayah">
                        @can('desa.lihat')
                            <li>
                                <a href="{{ route('desa.index') }}">
                                    <span class="icon-[healthicons--village-outline-24px] size-5"></span>
                                    Desa
                                </a>
                            </li>
                        @endcan
                        @can('kecamatan.lihat')
                            <li>
                                <a href="{{ route('kecamatan.index') }}">
                                    <span class="icon-[material-symbols--holiday-village] size-5"></span>
                                    Kecamatan
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @canany(['laporan-bibit.lihat', 'laporan-hibah.lihat'])
                <li class="space-y-0.5">
                    <a class="collapse-toggle collapse-open:bg-base-content/10" id="menu-data-laporan"
                       data-collapse="#menu-data-laporan-collapse">
                        <span class="icon-[icon-park-outline--table-report] size-5"></span>
                        Data Laporan
                        <span
                            class="icon-[tabler--chevron-down] collapse-open:rotate-180 size-4 transition-all duration-300"></span>
                    </a>
                    <ul id="menu-data-laporan-collapse"
                        class="collapse hidden w-auto space-y-0.5 overflow-hidden transition-[height] duration-300"
                        aria-labelledby="menu-data-laporan">
                        @can('laporan-bibit.lihat')
                            <li>
                                <a href="{{ route('laporan-bibit.index') }}">
                                    <span class="icon-[mdi--seed-outline] size-5"></span>
                                    Laporan Bibit
                                </a>
                            </li>
                        @endcan
                        @can('laporan-hibah.lihat')
                            <li>
                                <a href="{{ route('laporan-alat.index') }}">
                                    <span class="icon-[mdi--donation-outline] size-5"></span>
                                    Laporan Hibah
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @can('admin.lihat')
                <li>
                    <a href="{{ route('admin.index') }}">
                        <span class="icon-[iconoir--user-crown] size-5"></span>
                        Data Admin
                    </a>
                </li>
            @endcan
            @can('role-permission.lihat')
                <li>
                    <a href="{{ route('admin.roles.index') }}">
                        <span class="icon-[fluent-mdl2--permissions] size-5"></span>
                        Role Permissions
                    </a>
                </li>
            @endcan
        </ul>
    </div>
</aside>
