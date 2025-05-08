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
        Schema::create('permintaan_bantuan_alat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_tani_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('penyuluh_id');
            $table->string('alat_diminta');
            $table->string('path_proposal');
            $table->string('status')->default('ditunda');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExist('permintaan_bantuan_alat');
    }
};
