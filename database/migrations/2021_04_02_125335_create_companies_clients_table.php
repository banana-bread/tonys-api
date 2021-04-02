<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies_clients', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('client_id');
            $table->uuid('company_id');
            $table->timestamps();

            $table->primary('id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('companies_clients');
        Schema::enableForeignKeyConstraints();
    }
}
