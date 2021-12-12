<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_project', function (Blueprint $table) {
            $table->id();
            $table->string('project_code', 30);
            $table->string('project_name', 100);
            $table->bigInteger('project_category_id');
            $table->double('project_value', 18, 2);
            $table->double('modal_value', 18, 2);
            $table->date('start_date');
            $table->date('finish_date');
            $table->double('estimate_profit_value', 18, 2);
            $table->bigInteger('estimate_profit_id');

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
        Schema::dropIfExists('tr_project');
    }
}
