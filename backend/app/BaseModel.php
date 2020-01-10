<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use \App\BaseModelTraits\BaseModelSqlInjectionTrait;
use \App\BaseModelTraits\BaseModelGetRelationDataTrait;
use \App\BaseModelTraits\BaseModelGetDataTrait;
use \App\BaseModelTraits\BaseModelSelectColumnDataTrait;

use \App\Libraries\BaseQueryBuilder;

use \Cache;
use \DB;

class BaseModel extends Model
{   
    use BaseModelSqlInjectionTrait;
    use BaseModelGetRelationDataTrait;
    use BaseModelGetDataTrait;
    use BaseModelSelectColumnDataTrait;
    
    
    
    protected $dates = ['created_at', 'updated_at'];
    protected $fillable = [], $casts = [];
    protected $allColumnsFromDB = [];
    
    
    
    public function __construct($table = NULL, array $attributes = []) 
    {
        if($table) $this->setTable($table);
        
        $this->fillVariables();
        
        parent::__construct($attributes); 
    }
    
    public function fillVariables()
    {
        if(count($this->fillable) > 0) return;
        
        $this->fillAllColumnsFromDB();
        $this->fillFillableColumns();
        $this->fillCastsColumns();        
    }
    
    public function getAllColumnsFromDB()
    {
        if($this->allColumnsFromDB == [])
            $this->fillAllColumnsFromDB ();
        
        return $this->allColumnsFromDB;
    }
    
    
    
    /****    Override    ****/
    
    public function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return new BaseQueryBuilder(
            $connection, $connection->getQueryGrammar(), $connection->getPostProcessor(), $this
        );
    }
    
    public function find($id, $columns = ['*'])
    {
        $return = parent::find($id, $columns);
        if($return == NULL) return NULL;
        
        $return->fillVariables();
        
        return $return;
    }
    
    public function save(array $options = [])
    {
        $this->fillVariables();
        
        $orj = [];
        foreach($this->getAllColumnsFromDB() as $column_name => $column)
            if(strlen($column['srid']) > 0)
                if(strlen($this->{$column['name']}) > 0)
                    if(!is_object($this->{$column['name']}))
                    {
                        $orj[$column['name']] = $this->{$column['name']} ;
                        $this->{$column['name']} = DB::raw('ST_GeomFromText(\''.$this->{$column['name']}.'\', '.$column['srid'].')');
                    }
                    
        $return = parent::save($options);
        
        foreach($orj as $column_name => $value) 
        {
            $this->{$column_name} = $value;
            $this->original[$column_name] = $value;
        }
        
        return $return;
    }
    
    
    
    /*****    ****/
    
    /*public function archive($columnArrayId, $page, $limit)
    {
        //column array bile olmadan herşeyi geri dönebilir tüm kolonları
        custom_abort(999);
        $start = ($page - 1) * $limit;
        
        $model = DB::table($this->getTable().'_archive')
                ->where('record_id', $this->id);
        
        $count = $model->count();
        
        $data = $model->limit($limit)->offset($start) ->get();
        
        $columns = $this->getColumnsByColumnArrayId($this->getQuery(), $columnArrayId);
        $columns = $this->getFilteredColumns($columns);
        
        $records = [];
        foreach($data as $record)
        {
            $temp = [];
            foreach($columns as $column)
                $temp[$column->name] = $record->{$column->name};
            array_push($records, $temp);
        }
        
        return 
        [
            'records' => $records, 
            'columns' => $columns,
            'count' => $count
        ];
    }*/
    
    public function fillAllColumnsFromDB()
    {
        $this->allColumnsFromDB = helper('get_all_columns_from_db', $this->getTable());
    }
    
    public function fillFillableColumns()
    {
        if($this->allColumnsFromDB == []) return;
        $this->fillable = $this->getFillableColumns();
    }
    
    public function fillCastsColumns()
    {
        if($this->allColumnsFromDB == []) return;
        $this->casts = $this->getCastsColumns();
    }
    
    public function getFillableColumns()
    {
        $cacheName = 'tableName:'.$this->getTable().'|fillableColumns';
        
        $fillable = Cache::rememberForever($cacheName, function()
        {      
            $temp = [];
            
            foreach($this->allColumnsFromDB as $column)
                array_push($temp, $column['name']);
            
            return $temp;
        });
        
        return $fillable;
    }
    
    public function getCastsColumns()
    {
        $cache_name = 'tableName:'.$this->getTable().'|castsColumns';
        
        $casts = Cache::rememberForever($cache_name, function()
        {      
            $temp = [];
            
            foreach($this->allColumnsFromDB as $column)
                if(strstr($column['type'], 'json'))
                    $temp[$column['name']] = 'array';
            
            return $temp;
        });
        
        return $casts;
    }
    
     public function getRelationData($columnName)
    {
        $column = get_attr_from_cache('columns', 'name', $columnName, '*');
        if(strlen($column->column_table_relation_id) == 0)
            return $this->{$columnName};
        
        if(isset($this->{$columnName . '__relation_data'}))
            return $this->{$columnName . '__relation_data'};
            
        $json = $this->{$columnName};
        if(is_array($this->{$columnName}))
            $json = json_encode($this->{$columnName});
            
        $cacheName = 'tableName:'.$this->getTable().'|id:'.$this->id.'|columnName:'.$columnName.'|columnData:'.$json.'|relationData';
        $th = $this;
        
        return Cache::rememberForever($cacheName, function() use($th, $column)
        {      
            $th->fillRelationData($column);
            return $this->{$columnName . '__relation_data'};
        });
    }
}