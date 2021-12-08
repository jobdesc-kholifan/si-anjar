<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsInvestorBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_investor_bank', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('investor_id');
            $table->bigInteger('bank_id');
            $table->string('branch_name', 100);
            $table->string('no_rekening', 100);
            $table->string('atas_nama', 100);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_investor_bank');
    }
}
