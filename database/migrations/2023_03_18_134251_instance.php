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
        Schema::create('instances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(true);
            $table->bigInteger('owner_id')->unsigned()->nullable(false);
            $table->string('api_key', 128)->nullable(true)->default(null);
            $table->timestamps();
        });
        Schema::create('linker_instance_users', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('instance_id')->unsigned()->nullable(false);
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->timestamps();

            $table->unique(['instance_id', 'user_id']);
        });
        Schema::create('instance_invites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 64)->nullable(false);
            $table->bigInteger('instance_id')->unsigned()->nullable(false);
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->bigInteger('created_by')->unsigned()->nullable(false);
            $table->timestamps();

            $table->unique(['instance_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instances');
        Schema::dropIfExists('linker_instance_users');
        Schema::dropIfExists('instance_invites');
    }
};
