<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColumnArraysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('column_arrays', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('name_basic')->nullable();
            
            $table->integer('column_array_type_id')->nullable();
            
            $table->integer('table_id')->nullable();
            $table->jsonb('column_ids')->nullable();
            
            $table->jsonb('join_table_ids')->nullable();
            $table->text('join_columns')->nullable();
            
            $table->text('description')->nullable();
            
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
        Schema::dropIfExists('column_arrays');
    }
}
