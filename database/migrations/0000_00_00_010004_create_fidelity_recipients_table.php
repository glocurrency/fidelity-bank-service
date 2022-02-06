<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFidelityRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fidelity_recipients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('street')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->char('country_code', 3);
            $table->string('phone_number')->nullable();
            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fidelity_recipients');
    }
}
