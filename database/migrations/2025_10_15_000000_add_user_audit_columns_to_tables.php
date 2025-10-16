<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration is an example that users can publish and modify
        // to add the audit columns to their existing tables

        /*
        Schema::table('your_table', function (Blueprint $table) {
            $table->userAuditable();
        });
        */
    }

    public function down(): void
    {
        /*
        Schema::table('your_table', function (Blueprint $table) {
            $table->dropUserAuditable();
        });
        */
    }
};
