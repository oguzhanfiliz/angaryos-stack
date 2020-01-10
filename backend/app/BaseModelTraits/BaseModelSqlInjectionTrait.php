<?php

namespace App\BaseModelTraits;

trait BaseModelSqlInjectionTrait 
{    
    private function is_sql_injectable($sql)
    {
        return count(explode(' from ', $sql)) == 2;
    }
    
    private function get_divided_select_array($sql)
    {
        $selects = explode(' from ', $sql)[0];
        $selects = str_replace(['select ', 'Select ', 'SELECT '], '', $selects);
        $selects = helper('divide_select', $selects);
        
        return $selects;
    }
    
    private function sql_injection_where($sql, $column_name, $operation, $value)
    {
        if($this->is_sql_injectable($sql)) 
        {
            $selects = $this->get_divided_select_array($sql);
            
            if(strlen(@$selects[$column_name]['column_in_where']) > 0)
                $column_name = $selects[$column_name]['column_in_where'];
            
            $where = $column_name . ' ' . $operation . ' ' . $value;
            
            if(strstr($sql, ' where '))
                $sql = str_replace(' where ', ' where ' . $where . ' and ');
            else
                $sql .= ' where ' . $where;
        }
        else
        {
            $where = $column_name . ' ' . $operation . ' ' . $value;
            $sql = 'select * from ('.$sql.') awt where ' . $where;
        }
        
        return $sql;
    }    
}