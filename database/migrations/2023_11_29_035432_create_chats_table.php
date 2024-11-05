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
        // Check if the 'chats' table already exists
        if (!Schema::hasTable('chats')) {
            Schema::create('chats', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained()->onDelete('cascade'); // Assuming you want cascading deletes
                $table->foreignId('receiver_id')->constrained()->onDelete('cascade'); // Assuming you want cascading deletes
                $table->text('message');
                $table->boolean('seen')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
