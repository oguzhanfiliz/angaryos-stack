<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataSourceRemoteColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_source_remote_columns', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->integer('data_source_rmt_table_id')->nullable();
            $table->string('name_basic')->nullable();
            $table->string('db_type_name')->nullable();
            
            $table->boolean('state')->nullable();
            $table->integer('own_id');
            $table->integer('user_id');
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
        Schema::dropIfExists('data_source_remote_columns');
    }
}
