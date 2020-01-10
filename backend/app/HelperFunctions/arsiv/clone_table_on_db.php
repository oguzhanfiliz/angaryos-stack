<?php
dd('function moved clone_table_on_db');

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

\DB::select('CREATE TABLE '.$params[1].' (LIKE '.$params[0].')');
\DB::select('create sequence '.$params[1].'_id_seq');
\DB::select('ALTER TABLE '.$params[1].' ALTER COLUMN id SET DEFAULT nextval(\''.$params[1].'_id_seq\')');
\DB::select('ALTER TABLE '.$params[1].' ADD CONSTRAINT '.$params[1].'_pk PRIMARY KEY (id)');

Schema::table($params[1], function (Blueprint $table) 
{
    $table->integer('record_id');
});