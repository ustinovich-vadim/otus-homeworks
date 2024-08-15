<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("SELECT create_reference_table('dialogs');");
        DB::statement("SELECT create_distributed_table('messages', 'dialog_id');");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("SELECT drop_distributed_table('messages');");
        DB::statement("SELECT drop_reference_table('dialogs');");
    }
};
