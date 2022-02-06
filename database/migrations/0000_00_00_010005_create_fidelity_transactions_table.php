<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFidelityTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fidelity_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaction_id')->unique()->index();
            $table->uuid('processing_item_id')->index();
            $table->uuid('fidelity_sender_id')->unique()->index();
            $table->uuid('fidelity_recipient_id')->unique()->index();

            $table->string('state_code');
            $table->longText('state_code_reason')->nullable();

            $table->string('status_code')->nullable();
            $table->longText('status_code_description')->nullable();
            $table->string('error_code')->nullable();
            $table->longText('error_code_description')->nullable();

            $table->string('reference')->unique();
            $table->unsignedSmallInteger('request_postfix');

            $table->char('send_currency_code', 3);
            $table->double('send_amount');
            $table->char('receive_currency_code', 3);
            $table->double('receive_amount');
            $table->string('bank_code');
            $table->string('account_number');

            $table->timestamps(6);
            $table->softDeletes('deleted_at', 6);

            $table->foreign('fidelity_sender_id')->references('id')->on('fidelity_senders');
            $table->foreign('fidelity_recipient_id')->references('id')->on('fidelity_recipients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fidelity_transactions');
    }
}
