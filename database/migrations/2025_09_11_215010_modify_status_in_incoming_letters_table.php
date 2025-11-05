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
        Schema::table('incoming_letters', function (Blueprint $table) {
            $table->enum('status', ['pending','in-progress','viewed','responded','done'])
                  ->default('pending')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incoming_letters', function (Blueprint $table) {
            $table->enum('status', ['pending','viewed','responded','done'])
                  ->default('pending')
                  ->change();
        });
    }
};
