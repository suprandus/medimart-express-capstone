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
        Schema::create('pay_mongo_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('paymongo_status')->default(true);
            $table->boolean('live_mode')->default(false);
            $table->text('public_key');
            $table->text('secret_key');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_mongo_settings');
    }
};
