<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->boolean('subscribed_to_emails')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->primary('id');
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id')->unique();
            $table->timestamps();

            $table->primary('id');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id')->unique();
            $table->uuid('company_id');
            $table->boolean('admin');
            $table->boolean('owner');
            $table->boolean('bookings_enabled');
            $table->json('settings');
            $table->timestamps();

            $table->primary('id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('users');
    }
}
