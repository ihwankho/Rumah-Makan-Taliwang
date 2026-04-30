<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mejas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor_meja', 50);
            $table->string('qr_code', 255)->nullable(); // boleh null dulu
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mejas');
    }
};
