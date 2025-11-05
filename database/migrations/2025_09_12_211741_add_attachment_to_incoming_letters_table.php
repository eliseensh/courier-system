<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Vérifie si la table existe
        if (Schema::hasTable('incoming_letters')) {
            // Ajoute la colonne 'attachment' uniquement si elle n'existe pas
            Schema::table('incoming_letters', function (Blueprint $table) {
                if (!Schema::hasColumn('incoming_letters', 'attachment')) {
                    $table->string('attachment')->nullable();
                }
            });
        } else {
            // Si la table n'existe pas, la crée complètement
            Schema::create('incoming_letters', function (Blueprint $table) {
                $table->id();
                $table->string('number')->nullable()->unique();
                $table->date('date');
                $table->string('reference')->nullable();
                $table->string('annex')->nullable();
                $table->string('company');
                $table->string('addressed_to')->nullable();
                $table->string('subject');
                $table->enum('status', ['pending','in-progress','viewed','responded','done'])->default('pending');
                $table->text('observation')->nullable();
                $table->string('attachment')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::table('incoming_letters', function (Blueprint $table) {
            if (Schema::hasColumn('incoming_letters', 'attachment')) {
                $table->dropColumn('attachment');
            }
        });
    }
};
