<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // e.g. John Deere 6R 150
            $table->string('type');           // Tractor, Harvester, etc.
            $table->decimal('rate', 10, 2);   // daily rate
            $table->enum('status', ['available', 'onfield', 'maintenance'])->default('available');
            $table->string('image_path')->nullable(); // path to uploaded photo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};