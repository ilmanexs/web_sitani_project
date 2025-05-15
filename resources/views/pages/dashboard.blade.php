@extends('layouts.layout')
@section('title', 'Dashboard | Sitani')
@section('content')
    <div class="w-full h-fit flex flex-col gap-2.5 mb-2.5">
        <div class="stats-card-container grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
            <x-ui.card-stats :title="'Total Bibit Berkualitas'" :stats="$totalBibit"
                             :description="'Bibit unggul yang terdaftar di Sitani'"
                             :icon="'icon-[pepicons-pencil--seedling]'" :icon-color="'text-bg-soft-success'"/>
            <x-ui.card-stats :title="'Total Komoditas'" :stats="$totalKomoditas"
                             :description="'Komoditas yang terdaftar di Sitani'"
                             :icon="'icon-[icon-park-outline--commodity]'" :icon-color="'text-bg-soft-secondary'"/>
            <x-ui.card-stats :title="'Total Penyuluh'" :stats="$totalPenyuluhTerdaftar"
                             :description="'Penyuluh yang terdaftar diSitani'"
                             :icon="'icon-[octicon--person-24]'" :icon-color="'text-bg-soft-primary'"/>
            <x-ui.card-stats :title="'Total Kelompok Tani'" :stats="$totalKelompokTani"
                             :description="'Kelompok Tani yang terdaftar diSitani'" :icon="'icon-[iconoir--group]'"
                             :icon-color="'text-bg-soft-accent'"/>
            <x-ui.card-stats :title="'Total Laporan Bibit'" :stats="$totalLapBibit"
                             :description="'Laporan Bibit pada hari ini'"
                             :icon="'icon-[carbon--report]'" :icon-color="'text-bg-soft-warning'"/>
            <x-ui.card-stats :title="'Total Permintaan Hibah'" :stats="$totalPermintaanHibah"
                             :description="'Permintaan Hibah pada hari ini'" :icon="'icon-[mdi--donation-outline]'"
                             :icon-color="'text-bg-soft-info'"/>
        </div>
        <div class="stat-chart-container w-full h-fit grid grid-cols-2 max-xl:grid-cols-1 gap-2.5">
            @php
                $seriesBibits = [$statsBibit['approved'], $statsBibit['rejected']];
                $labelBibits = ['Unggul', 'Non-Unggul'];
                $bibitColors = ['var(--color-success)', 'var(--color-error)'];
                $seriesPenyuluhs = [$percPakaiApp, $percBelumPakaiApp];
                $labelPenyuluhs = ['Dengan App', 'Tanpa App'];
                $penyuluhColors = ['var(--color-accent)', 'var(--color-warning)'];
            @endphp
            <x-ui.card>
                <div class="grid grid-cols-2 max-lg:grid-cols-1 gap-2.5">
                    <div class="flex flex-col justify-center">
                        <span class="block text-wrap font-bold">Statistik Penggunaan Bibit</span>
                        <small class="block text-wrap">Persentase perbandingan penggunaan bibit unggul <br> dengan non
                            unggul di Kab.Nganjuk pada tahun ini.</small>
                    </div>
                    <div id="doughnut-chart-bibit"
                         class="js-doughnut-chart box-border flex justify-end max-lg:justify-center"
                         data-series='@json($seriesBibits)'
                         data-labels='@json($labelBibits)' data-colors='@json($bibitColors)'></div>
                </div>
            </x-ui.card>
            <x-ui.card>
                <div class="grid grid-cols-2 max-lg:grid-cols-1 gap-2.5">
                    <div class="flex flex-col justify-center">
                        <span class="block text-wrap font-bold">Statistik Penyuluh</span>
                        <small class="block text-wrap">Persentase penyuluh yang sudah menggunakan <br> applikasi Sitani
                            Mobile</small>
                    </div>
                    <div id="doughnut-chart-penyuluh"
                         class="js-doughnut-chart box-border flex justify-end max-lg:justify-center"
                         data-series='@json($seriesPenyuluhs)'
                         data-labels='@json($labelPenyuluhs)' data-colors='@json($penyuluhColors)'></div>
                </div>
            </x-ui.card>
        </div>
        <div class="table-container">
            <x-ui.card>
                <x-ui.title :title="'Data Aliansi Jokowi'" :custom-class="'mb-3.5 font-bold'"/>
                <table id="rekap-table" class="table">
                    <x-ui.table.header-table :items="[
        ['title' => 'Nama'],
        ['title' => 'Deskripsi'],
    ]"/>
                    <tbody>
                    <tr>
                        <td>Ridho</td>
                        <td>Ketua</td>
                    </tr>
                    <tr>
                        <td>Ika</td>
                        <td>Wakil</td>
                    </tr>
                    <tr>
                        <td>Adit</td>
                        <td>Sekertaris</td>
                    </tr>
                    </tbody>
                </table>
            </x-ui.card>
        </div>
        <div class="w-full h-fit flex flex-col gap-2.5 mb-2.5">
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Data tahun: <span class="font-semibold">{{ $selectedYear }}</span>
                </label>
            </div>

            <!-- Card Stats -->
            <x-ui.card-stats
                :title="'Total Permintaan Hibah'"
                :stats="$totalPermintaanHibah ?? 0"
                :description="'Disetujui pada ' . ($selectedYear ?? date('Y'))"
                :icon="'icon-[mdi--donation-outline]'"
                :icon-color="'text-bg-soft-info'"
            />

            <x-ui.card>
                <!-- Card Statistik Pengajuan Alat dengan Donut Chart -->
                <div class="stats-card-container grid grid-cols-2 max-lg:grid-cols-1 gap-2.5">
                    <div class="flex-1 w-full flex flex-col justify-center">
                        <span class="block text-wrap font-bold mb-2">Statistik Pengajuan Alat</span>
                        <small class="text-wrap block">Persentase alat yang disetujui dan ditolak dalam 1 tahun terakhir.</small>

                        <!-- Display Persentase -->
                        <div class="flex mt-4 gap-4">
                            <div>
                                <span class="block" >Persentase Disetujui</span>
                                <span class="block" id="persentaseDisetujui">0%</span>
                            </div>
                            <div>
                                <span class="block" >Persentase Ditolak</span>
                                <span class="block" id="persentaseDitolak">0%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Donut Chart atau fallback jika data kosong -->
                    <div class="relative w-full h-48 flex justify-center sm:justify-center md:justify-center lg:justify-end">
                        @php
                            $totalDiterima = $totalDiterima ?? 0;
                            $totalDitolak = $totalDitolak ?? 0;
                        @endphp

                        @if ($totalDiterima == 0 && $totalDitolak == 0)
                            <div class="flex justify-center items-center h-full text-gray-500">
                                Belum ada data pengajuan alat tahun ini.
                            </div>
                        @else
                            <div id="alatChart" class="js-doughnut-chart"
                                 data-series='@json([$totalDiterima, $totalDitolak])'
                                 data-labels='@json(["Disetujui", "Ditolak"])'
                                 data-colors='@json(["#4CAF50", "#F44336"])'>
                            </div>
                        @endif
                    </div>
                </div>
            </x-ui.card>
        </div>


        <div class="fax-container">
            <x-ui.card>
                <x-ui.title :title="'FAX'" :custom-class="'font-bold'"/>
                <x-ui.accordion.accordion>
                    <x-ui.accordion.accordion-item :title="'Bagaimana cara mengunduh template?'"
                                                   :description="'Anda bisa mengunduh template untuk menggunakan fitur import dan export data dengan cara klik tombol file, dan pilih opsi Unduh Template.'"
                                                   :key-id="'download-accordion'"/>
                    <x-ui.accordion.accordion-item :title="'Bagaimana cara import data?'"
                                                   :description="'Pastikan anda telah mengunduh template yang disediakan, dan anda bisa klik tombol file, pilih opsi Import, dan upload file excel yang sudah anda isi dengan data.'"
                                                   :key-id="'import-accordion'"/>
                    <x-ui.accordion.accordion-item :title="'Bagaimana cara export data?'"
                                                   :description="'Anda bisa klik tombol file, dan pilih opsi export, data akan diexport secara otomatis.'"
                                                   :key-id="'import-accordion'"/>
                </x-ui.accordion.accordion>
            </x-ui.card>
        </div>
    </div>
