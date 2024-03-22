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
        Schema::create('tr_lpj_gudep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_gudep_id')->references('id')->on('tr_proposal_gudep')->onDelete('cascade');
            $table->foreignId('user_id')->references('id')->on('ms_user')->onDelete('cascade');
            $table->text('foto_kegiatan');
            $table->string('dokumen_lpj');
            $table->string('evaluasi');
            $table->string('saran')->nullable();
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
        Schema::dropIfExists('tr_lpj_gudep');
    }
};
