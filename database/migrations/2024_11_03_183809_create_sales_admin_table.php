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
        Schema::create('sales_admin', function (Blueprint $table) {
            $table->id('sales_admin_id'); // Primary key
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->unsignedBigInteger('product_brand_id')->nullable();
            $table->unsignedBigInteger('product_category_id')->nullable();
            $table->unsignedBigInteger('product_sub_category_id')->nullable();
            $table->unsignedBigInteger('product_child_category_id')->nullable();
            $table->decimal('product_price', 10, 2)->nullable();
            $table->integer('product_order_quantity')->nullable();
            $table->decimal('order_cost', 10, 2)->nullable();
            $table->decimal('sales', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_admin');
    }
};