@endsection
@once
    <script>
        window.addEventListener("load", () => {
            const chartEls = document.querySelectorAll('.js-doughnut-chart');

            chartEls.forEach(chartEl => {
                if (!chartEl) return;

                const series = JSON.parse(chartEl.dataset.series);
                const labels = JSON.parse(chartEl.dataset.labels);
                const colors = JSON.parse(chartEl.dataset.colors);

                const maxIndex = series.indexOf(Math.max(...series));
                const maxValue = series[maxIndex];
                const maxLabel = labels[maxIndex];

                buildChart(`#${chartEl.id}`, mode => ({
                    chart: {
                        height: 195,
                        type: 'donut',
                        width: 195,
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    name: {
                                        fontSize: '2rem'
                                    },
                                    value: {
                                        fontSize: '1.5rem',
                                        color: 'var(--color-base-content)',
                                        formatter: (val) => parseInt(val, 10) + '%'
                                    },
                                    total: {
                                        show: true,
                                        fontSize: '0.8rem',
                                        label: `${maxLabel}`,
                                        color: 'var(--color-primary)',
                                        formatter: () => `${maxValue}%`
                                    }
                                }
                            }
                        }
                    },
                    series: series,
                    labels: labels,
                    legend: {
                        show: true,
                        position: 'bottom',
                        markers: {offsetX: -3},
                        labels: {
                            useSeriesColors: true
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: false,
                        curve: 'straight'
                    },
                    colors: colors,
                    states: {
                        hover: {
                            filter: {
                                type: 'none'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true
                    }
                }));
            });


            if (document.getElementById("rekap-table") && typeof simpleDatatables.DataTable !== 'undefined') {
                const dataTable = new simpleDatatables.DataTable("#rekap-table", {
                    paging: true,
                    perPage: 5,
                    perPageSelect: [5, 10, 15, 20, 25],
                    sortable: true,
                    searcable: true,
                    labels: {
                        placeholder: "Cari data",
                        searchTitle: "Search within table",
                        pageTitle: "Page {page}",
                        perPage: "Data per halaman",
                        noRows: "Data Kosong",
                        info: "Menampilkan {start} sampai {end} dari {rows} data",
                        noResults: "Data tidak ditemukan",
                    },
                });
            }

            const $topTable = $(".datatable-top");
            const $pagesTable = $(".datatable-dropdown");
            const $searchTable = $(".datatable-search");
            if ($topTable && $pagesTable && $searchTable) {
                $topTable.addClass('flex justify-between!');
                $pagesTable.addClass('grow-0! w-fit!');
                $searchTable.addClass('grow-0! w-fit!');
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var totalDiterima = {{ $totalDiterima }};
            var totalDitolak = {{ $totalDitolak }};
            var totalPermintaan = totalDiterima + totalDitolak;

            var persentaseDisetujui = (totalDiterima / totalPermintaan) * 100 || 0;
            var persentaseDitolak = (totalDitolak / totalPermintaan) * 100 || 0;

            document.getElementById('persentaseDisetujui').textContent = persentaseDisetujui.toFixed(2) + '%';
            document.getElementById('persentaseDitolak').textContent = persentaseDitolak.toFixed(2) + '%';
        });
    </script>

@endonce
