<?php

dd('get_auths was moved');

if($params['db_type'] != 'jsonb') dd('auths kolon tipi yanlış!');
   


/****   Functions    ****/
/*
if(!function_exists('dataFor_updataDataFromDataSource'))
{
    function dataFor_updataDataFromDataSource($params)
    {
        $data = dataFor_fillRelationDataForDataSource($params);
        $return = [];
        foreach($data as $item)
            $return[$item->_source_column] = $item->_display_column;
        
        return $return;
    }
}
*/
/*if(!function_exists('dataFor_getSelectColumnDataForDataSource'))
{
    function dataFor_getSelectColumnDataForDataSource($params)
    {
        $return = [];

        $start = ($params['page']-1) * $params['limit'];
        $stop = $start + $params['limit'];

        $i = 0;
        $more = false;

        $params['data'] = helper('tr_strtolower', $params['data']);
        
        $all_auths = helper('get_all_auths');
        foreach($all_auths as $source => $display)
        {
            $sourceL = helper('tr_strtolower', $source);
            $displayL = helper('tr_strtolower', $display);
            
            if($params['data'] == '' || strstr($sourceL, $params['data']) || strstr($displayL, $params['data']))
            {
                if($i >= $start)
                    $return[$source] = $display;     

                $i++;
            }

            if($stop == $i)
            {
                $more = TRUE;
                break;
            }
        }

        return ['records' => $return, 'more' => $more];
    }
}*/
/*
if(!function_exists('dataFor_fillRelationDataForDataSource'))
{    
    function dataFor_fillRelationDataForDataSource($params)
    {
        $allAuths = helper('get_all_auths');
        $list = json_decode($params['data']);
        
        if(!is_array($list)) return [];
        
        $return = [];
        foreach($list as $item)
        {
            $temp = helper('get_null_object');
            $temp->_source_column = $item;
            
            if(!is_numeric($item))
                $temp->_display_column = $allAuths[$item];
            else
            {
                $ag = \DB::table('auth_groups')->find($item);
                $temp->_display_column = $ag->name;
            }
            
            $temp->_source_column_name = '_source_column';
            $temp->_display_column_name = '_display_column_name';
                
            array_push($return,  $temp);
        }
        
        return $return;
    }
}
*/
/*if(!function_exists('dataFor_addWhereForDataSourceJsonb'))
{
    function dataFor_addWhereForDataSourceJsonb($params)
    {
        $list = json_decode($params['data']);

        $len = count($list);
        $i = 0;

        $return = [];
        while($i < $len)
        {
            $model = DB::table('auth_groups');
            if(is_numeric($list[$i]))
                $model->whereRaw('auths @> \''.$list[$i].'\'::jsonb');
            else
                $model->where('auths', 'like', '%"'.$list[$i].'"%');
            $temp = $model->get();

            if(count($temp) > 0)
                foreach($temp as $auth)
                {
                    array_push($list, $auth->id);
                    array_push($return, $auth->id);

                    $len++;
                }

            $i++;
        }

        return $return;
    }
}*/


/****    Action    ****/

$params['user'] = new \App\User();
$params['caller_function'] = 'dataFor_'.$params['caller_function']; 

return $params['caller_function']($params);


/*
if(strlen($params['source']) > 0)
{
    
    
    //görünen ad ile değitirilecek
    foreach($auths as $auth)
        $return[$auth] = $auth;
}
else if(strlen($params['display']) > 0)
{
    $return = [];
    $all = get_all();
    foreach($all as $source => $display)
        dd($source);
}
else
{
    dd('getAll');
}

return json_encode($return);*/