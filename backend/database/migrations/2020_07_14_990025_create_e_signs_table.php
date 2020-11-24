<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateESignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_signs', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->integer('table_id')->nullable();
            $table->integer('source_record_id')->nullable();
            $table->integer('column_id')->nullable();
            $table->text('signed_text')->nullable();
            $table->timestamp('sign_at')->nullable();
            $table->jsonb('sign_file')->nullable();
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
        Schema::dropIfExists('e_signs');
    }
}
