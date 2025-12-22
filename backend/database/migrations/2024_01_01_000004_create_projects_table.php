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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('venue_name')->nullable();
            $table->string('venue_address', 500)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('hourly_wage');
            $table->unsignedInteger('transportation_fee')->default(0);
            $table->text('requirements')->nullable();
            $table->enum('status', ['draft', 'published', 'closed', 'completed'])->default('draft');
            $table->timestamps();

            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
