<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataSourceDirectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_source_directions', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('name')->nullable();
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
        Schema::dropIfExists('data_source_directions');
    }
}
