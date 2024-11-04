<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLatitudeLongitudeTinBirToVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable(); // For latitude
            $table->decimal('longitude', 11, 8)->nullable(); // For longitude
            $table->string('tin', 20)->nullable(); // TIN (Taxpayer Identification Number)
            $table->string('bir_certificate')->nullable(); // BIR Certificate image path
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'tin', 'bir_certificate']);
        });
    }
}
