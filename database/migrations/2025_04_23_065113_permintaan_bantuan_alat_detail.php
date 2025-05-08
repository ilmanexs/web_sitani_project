<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permintaan_bantuan_alat_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_bantuan_alat_id');
            $table->string('nama_ketua');
            $table->string('no_hp_ketua');
            $table->string('npwp');
            $table->string('email_kelompok_tani');
            $table->string('password_email');
            $table->string('path_ktp_ketua');
            $table->string('path_badan_hukum');
            $table->string('path_piagam');
            $table->string('path_surat_domisili');
            $table->string('path_foto_lokasi');
            $table->string('path_ktp_sekretaris');
            $table->string('path_ktp_ketua_upkk');
            $table->string('path_ktp_anggota1');
            $table->string('path_ktp_anggota2');
            $table->timestamps();

            $table->foreign('permintaan_bantuan_alat_id', 'fk_pb_alat_detail_pb_alat_id')
                ->references('id')
                ->on('permintaan_bantuan_alat')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_bantuan_alat_detail');
    }
};
