<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeServiceDefinitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_service_definition', function (Blueprint $table) {
            $table->id();
            $table->uuid('employee_id');
            $table->uuid('service_definition_id');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('service_definition_id')->references('id')->on('service_definitions');
            $table->index(['employee_id', 'service_definition_id'], 'employee_service_definition_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
