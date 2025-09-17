<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsqueueTable extends Migration
{
    public function up()
    {
        Schema::create('smsqueue', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('mobile', 15); // Mobile number (adjust length as needed)
            $table->text('msg'); // SMS message
            $table->string('refid')->nullable(); // Reference ID (optional)
            $table->enum('status', ['pending', 'sent', 'approved', 'rejected', 'cancelled', 'failed'])->default('pending'); // SMS status
            $table->timestamp('timestamp'); // Timestamp
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('smsqueue');
    }
}
