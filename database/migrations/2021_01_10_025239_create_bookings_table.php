<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('employee_id');
            $table->uuid('client_id')->nullable();
            $table->dateTime('started_at');
            $table->dateTime('ended_at');
            $table->dateTime('cancelled_at')->nullable();
            $table->uuid('cancelled_by')->nullable();
            $table->timestamps();

            $table->primary('id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('client_id')->references('id')->on('clients');
        });

        Schema::table('time_slots', function (Blueprint $table) {
            $table->uuid('booking_id')->nullable()->after('company_id');
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
        Schema::dropIfExists('bookings');
        Schema::enableForeignKeyConstraints();
    }
}