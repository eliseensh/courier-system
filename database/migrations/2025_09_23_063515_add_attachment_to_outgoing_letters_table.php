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
    Schema::table('outgoing_letters', function (Blueprint $table) {
        if (!Schema::hasColumn('outgoing_letters', 'attachment')) {
            $table->string('attachment')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('outgoing_letters', function (Blueprint $table) {
        $table->dropColumn('attachment');
    });
}

};
