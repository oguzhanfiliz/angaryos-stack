<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('name_basic')->nullable();
            $table->integer('subscriber_type_id')->nullable();
            $table->text('php_code')->nullable();
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
        Schema::dropIfExists('subscribers');
    }
}
