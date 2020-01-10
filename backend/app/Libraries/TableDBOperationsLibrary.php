<?php

namespace App\Libraries;

use App\BaseModel;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use DB;

class TableDBOperationsLibrary 
{
    public $geoColumns = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon'];

    public $defaultColumnNames = [ 'id', 'state', 'own_id', 'user_id', 'created_at', 'updated_at'];
    public $defaultColumnIds = [];
    
    
    
    public function __construct() 
    {
        foreach($this->defaultColumnNames as $name)
            array_push ($this->defaultColumnIds, 
                        get_attr_from_cache('columns', 'name', $name, 'id'));
    }
    
    
    
    /****  Main Subscriber Functions  ****/
    
    public function TableEvent($params)
    {
        return $this->{'TableEventFor'.ucfirst($params['type'])}($params);
    }
    
    public function ColumnEvent($params)
    {
        return $this->{'ColumnEventFor'.ucfirst($params['type'])}($params);
    }
    
    public function AddTableFullAuthToAdminUser($table)
    {
        $auths = 
        [
            'tables:'.$table['name'].':lists:0',
            'tables:'.$table['name'].':queries:0',
            'tables:'.$table['name'].':shows:0',
            'tables:'.$table['name'].':edits:0',
            'tables:'.$table['name'].':deleteds:0',
            'tables:'.$table['name'].':creates:0',
        ];

        $robotUserId = ROBOT_USER_ID;

        $auth = new \App\BaseModel('auth_groups');
        $auth->name = $table['display_name'] . ' Tam Yetki';
        $auth->auths = $auths;
        $auth->state = TRUE;
        $auth->own_id = $robotUserId;
        $auth->user_id = $robotUserId;
        $auth->save();

        $adminAuth = get_attr_from_cache('auth_groups', 'id', 1, '*');
        $adminAuth->fillVariables();

        $temp = $adminAuth->auths;
        $temp[count($temp) - 1] = $auth->id;
        $adminAuth->auths = $temp;

        $adminAuth->save();
    }
    
    public function CreateRelationColumnsForTable($table)
    {
        $robotUserId = ROBOT_USER_ID;

        $relation = new BaseModel('column_table_relations');
        $relation->name_basic = $table->display_name . ' kolonu varsayılan tablo ilişkisi';
        $relation->relation_table_id = $table->id;
        $relation->relation_source_column_id = get_attr_from_cache('columns', 'name', 'id', 'id');
        $relation->relation_display_column_id = get_attr_from_cache('columns', 'name', 'name', 'id');
        $relation->state = TRUE;
        $relation->own_id = $robotUserId;
        $relation->user_id = $robotUserId;
        $relation->save();


        $single = new BaseModel('columns');
        $single->name = $table->name.'_id';
        $single->display_name = $table->display_name;
        $single->column_db_type_id = get_attr_from_cache('column_db_types', 'name', 'integer', 'id');
        $single->column_gui_type_id = get_attr_from_cache('column_gui_types', 'name', 'select', 'id');
        $single->column_table_relation_id = $relation->id;
        $single->column_validation_ids =
        [
            get_attr_from_cache('column_validations', 'validation_with_params', 'nullable', 'id'),
            get_attr_from_cache('column_validations', 'validation_with_params', 'integer', 'id'),
            get_attr_from_cache('column_validations', 'validation_with_params', 'numeric_min:1', 'id')
        ];
        $single->state = TRUE;
        $single->own_id = $robotUserId;
        $single->user_id = $robotUserId;
        $single->save();


        $multi = new BaseModel('columns');
        $multi->name = $table->name.'_ids';
        $multi->display_name = $table->display_name.'(lar)';
        $multi->column_db_type_id = get_attr_from_cache('column_db_types', 'name', 'jsonb', 'id');
        $multi->column_gui_type_id = get_attr_from_cache('column_gui_types', 'name', 'multiselect', 'id');
        $multi->column_table_relation_id = $relation->id;
        $multi->column_validation_ids =
        [
            get_attr_from_cache('column_validations', 'validation_with_params', 'nullable', 'id'),
            get_attr_from_cache('column_validations', 'validation_with_params', 'json', 'id')
        ];
        $multi->state = TRUE;
        $multi->own_id = $robotUserId;
        $multi->user_id = $robotUserId;
        $multi->save();
    }
    
    
    
    /****    Common Functions    ****/
    
    private function ReturnFormError($columnName, $errors)
    {
        $errorMessage =
        [
            'message' => 'error',
            'errors' => [ $columnName => $errors ]
        ];

        custom_abort($errorMessage);
    }
    
