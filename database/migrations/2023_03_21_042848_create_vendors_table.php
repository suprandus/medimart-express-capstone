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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->text('banner');
            $table->string('phone');
            $table->string('email');
            $table->text('address');
            $table->text('description');
            $table->text('fb_link')->nullable();
            $table->text('tw_link')->nullable();
            $table->text('insta_link')->nullable();
            $table->integer('user_id');
            $table->boolean('status')->default(0);
            //new rows
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('tin', 20)->nullable(); 
            $table->string('bir_certificate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
