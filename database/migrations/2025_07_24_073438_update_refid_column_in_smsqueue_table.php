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
          Schema::table('smsqueue', function (Blueprint $table) {
            // Rename old 'refid' to 'excel_id'
            $table->renameColumn('refid', 'excel_id');
        });

        Schema::table('smsqueue', function (Blueprint $table) {
            // Add new nullable 'refid' column after 'excel_id'
            $table->string('refid')->nullable()->after('excel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('smsqueue', function (Blueprint $table) {
            // Drop the new 'refid' column
            $table->dropColumn('refid');
        });

        Schema::table('smsqueue', function (Blueprint $table) {
            // Rename 'excel_id' back to 'refid'
            $table->renameColumn('excel_id', 'refid');
        });
    }
};
