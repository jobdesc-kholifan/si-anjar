<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrProjectSkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_project_sk', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id');
            $table->integer('revision');
            $table->string('no_sk', 30);
            $table->text("pdf_payload")->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->boolean('is_draft')->nullable();
            $table->boolean('status_id')->nullable();

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
        Schema::dropIfExists('tr_project_sk');
    }
}
