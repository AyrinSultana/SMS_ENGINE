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
        Schema::create('sms_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->nullable()->constrained('template')->onDelete('set null');
            $table->string('recipient');
            $table->string('mobile_no')->nullable();
            $table->string('template_name')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'sent', 'approved', 'rejected', 'cancelled', 'failed'])->default('pending');
            $table->timestamp('modified_at')->default(now());
            $table->string('source')->nullable()->comment('Source of the SMS (e.g., Summary, Direct)');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_history');
    }
};
