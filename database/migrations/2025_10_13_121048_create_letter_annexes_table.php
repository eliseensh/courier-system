<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_annexes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incoming_letter_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_annexes');
    }
};
