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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_menu_id')
                ->constrained('kategori_menus')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nama', 150);
            $table->integer('harga');
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->boolean('perlu_dimasak')->default(true);

            $table->boolean('is_aktif')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
