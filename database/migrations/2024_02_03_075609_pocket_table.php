<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pocket', function (Blueprint $table) {
            $table->uuid('id')->unique()->first();
            $table->integer('calendar_year')->unsigned()->after('id');
            $table->integer('limit')->unsigned()->after('calendar_year');
            $table->string('name', 64)->after('limit');

            $table->primary('id');
            $table->unique(['calendar_year', 'name']);
            $table->foreign('calendar_year')->references('year')->on('calendar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pocket');
    }
};
