@extends('layouts.layout')
@section('title', 'Laporan Bibit | Sitani')
@section('content')
    <x-ui.card>
        <div class="mb-5 border-b border-[#E5E8EC]">
            <x-ui.title :title="'Verifikasi Laporan'"/>
        </div>
        <form id="form-verify-bibit" action="{{route('laporan-bibit.update', $laporan->id)}}" method="post"
              class="flex flex-col space-y-5">
            @method('PUT')
            @csrf
            <div class="input-container space-y-5">
                {{-- start info kelompok tani --}}
                <div id="info-kelompok-tani" class="space-y-5">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <x-form.input-text :keyId="'nama-kelompok-tani'" :name="'nama-kelompok-tani'"
                                           :label="'Nama Kelompok Tani'" :default-value="$laporan->kelompokTani->nama"
                                           :isDisable="true"/>
                        <x-form.input-text :keyId="'desa-kelompok-tani'" :name="'desa-kelompok-tani'" :label="'Desa'"
                                           :default-value="$laporan->kelompokTani->desa->nama" :isDisable="true"/>
                    </div>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <x-form.input-text :keyId="'kecamatan-kelompok-tani'" :name="'kecamatan-kelompok-tani'"
                                           :label="'Kecamatan'" :default-value="$laporan->kelompokTani->kecamatan->nama"
                                           :isDisable="true"/>
                        <x-form.input-text :keyId="'komoditas'" :name="'komoditas'" :label="'Komoditas'"
                                           :default-value="$laporan->komoditas->nama" :isDisable="true"/>
                    </div>
                </div>
                {{-- End info kelompok tani --}}

                {{-- start detail laporan --}}
                <div id="detail-laporan" class="space-y-5">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <x-form.input-text :keyId="'bibit'" :name="'bibit'" :label="'Bibit'"
                                           :default-value="$laporan->laporanKondisiDetail->jenis_bibit"
                                           :isDisable="true"/>
                        <x-form.input-text :keyId="'luas-lahan'" :name="'luas-lahan'" :label="'Luas Penanaman'"
                                           :default-value="$laporan->laporanKondisiDetail->luas_lahan . ' Hektar'"
                                           :isDisable="true"/>
                    </div>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <x-form.input-text :keyId="'estimasi-panen'" :name="'estimasi-panen'" :label="'Estimasi Panen'"
                                           :default-value="$laporan->laporanKondisiDetail->estimasi_panen"
                                           :isDisable="true"/>
                        <x-form.input-text :keyId="'pelapor'" :name="'pelapor'" :label="'Pembuat Laporan'"
                                           :default-value="$laporan->penyuluh->penyuluhTerdaftar->nama"
                                           :isDisable="true"/>
                    </div>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="label-text mb-2.5" for="foto-bibit">Foto Bibit</label>
                            <div class="relative min-w-8 max-w-96 h-60">
                                <div class="skeleton skeleton-animated absolute inset-0" id="skeleton-image-bibit"></div>
                                <a href="{{ asset('storage/' . $laporan->laporanKondisiDetail->foto_bibit)}}"
                                   data-fancybox="data-foto" data-caption="Foto Bibit">
                                    <img src="{{ asset('storage/' . $laporan->laporanKondisiDetail->path_foto_lokasi)}}"
                                         alt="foto-bibit"
                                         class="image-bibit w-full h-full object-cover rounded-md shadow absolute cursor-pointer"
                                         onload="document.getElementById('skeleton-image-bibit').style.display='none';"/>
                                </a>
                            </div>
                        </div>
                        <div>
                            <label class="label-text mb-2.5" for="foto-lokasi">Foto Lokasi</label>
                            <div class="relative min-w-8 max-w-96 h-60">
                                <div class="skeleton skeleton-animated absolute inset-0" id="skeleton-image-lokasi"></div>
                                <a href="{{ asset('storage/' . $laporan->laporanKondisiDetail->path_foto_lokasi)}}"
                                   data-fancybox="data-foto" data-caption="Foto Lokasi Penanaman">
                                    <img src="{{ asset('storage/' . $laporan->laporanKondisiDetail->path_foto_lokasi)}}"
                                         alt="foto-lokasi"
                                         class="image-lokasi w-full h-full object-cover rounded-md shadow absolute cursor-pointer"
                                         onload="document.getElementById('skeleton-image-lokasi').style.display='none';"/>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="grid grid-cols-1 gap-6 md:flex md:flex-col">
                            <x-form.input-text :keyId="'lokasi-penanaman'" :name="'lokasi-penanaman'"
                                               :label="'Lokasi Penanaman'"
                                               :default-value="$laporan->laporanKondisiDetail->lokasi_lahan"
                                               :isDisable="true"/>
                            <x-form.input-text :keyId="'waktu-laporan'" :name="'waktu-laporan'" :label="'Waktu Laporan'"
                                               :default-value="$laporan->created_at"
                                               :isDisable="true"/>
                        </div>
                        <div>
                            <label class="label-text" for="radio-verifikasi">Kualitas bibit</label>
                            <div class="flex flex-wrap gap-2">
                                <div class="flex items-center gap-1">
                                    <input type="radio" name="status" class="radio radio-inset radio-success"
                                           id="radio-berkualitas"
                                           value="1" {{ old('status', $laporan->status) == 1 ? 'checked' : '' }} />
                                    <label class="label-text text-base" for="radio-berkualitas"> Berkualitas </label>
                                </div>
                                <div class="flex items-center gap-1">
                                    <input type="radio" name="status" class="radio radio-inset radio-error"
                                           id="radio-tidak-berkualitas"
                                           value="0" {{ old('status', $laporan->status) == 0 ? 'checked' : '' }}/>
                                    <label class="label-text text-base" for="radio-tidak-berkualitas"> Tidak Berkualitas
                                    </label>
                                </div>
                            </div>
                            @error('status')
                            <p class="helper-text text-red-600">{{ $message }}</p>
                            @else
                                <span
                                    class="helper-text">Verifikasi kualitas bibit dengan memilih opsi yang tersedia</span>
                                @enderror
                        </div>
                    </div>
                </div>
                {{-- End detail laporan --}}
            </div>

            {{-- action button --}}
            <div class="button-group flex space-x-4">
                <x-ui.button.back-button :style="'btn-soft'" :title="'Kembali'" :routes="'laporan-bibit.index'"/>
                <x-ui.button.save-button :style="'btn-soft'" :formId="'form-verify-bibit'"
                                         :title="$laporan->status == 2 ? 'Verifikasi': 'Perbarui'"/>
            </div>
        </form>
    </x-ui.card>
    @once
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.Fancybox) {
                    Fancybox.bind("[data-fancybox]", {
                    });
                }
            });
        </script>
    @endonce
@endsection

