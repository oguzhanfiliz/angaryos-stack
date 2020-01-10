<?php

namespace App\Repositories;

use DB;

class AuthsRepository 
{
    //dataFor_fillRelationDataForDataSource
    public function getRecordsBySourceData($data)
    {
        $allAuths = helper('get_all_auths');
        $list = json_decode($data);
        
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
    
    public function getRecordsForListBySourceData($data)
    {
        $data = $this->getRecordsBySourceData($data);
        
        $return = [];
        foreach($data as $item)
            $return[$item->_source_column] = $item->_display_column;
        
        return $return;
    }
    
    public function searchRecords($serach, $page, $limit = REC_COUNT_PER_PAGE)
    {
        $return = [];

        $start = ($page-1) * $limit;
        $stop = $start + $limit;

        $i = 0;
        $more = false;

        $serach = helper('tr_strtolower', $serach);
        
        $all_auths = helper('get_all_auths');
        foreach($all_auths as $source => $display)
        {
            $sourceL = helper('tr_strtolower', $source);
            $displayL = helper('tr_strtolower', $display);
            
            if($serach == '' || strstr($sourceL, $serach) || strstr($displayL, $serach))
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
    
    public function whereRecords($serach)
    {
        $list = json_decode($serach);

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
}
