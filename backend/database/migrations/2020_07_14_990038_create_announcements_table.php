<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('title')->nullable();
            $table->text('announcement')->nullable();
            $table->text('description')->nullable();
            
            $table->string('type')->nullable();
            $table->string('icon')->nullable();
            
            $table->boolean('all_users')->nullable();
            $table->jsonb('department_ids')->nullable();
            $table->jsonb('user_ids')->nullable();
            
            $table->boolean('sms')->nullable();
            $table->boolean('mail')->nullable();
            $table->boolean('notification')->nullable();
            $table->boolean('web')->nullable();
            
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            
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
        Schema::dropIfExists('announcements');
    }
}
