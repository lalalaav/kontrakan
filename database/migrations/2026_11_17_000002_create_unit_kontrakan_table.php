<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_kontrakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lokasi_id')->constrained('lokasi')->cascadeOnDelete();
            $table->string('nomor_unit', 20)->unique();
            $table->decimal('tarif_sewa', 12, 2);
            $table->unsignedTinyInteger('jumlah_kamar')->default(1);
            $table->decimal('luas_bangunan', 8, 2)->nullable();
            $table->text('fasilitas')->nullable();
            $table->enum('status', ['kosong', 'terisi', 'perbaikan'])->default('kosong')->index();
            $table->boolean('izin_hewan')->default(false);
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_kontrakan');
    }
};
