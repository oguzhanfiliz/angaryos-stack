<?php

namespace App\Libraries;

use DB;

class ESignLibrary
{
    private function CreateSignedText($params)
    {
        $record = $params['record'];
        $user = $params['user'];
        $type = $params['type'];
        $table = $params['table'];
        $column = $params['column'];
        
        if(!$column) $pattern = DB::table('tables')->find($table->id)->e_sign_pattern_t;
        else $pattern = DB::table('columns')->find($column->id)->e_sign_pattern_c;
        
        $signedText = '***';
        
        eval(helper('clear_php_code', $pattern));
        
        if($signedText == '***') custom_abort('not.build.e.sing.text');
        
        return $signedText;
    }
    
    public function Event($params)
    {
        $n = new \Carbon\Carbon();
        
        $data =
        [
            'table_id' => $params['table']->id,
            'source_record_id' => $params['record']->id,
            'table_id' => $params['table']->id,
            'state' => TRUE,
            'created_at' => $n,
            'updated_at' => $n,
            'user_id' => $params['user']->id,
            'own_id' => $params['user']->id,
        ];
        
        if($params['column']) $data['column_id'] = $params['column']->id;
        $data['signed_text'] = $this->CreateSignedText($params);
        
        DB::table('e_signs')->insert($data);
    }
}
