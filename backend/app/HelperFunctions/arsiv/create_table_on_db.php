<?php
dd('deleted tihs function create_table_on_db');
use App\BaseModel;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

$defaultColumnNames = [ 'id', 'state', 'own_id', 'user_id', 'created_at', 'updated_at', 'own', 'all' ];

$columnIds = json_decode($params['column_ids']);

$columns = [];
foreach($columnIds as $columnId)
{
    $column = get_attr_from_cache('columns', 'id', $columnId, '*');
    if(in_array($column->name, $defaultColumnNames)) continue;
    
    array_push($columns, $column);
}

$geoColumns = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon'];

Schema::create($params['name'], function (Blueprint $table) use($columns, $geoColumns) 
{
    $table->bigIncrements('id');
    
    foreach($columns as $column)
    {
        $dbType = $column->getRelationData('column_db_type_id'); 
        if(in_array($dbType->name, $geoColumns)) continue;
        $table->{$dbType->schema_code}($column->name)->nullable();
    }
        
    $table->boolean('state')->default(TRUE)->nullable();
    $table->integer('own_id')->nullable();
    $table->integer('user_id')->nullable();
    $table->timestamps();
});

foreach($columns as $column)
{
    $dbType = $column->getRelationData('column_db_type_id'); 
    if(in_array($dbType->name, $geoColumns))
    {
        $srid = $column->srid;
        if(strlen($srid) == 0) $srid = DB_PROJECTION;
        
        \DB::statement('ALTER TABLE '.$params['name'].' ADD COLUMN '.$column->name.' geometry('. ucfirst($dbType->name).', '.$srid.')');
    }
}