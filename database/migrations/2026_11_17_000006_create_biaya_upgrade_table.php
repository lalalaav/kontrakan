<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_upgrade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_kontrakan_id')->nullable()->constrained('unit_kontrakan')->nullOnDelete();
            $table->string('judul');
            $table->text('keterangan')->nullable();
            $table->decimal('jumlah', 12, 2);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_upgrade');
    }
};
