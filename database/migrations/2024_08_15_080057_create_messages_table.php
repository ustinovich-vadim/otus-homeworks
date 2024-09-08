<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dialog_id');
            $table->unsignedBigInteger('sender_id');
            $table->text('text');
            $table->timestamps();

            $table->index('dialog_id');
            $table->index('sender_id');
            $table->index('created_at');
            $table->primary(['dialog_id', 'id']);
            $table->foreign('dialog_id')->references('id')->on('dialogs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
