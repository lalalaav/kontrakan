<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kontrak_sewa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_kontrakan_id')->constrained('unit_kontrakan')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('nominal_sewa', 12, 2);
            $table->string('ktp_path')->nullable();
            $table->enum('status', ['diajukan', 'disetujui', 'aktif', 'ditolak', 'selesai'])
                ->default('diajukan')->index();
            $table->text('catatan')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kontrak_sewa');
    }
};
