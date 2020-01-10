<?php

dd('bu fonksiyon hiç kullanılmıyor olabilir');
$return = [];
foreach($params as $select)
{
    if(strstr($select, '.')) 
    {
        $temp = explode('.', $select);
        $return[$temp[1]] = 
        [
            'table_alias' => helper('clear_column_name_from_divided_operation_chars', $temp[0]),
            'column' => helper('clear_column_name_from_divided_operation_chars', $temp[1]),
            'column_in_where' => helper('clear_column_name_from_divided_operation_chars', $temp[0]).'.'.helper('clear_column_name_from_divided_operation_chars', $temp[1])
        ];
    }
    else if(strstr($select, ' as ')) 
    {
        $temp = explode(' as ', $select);
        $return[$temp[1]] = 
        [
            'table_alias' => '',
            'column' => helper('clear_column_name_from_divided_operation_chars', $temp[1]),
            'column_in_where' => helper('clear_column_name_from_divided_operation_chars', $temp[0]),
        ];
    }
    else
        $return[$select] = 
        [
            'table_alias' => '',
            'column' => $select,
            'column_in_where' => $select
        ];
}

return $return;