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
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('phone', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('prefecture', 50)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_branch', 100)->nullable();
            $table->enum('bank_account_type', ['ordinary', 'current'])->nullable();
            $table->string('bank_account_number', 20)->nullable();
            $table->string('bank_account_holder', 100)->nullable();
            $table->string('profile_photo_path', 255)->nullable();
            $table->timestamp('id_verified_at')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_profiles');
    }
};
