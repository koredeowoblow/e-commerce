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
        if (!Schema::hasTable('genders')) {
            Schema::create('genders', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
            });
        }

        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug');
            });
        }

        if (!Schema::hasTable('states')) {
            Schema::create('states', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('state_id')->constrained('states')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('middle_name')->nullable();
                $table->string('last_name');
                $table->string('matric_no')->unique();
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('password');
                $table->string('profile_image')->nullable();
                $table->foreignId('country_id')->nullable()->constrained('countries');
                $table->foreignId('state_id')->nullable()->constrained('states');
                $table->foreignId('city_id')->nullable()->constrained('cities');
                $table->foreignId('gender_id')->nullable()->constrained('genders');
                $table->string('street_address')->nullable();
                $table->string('post_code')->nullable();
                $table->text('about')->nullable();
                $table->boolean('terms_condition')->default(false);
                $table->boolean('email_verified')->default(false);
                $table->string('email_verify_token')->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->boolean('password_changed')->default(false);
                $table->enum('verified_status', ['unverified', 'verified'])->default('unverified');
                $table->enum('status', ['inactive', 'active'])->default('inactive');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('genders');
    }
};
