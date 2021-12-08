<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsInvestorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_investor', function (Blueprint $table) {
            $table->id();
            $table->string('investor_name', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('phone_number_alternative', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('no_ktp', 100)->nullable();
            $table->string('npwp', 100)->nullable();
            $table->string('place_of_birth', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->bigInteger('gender_id')->nullable();
            $table->bigInteger('religion_id')->nullable();
            $table->bigInteger('relationship_id')->nullable();
            $table->string('job_name')->nullable();
            $table->string('emergency_name', 100)->nullable();
            $table->string('emergency_phone_number', 20)->nullable();
            $table->string('emergency_relationship', 100)->nullable();

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
        Schema::dropIfExists('ms_investor');
    }
}
