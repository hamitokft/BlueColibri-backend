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
        Schema::create('cafeteria', function (Blueprint $table) {
            $table->uuid('id')->unique()->first();
            $table->string('account_id')->after('id');
            $table->string('pocket_id')->after('account_id');
            $table->integer('jan')->unsigned()->default(0)->after('pocket_id');
            $table->integer('feb')->unsigned()->default(0)->after('jan');
            $table->integer('mar')->unsigned()->default(0)->after('feb');
            $table->integer('apr')->unsigned()->default(0)->after('mar');
            $table->integer('may')->unsigned()->default(0)->after('apr');
            $table->integer('jun')->unsigned()->default(0)->after('may');
            $table->integer('jul')->unsigned()->default(0)->after('jun');
            $table->integer('aug')->unsigned()->default(0)->after('jul');
            $table->integer('sep')->unsigned()->default(0)->after('aug');
            $table->integer('oct')->unsigned()->default(0)->after('sep');
            $table->integer('nov')->unsigned()->default(0)->after('oct');
            $table->integer('dec')->unsigned()->default(0)->after('nov');

            $table->primary('id');
            $table->unique(['account_id', 'pocket_id']);
            $table->foreign('account_id')->references('id')->on('account');
            $table->foreign('pocket_id')->references('id')->on('pocket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafeteria');
    }
};
