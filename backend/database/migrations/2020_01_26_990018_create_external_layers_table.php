<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalLayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_layers', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('name')->nullable();
            $table->integer('custom_layer_type_id')->nullable();
            $table->text('layer_base_url')->nullable();
            $table->string('layer_name')->nullable();
            $table->integer('layer_style_id')->nullable();
            $table->integer('srid')->nullable();
            $table->text('cql_filter')->nullable();
            $table->text('legend_url')->nullable();
            $table->jsonb('table_ids')->nullable();
            $table->integer('period')->nullable();
            
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
        Schema::dropIfExists('external_layers');
    }
}
