<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_files', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ref_type_id');
            $table->bigInteger('ref_id');
            $table->text('directory');
            $table->string('file_name', 100);
            $table->string('mime_type', 50);
            $table->bigInteger('file_size');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('ms_files');
    }
}
