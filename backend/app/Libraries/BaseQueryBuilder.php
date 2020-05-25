<?php

namespace App\Libraries;

use Illuminate\Support\Arr;
use Illuminate\Database\Query\Builder;
use DB;

class BaseQueryBuilder extends Builder
{
    public function aggregate($function, $columns = ['*'])
    {
        $results = $this->cloneWithout($this->unions ? [] : ['columns'])
                        ->cloneWithoutBindings($this->unions ? [] : ['select'])
                        ->setAggregate($function, $columns)
                        ->distinct()//added
                        ->get($columns);
        if (! $results->isEmpty()) 
        {
            if(count($results) == 1)
                return array_change_key_case((array) $results[0])['aggregate'];
            
            $return = [];
            foreach($results as $rec)
                array_push ($return, $rec->aggregate);
                    
            return $return;
        }
        else return 0;
    }
    
    public function count($columns = '*')
    {
        $count = $this->aggregate(__FUNCTION__, Arr::wrap($columns));
        
        if($count == NULL) return 0;
        if(is_numeric($count)) return $count;
        
        return count($count);
    }
    
    public function sum($columns = '*')
    {
        $sum = $this->aggregate(__FUNCTION__, [$columns]);
        
        if($sum == NULL) return 0;
        if(is_numeric($sum)) return $sum;
        
        return array_sum($sum);
    }
    
    public function max($columns = '*')
    {
        $max = $this->aggregate(__FUNCTION__, [$columns]);
        
        if($max == NULL) return 0;
        if(is_numeric($max)) return $max;
        
        return max($max);
    }
    
    public function min($columns = '*')
    {
        $min = $this->aggregate(__FUNCTION__, [$columns]);
        
        if($min == NULL) return 0;
        if(is_numeric($min)) return $min;
        
        return min($min);
    }
    
    public function avg($columns = '*')
    {
        $avg = $this->aggregate(__FUNCTION__, [$columns]);
        
        if($avg == NULL) return 0;
        if(is_numeric($avg)) return $avg;
        
        $divide = 0;
        $sum = 0;
        $count = $this->aggregate('count', Arr::wrap($columns));
        foreach($count as $i => $multiplier)
        {
            $divide += $multiplier;
            $sum += $multiplier * $avg[$i];
        }
        
        return $divide == 0 ? 0 : $sum / $divide;
    }
    
    public function get($columns = ['*'])
    {
        $this->addSelectsWithGeoInjection();
        return parent::get($columns);
    }
    
    private function addSelectsWithGeoInjection()
    {
        if($this->columns == NULL)
            $this->addSelectsWithGeoInjectionForAllColumns();
        else
            $this->addSelectsWithGeoInjectionForAddedColumns();
    }
    
    private function addSelectsWithGeoInjectionForAllColumns()
    {
        $columns = helper('get_all_columns_from_db', $this->from);
        foreach($columns as $column)
        {
            if(strlen($column['srid']) == 0)
                $this->addSelect($column['name']);
            else
            {
                $temp = 'ST_AsText('.$column['name'].') as ' . $column['name'];
                $this->addSelect(DB::raw($temp));
            }
        }
    }
    
    private function addSelectsWithGeoInjectionForAddedColumns()
    {
        $geos = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon'];
        
        foreach($this->columns as $key => $value)
        {
            if(is_object($value)) continue;
            if(strstr($value, ' ')) continue;
            
            $name = last(explode('.', $value));
            if($name == '*') continue;
            
            $dbTypeId = get_attr_from_cache('columns', 'name', $name, 'column_db_type_id');
            if(strlen($dbTypeId) == 0) dd('addSelectsWithGeoInjectionForAddedColumns');
            
            $type = get_attr_from_cache('column_db_types', 'id', $dbTypeId, 'name');

            if(in_array($type, $geos))
            {
                $temp = 'ST_AsText('.$value.') as ' . $name;
                $this->addSelect(DB::raw($temp));
            }
        }
    }
}