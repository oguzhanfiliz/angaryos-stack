<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('columns', function (Blueprint $table) 
        {
            $table->bigIncrements('id');
            
            $table->string('display_name')->nullable();
            $table->string('name')->nullable();
            $table->integer('column_db_type_id')->nullable();
            $table->integer('column_gui_type_id')->nullable();
            $table->integer('srid')->nullable();
            $table->integer('up_column_id')->nullable();
            $table->integer('column_table_relation_id')->nullable();
            $table->jsonb('subscriber_ids')->nullable();
            $table->jsonb('column_validation_ids')->nullable();
            $table->jsonb('column_gui_trigger_ids')->nullable();
            $table->integer('column_collective_info_id')->nullable();
            $table->text('default')->nullable();
            $table->text('e_sign_pattern_c')->nullable();
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
        Schema::dropIfExists('columns');
    }
}
