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
        Schema::create('modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false);
            $table->string('description')->nullable(true);
            $table->string('controller_path')->nullable(false);
            $table->string('fa_icon')->nullable(true);
            $table->timestamps();
        });
        Schema::create('instance_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('instance_id')->nullable(false);
            $table->bigInteger('module_id')->nullable(false);
            $table->boolean('public_access')->nullable(false)->default(false);
            $table->datetime('paid_until')->nullable(true);
            $table->timestamps();

            $table->unique(['instance_id', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
        Schema::dropIfExists('instance_modules');
    }
};
