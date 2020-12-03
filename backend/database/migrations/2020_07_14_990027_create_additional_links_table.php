<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->integer('additional_link_type_id')->nullable();
            $table->integer('table_group_id')->nullable();
            $table->string('name_basic')->nullable();
            $table->text('url')->nullable();
            $table->boolean('open_new_window')->nullable();
            $table->text('payload')->nullable();
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
        Schema::dropIfExists('additional_links');
    }
}
