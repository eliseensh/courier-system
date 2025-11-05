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
    Schema::create('incoming_letters', function (Blueprint $table) {
        $table->id();
        $table->string('number')->nullable()->unique();   // Letter number (optional but unique)
        $table->date('date');                             // Date of the letter
        $table->string('reference')->nullable();          // Reference
        $table->string('annex')->nullable();              // Annex/attachment
        $table->string('company');                        // Sender company
        $table->string('subject');                        // Subject of the letter
      $table->enum('status', ['pending','in-progress','viewed','responded','done'])->default('pending');

        $table->text('observation')->nullable();          // Notes/observations
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('incoming_letters');
    }
};
