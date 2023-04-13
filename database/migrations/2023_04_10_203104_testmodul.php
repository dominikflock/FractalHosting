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
        Schema::create('test_module_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('instance_id');
            $table->string("message")->nullable(false);
            $table->string("seen")->nullable(false)->default(false);
            $table->boolean('updated_by_cron')->nullable(false)->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_module_messages');
    }
};
