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
        Schema::create('user_loadouts', function (Blueprint $table) {
       
            $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
       
        $table->foreignId('headgear_id')->nullable()->constrained('shop_items')->onDelete('set null');
        $table->foreignId('top_id')->nullable()->constrained('shop_items')->onDelete('set null');
        $table->foreignId('bottom_id')->nullable()->constrained('shop_items')->onDelete('set null');
        $table->foreignId('accessory_id')->nullable()->constrained('shop_items')->onDelete('set null');
        $table->foreignId('background_id')->nullable()->constrained('shop_items')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_loadouts');
    }
};
