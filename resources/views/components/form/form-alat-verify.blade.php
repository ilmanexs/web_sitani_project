<form id="form-verify-alat" action="{{route('laporan-alat.update', $laporan->id)}}" method="post" class="flex flex-col space-y-5">
    @method('PUT')
    @csrf
    <div class="input-container space-y-5">
        {{-- start info kelompok tani --}}
        <div id="info-kelompok-tani" class="space-y-5">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="nama-ketua-kelompok-tani">Nama Ketua Kelompok Tani</label>
                    <input
                        class="bg-gray-50 border border-gray-300 text-gray-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 cursor-not-allowed"
                        placeholder="Nama ketua kelompok tani" type="text" id="nama-ketua-kelompok-tani" name="nama-ketua-kelompok-tani"
                        value="{{ old('nama-ketua-kelompok-tani', $laporan->LaporanBantuanAlatDetail->nama_ketua) }}" disabled>
                </div>
                <div>
                    <label for="no-hp-ketua">No HP Ketua</label>
                    <input
                        class="bg-gray-50 border border-gray-300 text-gray-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 cursor-not-allowed"
                        placeholder="No Hp Ketua Kelompok tani" type="text" id="no-hp-ketua" name="no-hp-ketua"
                        value="{{ old('no-hp-ketua', $laporan->LaporanBantuanAlatDetail->no_hp_ketua) }}" disabled>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="npwp">NPWP</label>
                    <input
                        class="bg-gray-50 border border-gray-300 text-gray-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 cursor-not-allowed"
                        placeholder="NPWP" type="text" id="npwp" name="npwp"
                        value="{{ old('npwp', $laporan->LaporanBantuanAlatDetail->npwp) }}" disabled>
                </div>
                <div>
                    <label for="email-kelompok-tani">Email Kelompok Tani</label>
                    <input
                        class="bg-gray-50 border border-gray-300 text-gray-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 cursor-not-allowed"
                        placeholder="Email Kelompok Tani" type="email" id="email-kelompok-tani" name="email-kelompok-tani"
                        value="{{ old('email-kelompok-tani', $laporan->LaporanBantuanAlatDetail->email_kelompok_tani) }}" disabled>
                </div>
                <div>
                    <label for="password-email-kelompok-tani">Password Email Kelompok Tani</label>
                    <input
                        class="bg-gray-50 border border-gray-300 text-gray-600 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 cursor-not-allowed"
                        placeholder="Email Kelompok Tani" type="email" id="email-kelompok-tani" name="email-kelompok-tani"
                        value="{{ old('email-kelompok-tani', $laporan->LaporanBantuanAlatDetail->password_email) }}" disabled>
                </div>
            </div>
        </div>
        {{-- End info kelompok tani --}}

        <div id="detail-laporan" class="space-y-5">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="path_ktp_ketua">KTP Ketua</label>
                    @if($laporan->LaporanBantuanAlatDetail->path_ktp_ketua)
                        <img src="{{ asset('storage/' . $laporan->LaporanBantuanAlatDetail->path_ktp_ketua) }}" alt="KTP Ketua" class="object-cover w-full h-48 rounded-lg">
                    @else
                        <p>No image available</p>
                    @endif
                </div>
                <div>
                    <label for="path_badan_hukum">Badan Hukum (PDF)</label>
                    @if($laporan->LaporanBantuanAlatDetail->path_badan_hukum)
                        <a href="{{ asset('storage/' . $laporan->LaporanBantuanAlatDetail->path_badan_hukum) }}" target="_blank" class="text-blue-500">View Badan Hukum (PDF)</a>
                    @else
                        <p>No file available</p>
                    @endif
                </div>

            </div>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="path_piagam">Piagam Pengesahan (PDF)</label>
                    @if($laporan->LaporanBantuanAlatDetail->path_piagam)
                        <a href="{{ asset('storage/' . $laporan->LaporanBantuanAlatDetail->path_piagam) }}" target="_blank" class="text-blue-500">View Piagam (PDF)</a>
                    @else
                        <p>No file available</p>
                    @endif
                </div>
                <div>
                    <label for="path_surat_domisili">Surat Domisili (PDF)</label>
                    @if($laporan->LaporanBantuanAlatDetail->path_surat_domisili)
                        <a href="{{ asset('storage/' . $laporan->LaporanBantuanAlatDetail->path_surat_domisili) }}" target="_blank" class="text-blue-500">View Surat Domisili (PDF)</a>
                    @else
                        <p>No file available</p>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="path_foto_lokasi">Foto Lokasi</label>
                    @if($laporan->LaporanBantuanAlatDetail->path_foto_lokasi)
                        <img src="{{ asset('storage/' . $laporan->LaporanBantuanAlatDetail->path_foto_lokasi) }}" alt="Foto Lokasi" class="object-cover w-full h-48 rounded-lg">
                    @else
                        <p>No image available</p>
                    @endif
                </div>
                <div>
                    <label for="path_ktp_sekretaris">KTP Sekretaris</label>
                    @if($laporan->LaporanBantuanAlatDetail->path_ktp_sekretaris)
                        <img src="{{ asset('storage/' . $laporan->LaporanBantuanAlatDetail->path_ktp_sekretaris) }}" alt="KTP Sekretaris" class="object-cover w-full h-48 rounded-lg">
                    @else
                        <p>No image available</p>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="path_ktp_ketua_upkk">KTP Ketua UPKK</label>
                    @if($laporan->LaporanBantuanAlatDetail->path_ktp_ketua_upkk)
                        <img src="{{ asset('storage/' . $laporan->LaporanBantuanAlatDetail->path_ktp_ketua_upkk) }}" alt="KTP Ketua UPKK" class="object-cover w-full h-48 rounded-lg">
                    @else
                        <p>No image available</p>
                    @endif
                </div>
                <div>
                    <label for="path_ktp_anggota1">KTP Anggota 1</label>
                    @if($laporan->LaporanBantuanAlatDetail->path_ktp_anggota1)
                        <img src="{{ asset('storage/' . $laporan->LaporanBantuanAlatDetail->path_ktp_anggota1) }}" alt="KTP Anggota 1" class="object-cover w-full h-48 rounded-lg">
                    @else
                        <p>No image available</p>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="path_ktp_anggota2">KTP Anggota 2</label>
                    @if($laporan->LaporanBantuanAlatDetail->path_ktp_anggota2)
                        <img src="{{ asset('storage/' . $laporan->LaporanBantuanAlatDetail->path_ktp_anggota2) }}" alt="KTP Anggota 2" class="object-cover w-full h-48 rounded-lg">
                    @else
                        <p>No image available</p>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="path_proposal">Proposal (PDF)</label>
                    @if($laporan->path_proposal)
                        <a href="{{ asset('storage/' . $laporan->path_proposal) }}" target="_blank" class="text-blue-500">View Proposal (PDF)</a>
                    @else
                        <p>No proposal available</p>
                    @endif
                </div>
            </div>
        <div>
            <label for="radio-verifikasi">Verifikasi Alat</label>
            <div class="flex flex-wrap gap-2">
                <div class="flex items-center gap-1">
                    <input type="radio" name="status" class="radio radio-inset radio-success"
                           id="alat-diverifikasi" value="1" {{ old('status', $laporan->status) == 1 ? 'checked' : '' }} />
                    <label class="label-text text-base" for="alat-diverifikasi"> Setujui </label>
                </div>
                <div class="flex items-center gap-1">
                    <input type="radio" name="status" class="radio radio-inset radio-error"
                           id="alat-ditolak" value="0" {{ old('status', $laporan->status) == 0 ? 'checked' : '' }}/>
                    <label class="label-text text-base" for="alat-ditolak"> Tolak </label>
                </div>
            </div>
            @error('status')
            <p class="helper-text text-red-600">{{ $message }}</p>
            @else
                <span class="helper-text">Verifikasi permintaan bantuan hibah</span>
                @enderror
            </div>
        </div>

    {{-- action button --}}
    <div class="button-group flex space-x-4">
        <button class="btn btn-soft btn-secondary" onclick="back()">Kembali</button>
        <x-ui.button.save-button :style="'btn-soft'" :formId="'form-verify-alat'" :title="'Verifikasi'"/>
    </div>

    </div>
</form>
<script>
    function back(){
        history.back();
    }
</script>
