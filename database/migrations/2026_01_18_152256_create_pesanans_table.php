<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();

            $table->uuid('id_meja');
            $table->foreign('id_meja')->references('id')->on('mejas')->cascadeOnUpdate()->restrictOnDelete();

            $table->foreignId('id_pelanggan')
                ->constrained('pelanggans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // ✅ tambahan dari pembahasan sebelumnya
            $table->enum('tipe_pesanan', ['makan_ditempat', 'bungkus']);

            // status sesuai ERD (silakan sesuaikan isi enum-nya kalau kamu sudah punya)
            $table->enum('status_pesanan', ['menunggu', 'diproses', 'selesai', 'dibayar'])->default('menunggu');

            $table->decimal('total_harga', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
