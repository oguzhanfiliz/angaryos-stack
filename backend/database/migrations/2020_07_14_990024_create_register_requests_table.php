<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('tc')->nullable();
            $table->string('name_basic')->nullable();
            $table->string('surname')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->jsonb('detail')->nullable();
            
            
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
        Schema::dropIfExists('public_contents');
    }
}
