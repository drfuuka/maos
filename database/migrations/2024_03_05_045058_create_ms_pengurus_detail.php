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
        Schema::create('ms_pengurus_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('ms_user')->onDelete('cascade');
            $table->string('nama_pengaju');
            $table->string('jabatan');
            $table->string('no_hp');
            $table->string('ttd')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ms_pengurus_detail');
    }
};
