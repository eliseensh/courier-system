<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('outgoing_letters', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('date');
            $table->string('recipient');
            $table->string('subject');
            $table->text('observation')->nullable();
            $table->string('attachment')->nullable();
            $table->enum('status', ['draft', 'sent', 'archived'])->default('draft');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('outgoing_letters');
    }
};
