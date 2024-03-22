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
        Schema::create('tr_proposal_gudep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('ms_user')->onDelete('cascade');
            $table->string('jenis_proposal');
            $table->string('dasar_kegiatan');
            $table->string('maksud_tujuan');
            $table->string('nama_kegiatan');
            $table->string('tema_kegiatan');
            $table->string('kepanitiaan');
            $table->date('tanggal_kegiatan');
            $table->text('jadwal_kegiatan');
            $table->text('rincian_dana');
            $table->text('penutup');
            $table->string('dokumen_proposal');
            $table->enum('status_verifikasi', ['Ditolak', 'Diterima'])->nullable();
            $table->foreignId('verificator_id')->nullable()->references('id')->on('ms_user')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_proposal_gudep');
    }
};
