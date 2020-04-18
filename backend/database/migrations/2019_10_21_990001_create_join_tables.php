<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJoinTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('join_tables', function (Blueprint $table) 
        {
            $table->bigIncrements('id');
            
            $table->string('name_basic')->nullable();
            
            $table->integer('join_table_id')->nullable();
            $table->string('join_table_alias')->nullable();
            
            $table->string('connection_column_with_alias')->nullable();
            
            //$table->string('join_connection_type')->nullable();
            
            $table->integer('join_column_id')->nullable();
            
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
        Schema::dropIfExists('join_tables');
    }
}
