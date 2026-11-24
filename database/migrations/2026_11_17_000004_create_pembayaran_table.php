<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrak_sewa_id')->constrained('kontrak_sewa')->cascadeOnDelete();
            $table->char('periode', 7);                       // YYYY-MM
            $table->decimal('jumlah', 12, 2);
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->string('bukti_path')->nullable();
            $table->enum('status', ['menunggu', 'lunas', 'ditolak'])->default('menunggu')->index();
            $table->text('catatan')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['kontrak_sewa_id', 'periode']);   // cegah tagihan ganda
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
