<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kios', function (Blueprint $table) {
            $table->id('id_kios');
            $table->string('no_kios')->unique();
            $table->string('lokasi')->nullable();
            $table->decimal('ukuran', 8, 2)->default(10.00);
            $table->decimal('tarif_bulanan', 12, 2)->default(250000);
            $table->enum('status', ['kosong', 'terisi', 'maintenance'])->default('kosong');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kios');
    }
};
