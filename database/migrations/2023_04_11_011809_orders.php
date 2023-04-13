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
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('instance_id')->nullable(false);
            $table->bigInteger('instance_module_id')->nullable(false);
            $table->bigInteger('module_id')->nullable(false);
            $table->string('mollie_transaction_id')->nullable(true);
            $table->string('payment_status')->nullable(false);
            $table->bigInteger('duration_booked')->nullable(false);
            $table->datetime('finished_at')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
