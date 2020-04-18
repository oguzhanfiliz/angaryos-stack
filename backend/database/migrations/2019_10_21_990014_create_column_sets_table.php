<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColumnSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('column_sets', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('name_basic')->nullable();
            //$table->string('display_name')->nullable();
            $table->integer('table_id')->nullable();
            $table->integer('column_set_type_id')->nullable();
            $table->jsonb('column_array_ids')->nullable();
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
        Schema::dropIfExists('column_sets');
    }
}
