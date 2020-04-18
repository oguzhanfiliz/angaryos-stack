<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataSourceTblRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_source_tbl_relations', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->integer('data_source_id')->nullable();
            $table->integer('table_id')->nullable();
            $table->integer('data_source_rmt_table_id')->nullable();
            $table->integer('data_source_direction_id')->nullable();
            $table->string('cron')->nullable();
            $table->jsonb('data_source_col_relation_ids')->nullable();
            
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
        Schema::dropIfExists('data_source_tbl_relations');
    }
}
