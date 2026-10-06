<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The contact form that filled `leads` was removed; the table was empty in production.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('leads');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->string('industry')->nullable();
            $table->string('inquiry_type')->default('general');
            $table->text('message');
            $table->string('source')->default('contact-form');
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index('email');
        });
    }
};
