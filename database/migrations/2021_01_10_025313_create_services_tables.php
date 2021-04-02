<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_definitions', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('company_id');
            $table->string('name');
            $table->integer('price');
            $table->integer('duration');
            $table->timestamps();

            $table->primary('id');
            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('service_definition_id');
            $table->uuid('booking_id');
            $table->timestamps();

            $table->primary('id');
            $table->foreign('service_definition_id')->references('id')->on('service_definitions');
            $table->foreign('booking_id')->references('id')->on('bookings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('service_definitions');
        Schema::dropIfExists('services');
        Schema::enableForeignKeyConstraints();
    }
}