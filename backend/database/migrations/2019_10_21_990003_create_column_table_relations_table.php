<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColumnTableRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('column_table_relations', function (Blueprint $table) 
        {
            $table->bigIncrements('id');
            
            $table->string('name_basic')->nullable();
            
            $table->integer('relation_table_id')->nullable();
            $table->integer('relation_source_column_id')->nullable();
            $table->integer('relation_display_column_id')->nullable();
            
            $table->jsonb('join_table_ids')->nullable();
            
            $table->text('relation_sql')->nullable();            
            $table->text('relation_source_column')->nullable();
            $table->text('relation_display_column')->nullable();
            
            $table->integer('column_data_source_id')->nullable();
            
            //$table->integer('up_column_id')->nullable();
            $table->text('description')->nullable();
            
            $table->boolean('state')->default(TRUE)->nullable();
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
        Schema::dropIfExists('column_table_relations');
    }
}