    private function ReturnGeneralError($message)
    {
        $errorMessage =
        [
            'message' => $message
        ];

        custom_abort($errorMessage);
    }
    
    private function RenameColumn($tableName, $oldName, $newName)
    {
        Schema::table($tableName, function (Blueprint $table) use($oldName, $newName)
        {
            $table->renameColumn($oldName, $newName);
        });
    }
    
    private function ChangeColumn($tableName, $newColumn)
    {
        $type = get_attr_from_cache('column_db_types', 'id', $newColumn['column_db_type_id'], 'schema_code');
        
        try 
        {
            Schema::table($tableName, function (Blueprint $table) use($type, $newColumn)
            {
                if(strlen($newColumn['default']) == 0)
                    $table->{$type}($newColumn['name'])->nullable()->change();
                else
                    $table->{$type}($newColumn['name'])->default($newColumn['default'])->change();
            });
        } 
        catch (\Exception $exc) 
        {
            $this->ReturnError('column_db_type_id', [$exc->getMessage()]);
        }
    }
    
    
    
    /****    Columns Create    ****/
    
    private function ColumnEventForCreate($params) { }
    
    
    
    /****    Columns Update    ****/
    
    private function ColumnEventForUpdate($params)
    {
        $old = $params['record']->toArray();
        $new = $params['requests'];
        
        $this->UpdateColumn($old, $new);
    }
    
    
    
    /****    Column Restore    ****/
    
    private function ColumnEventForRestore($params)
    {
        $new = $params['record']->toArray();
        $old = (array)DB::table('columns')->find($new['record_id']);
        
        $this->UpdateColumn($old, $new);
    }
    
    
    
    /****    Column Delete    ****/
    
    private function ColumnEventForDelete($params)
    {
        $model = new BaseModel('tables');
        $tables = $model->where('column_ids', '@>', $params['record']->id)->get();
        foreach($tables as $t)
        {
            $t->fillVariables();
            copy_record_to_archive($t);
            
            $temp = $t->column_ids;
            $key = array_search($params['record']->id, $temp);
            unset($temp[$key]);
            
            $columnIds = [];
            foreach($temp as $id)
                array_push ($columnIds, $id);
            
            $t->column_ids = $columnIds;
            $t->save();
            
            $this->RenameColumn($t->name, $params['record']->name, 'deleted_'.$params['record']->name);
            $this->RenameColumn($t->name.'_archive', $params['record']->name, 'deleted_'.$params['record']->name);
        }
    }
    
    
    
    /****    Column Common Functions    ****/
    
    private function UpdateColumn($old, $new)
    {
        if( $old['name'] == $new['name'] 
            && $old['column_db_type_id'] == $new['column_db_type_id']
            && $old['default'] == $new['default'])
            
            return;
        
        
        $tables = DB::table('tables')->where('column_ids', '@>', $old['id'])->get();
        foreach($tables as $table)
        {
            if($old['name'] != $new['name'])
            {
                $this->RenameColumn($table->name, $old['name'], $new['name']);
                $this->RenameColumn($table->name.'_archive', $old['name'], $new['name']);
            }
            
            if($old['column_db_type_id'] != $new['column_db_type_id'] || $old['default'] != $new['default'])
            {
                $this->ChangeColumn($table->name, $new);
                $this->ChangeColumn($table->name.'_archive', $new);
            }
        }
    }
    
    
    
    /****    Table Create    ****/
    
    public function TableEventForCreate($params)
    {
        $columnIds = $this->CreateTableOnDB($params['requests']);
        $this->CreateArchiveTableOnDB($params['requests']['name'], $params['requests']['name'].'_archive');
        
        return $columnIds;
    }
    
    public function CreateTableOnDB($requests)
    {
        $columnIds = json_decode($requests['column_ids']);
        $columns = $this->GetRealColumnsByColumnIds($columnIds);

        $this->CreateTable($requests['name'], $columns);
        
        $column_ids = $this->GetColumnIdsForColumnIdsInjection($columns);        
        return ['column_ids' => json_encode($column_ids)];
    }
    
