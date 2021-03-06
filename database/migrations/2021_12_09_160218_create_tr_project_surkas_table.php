<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrProjectSurkasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_project_surkas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id');
            $table->string('surkas_no', 30);
            $table->double('surkas_value', 18, 2);
            $table->date('surkas_date');
            $table->double('admin_fee', 18, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('other_description')->nullable();

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
        Schema::dropIfExists('tr_project_surkas');
    }
}
