<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_pesanan')
                ->constrained('pesanans')
                ->cascadeOnDelete();

            // id_kasir ambil dari users
            $table->foreignId('id_kasir')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->enum('metode_pembayaran', ['tunai', 'qris']);
            $table->decimal('total_bayar', 10, 2);

            $table->enum('status_pembayaran', ['belum_bayar', 'dibayar'])->default('belum_bayar');

            $table->timestamp('tanggal_bayar')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