    public function CreateTable($tableName, $columns)
    {
        $geoColumns = $this->geoColumns;
        
        Schema::create($tableName, function (Blueprint $table) use($columns, $geoColumns) 
        {
            $table->bigIncrements('id');

            foreach($columns as $column)
            {
                $dbType = $column->getRelationData('column_db_type_id'); 
                if(in_array($dbType->name, $geoColumns)) continue;
                
                if(strlen($column->default) > 0)
                    $table->{$dbType->schema_code}($column->name)->default($column->default);
                else
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

                DB::statement('ALTER TABLE '.$requests['name'].' ADD COLUMN '.$column->name.' geometry('. ucfirst($dbType->name).', '.$srid.')');
            }
        }
    }
    
    public function CreateArchiveTableOnDB($tableName, $archiveTableName)
    {
        DB::select('CREATE TABLE '.$archiveTableName.' (LIKE '.$tableName.')');
        DB::select('create sequence '.$archiveTableName.'_id_seq');
        DB::select('ALTER TABLE '.$archiveTableName.' ALTER COLUMN id SET DEFAULT nextval(\''.$archiveTableName.'_id_seq\')');
        DB::select('ALTER TABLE '.$archiveTableName.' ADD CONSTRAINT '.$archiveTableName.'_pk PRIMARY KEY (id)');

        Schema::table($archiveTableName, function (Blueprint $table) 
        {
            $table->integer('record_id');
        });
    }
    
    
    
    /****    Table Update    ****/
    
    public function TableEventForUpdate($params)
    {
        $this->UpdateTableOnDB($params);
    }
    
    public function UpdateTableOnDB($params)
    {
        $table = $params['record'];
        
        $this->RenameTable($table->name, $params['requests']['name']);
        $this->RenameTable($table->name.'_archive', $params['requests']['name'].'_archive');
        
        $columnIds = json_decode($params['requests']['column_ids']);
        $columns = $this->GetRealColumnsByColumnIds($columnIds);
        
        $deletedColumnIds = $this->GetDeletedColumns($table->column_ids, $columnIds);
        $addedColumnIds = $this->GetAddedColumns($table->column_ids, $columnIds);
                
        $this->UpdateTable($params['requests']['name'], $deletedColumnIds, $addedColumnIds);
        $this->UpdateTable($params['requests']['name'].'_archive', $deletedColumnIds, $addedColumnIds);
        
        $column_ids = $this->GetColumnIdsForColumnIdsInjection($columns);        
        return ['column_ids' => json_encode($column_ids)];
    }
    
    public function UpdateTable($tableName, $deletedColumnIds, $addedColumnIds)
    {
        $geoColumns = $this->geoColumns;        
        Schema::table($tableName, function (Blueprint $table) use($deletedColumnIds, $addedColumnIds, $geoColumns) 
        {
            foreach($addedColumnIds as $columnId)
            {
                $column = get_attr_from_cache('columns', 'id', $columnId, '*');
                $dbType = $column->getRelationData('column_db_type_id'); 
                if(in_array($dbType->name, $geoColumns)) continue;
                $table->{$dbType->schema_code}($column->name)->nullable();
            }
            
            foreach($deletedColumnIds as $columnId)
            {
                $column = get_attr_from_cache('columns', 'id', $columnId, '*');
                $table->renameColumn($column->name, 'deleted_'.$column->name);
            }
        });

        foreach($addedColumnIds as $columnId)
        {
            $column = get_attr_from_cache('columns', 'id', $columnId, '*');
            $dbType = $column->getRelationData('column_db_type_id'); 
            if(in_array($dbType->name, $geoColumns))
            {
                $srid = $column->srid;
                if(strlen($srid) == 0) $srid = DB_PROJECTION;

                DB::statement('ALTER TABLE '.$params['requests']['name'].' ADD COLUMN '.$column->name.' geometry('. ucfirst($dbType->name).', '.$srid.')');
            }
        }
    }
    
    
    
    /****    Table Delete    ****/
    
    public function TableEventForDelete($params)
    {
        $this->DeleteTableOnDB($params);
    }
    
    private function DeleteTableOnDB($params)
    {
        $table = $params['record'];
        
        $this->RenameTable($table->name, 'deleted_'.$table->name);
        $this->RenameTable($table->name.'_archive', 'deleted_'.$table->name.'_archive');
    }
    
    
    
    /****    Table Restore    ****/
    
    public function TableEventForRestore($params)
    {
        $this->RestoreTableOnDB($params);
    }
    
    private function RestoreTableOnDB($params)
    {
        $columns = $params['record']->getRelationData('column_ids');
        $this->ReturnErrorIFTableHasDeletedRecord($columns);
        
        
        $newTable = $params['record'];
        
        $oldTable = new BaseModel('tables');
        $oldTable = $oldTable->find($newTable->record_id);
        
        $this->RenameTable($oldTable->name, $newTable->name);
        $this->RenameTable($oldTable->name.'_archive', $newTable->name.'_archive');
        
        $columns = $this->GetRealColumnsByColumnIds($newTable->column_ids);
        
        $deletedColumnIds = $this->GetDeletedColumns($oldTable->column_ids, $newTable->column_ids);
        $addedColumnIds = $this->GetAddedColumns($oldTable->column_ids, $newTable->column_ids);
               
        $this->RestoreTable($newTable->name, $deletedColumnIds, $addedColumnIds);
        $this->RestoreTable($newTable->name.'_archive', $deletedColumnIds, $addedColumnIds);
        
        return TRUE;
    }
    
    private function ReturnErrorIFTableHasDeletedRecord($columns)
    {
        foreach($columns as $column)
            if(substr($column->name, 0, 8) == 'deleted_' && SHOW_DELETED_TABLES_AND_COLUMNS != '1')
                $this->ReturnGeneralError('table.has.deleted.column: "'.$column->display_name.'"');
    }
    
    private function RestoreTable($tableName, $deletedColumnIds, $addedColumnIds)
    {
        $geoColumns = $this->geoColumns;        
        Schema::table($tableName, function (Blueprint $table) use($deletedColumnIds, $addedColumnIds, $geoColumns) 
        {
            foreach($addedColumnIds as $columnId)
            {
                $column = get_attr_from_cache('columns', 'id', $columnId, '*');
                
                if(substr($column->name, 0, 8) == 'deleted_') continue;
                
                $table->renameColumn('deleted_'.$column->name, $column->name);
            }
            
            foreach($deletedColumnIds as $columnId)
            {
                $column = get_attr_from_cache('columns', 'id', $columnId, '*');
                
                if(substr($column->name, 0, 8) == 'deleted_') continue;
                
                $table->renameColumn($column->name, 'deleted_'.$column->name);
            }
        });
    }
    
    
        
    /****  Table Common Functions  ****/
    
    public function GetRealColumnsByColumnIds($columnIds)
    {
        $columns = [];
        foreach($columnIds as $columnId)
        {
            $column = get_attr_from_cache('columns', 'id', $columnId, '*');
            if(in_array($column->name, $this->defaultColumnNames)) continue;

            array_push($columns, $column);
        }
        
        return $columns;
    }
    
    public function GetColumnIdsForColumnIdsInjection($columns)
    {
        $column_ids = [];
        
        array_push($column_ids, $this->defaultColumnIds[array_search('id', $this->defaultColumnNames)]);
        
        foreach($columns as $column)
            array_push($column_ids, $column->id);
        
        
        array_push($column_ids, $this->defaultColumnIds[array_search('state', $this->defaultColumnNames)]);
        array_push($column_ids, $this->defaultColumnIds[array_search('own_id', $this->defaultColumnNames)]);
        array_push($column_ids, $this->defaultColumnIds[array_search('user_id', $this->defaultColumnNames)]);
        array_push($column_ids, $this->defaultColumnIds[array_search('created_at', $this->defaultColumnNames)]);
        array_push($column_ids, $this->defaultColumnIds[array_search('updated_at', $this->defaultColumnNames)]);
        
        return $column_ids;
    }
    
    public function RenameTable($old, $new)
    {
        if($old == $new) return;
        
        Schema::rename($old, $new);
    }
    
    public function GetRealColumnsFromColumnIds($columnIds)
    {
        $columns = [];
        foreach($columnIds as $columnId)
            if(!in_array($columnId, $this->defaultColumnIds))
            {
                $column = get_attr_from_cache('columns', 'id', $columnId, '*');
                array_push($columns, $column);
            }
            
        return $columns;
    }
    
    public function GetDeletedColumns($currentColumnIds, $columnIds)
    {        
        $temp = [];
        foreach($currentColumnIds as $columnId)
            if(!in_array($columnId, $columnIds))
                array_push($temp, $columnId);
        
        $deletedColumnIds = [];
        foreach($temp as $key => $id)
            if(!in_array($id, $this->defaultColumnIds))
                array_push($deletedColumnIds, $id);
            
        return $deletedColumnIds;
    }
    
    public function GetAddedColumns($currentColumnIds, $columnIds)
    {
        $temp = [];
        foreach($columnIds as $columnId)
            if(!in_array($columnId, $currentColumnIds))
                array_push($temp, $columnId);
            
        $addedColumnIds = [];
        foreach($temp as $key => $id)
            if(!in_array($id, $this->defaultColumnIds))
                array_push($addedColumnIds, $id);
            
        return $addedColumnIds;
    }
}