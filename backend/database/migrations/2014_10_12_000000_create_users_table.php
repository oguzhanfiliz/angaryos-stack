<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) 
        {
            $table->bigIncrements('id');
            
            $table->jsonb('profile_picture')->nullable();
            $table->string('tc')->nullable();
            $table->string('name_basic')->nullable();
            $table->string('surname')->nullable();
            $table->integer('department_id')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->jsonb('tokens')->nullable();
            $table->jsonb('auths')->nullable();
            $table->integer('srid')->nullable();
            
            $table->rememberToken();
            
            $table->boolean('state')->default(TRUE)->nullable();
            $table->integer('own_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
        });
        
        DB::statement('ALTER TABLE users ADD COLUMN location geometry(Point, '.DB_PROJECTION.')');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
