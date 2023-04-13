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
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false);
            $table->string('description')->nullable(true);
            $table->bigInteger('permission_group')->unsigned()->nullable(false);
            $table->timestamps();
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false);
            $table->bigInteger('instance_id')->unsigned()->nullable(false);
            $table->timestamps();
        });
        Schema::create('linker_role_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('role_id')->unsigned()->nullable(false);
            $table->bigInteger('permission_id')->unsigned()->nullable(false);
            $table->timestamps();
            
            $table->unique(['role_id', 'permission_id']);
        });
        Schema::create('linker_user_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->bigInteger('role_id')->unsigned()->nullable(false);
            $table->timestamps();
            
            $table->unique(['role_id', 'user_id']);
        });
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false)->unique('unique_permission_group_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('linker_role_permissions');
        Schema::dropIfExists('linker_user_roles');
        Schema::dropIfExists('permission_groups');
        Schema::dropIfExists('linker_permission_permission_groups');
    }
};
