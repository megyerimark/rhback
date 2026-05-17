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
        Schema::create('shop_items', function (Blueprint $table) {
            $table->id();
           
        $table->string('name');
        $table->string('category'); // headgear, top, bottom, accessory, background
        $table->integer('price')->default(0); // Ár Gears pontban
        $table->string('image_url')->nullable();
        $table->string('model_url')->nullable(); // Jövőbeli 3D modell fájlhoz
        $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_items');
    }
};
