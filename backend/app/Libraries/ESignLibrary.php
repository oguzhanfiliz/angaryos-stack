<?php

namespace App\Libraries;

use DB;

class ESignLibrary
{
    private function GetSignedTextAndOverrideMethod($params)
    {
        $record = $params['record'];
        $user = $params['user'];
        $type = $params['type'];
        $table = $params['table'];
        $column = $params['column'];
        
        if(!$column) $pattern = DB::table('tables')->find($table->id)->e_sign_pattern_t;
        else $pattern = DB::table('columns')->find($column->id)->e_sign_pattern_c;
        
        $signedText = '***';
        $overrideMethod = 'ifNotSigned';
        
        eval(helper('clear_php_code', $pattern));
        
        if($signedText == '***') custom_abort('not.build.e.sing.text');
        
        return [$overrideMethod, $signedText];
    }
    
    public function Event($params)
    {
        $n = new \Carbon\Carbon();
        
        $data =
        [
            'table_id' => $params['table']->id,
            'source_record_id' => $params['record']->id,
            'state' => TRUE,
            'created_at' => $n,
            'updated_at' => $n,
            'user_id' => $params['user']->id,
            'own_id' => $params['user']->id,
        ];
        
        [$overrideMethod, $signedText] = $this->GetSignedTextAndOverrideMethod($params);
        
        dd('$overrideMethod control');
        
        $override = DB::table('e_signs')
                        ->where('table_id', $params['table']->id)
                        ->where('source_record_id', $params['record']->id);                        
        
        if($params['column'])
        {
            $data['column_id'] = $params['column']->id;
            $override = $override->where('column_id', $params['column']->id);
        }
        
        if($overrideMethod == 'ifNotSigned') $override = $override->whereRaw('(signed_at::text = \'\') IS NOT FALSE');
        
        if($overrideMethod != 'none')  $override->delete();        
                
        $data['signed_text'] = $signedText;
        DB::table('e_signs')->insert($data);
    }
}
