<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as Valid;

use App\BaseModel;

class BaseRequest extends FormRequest
{
    public $validator = NULL;
    private $customAttributes = [];
    private $customMessages = [];
    
    public function authorize()
    {
        return true;
    }

    private function getColumns()
    {
        global $pipe;
        
        $params = \Request::all();
        
        $model = new BaseModel($pipe['table']);
        $columnSet = $model->getColumnSet($model, (int)$params['column_set_id'], TRUE);
        $columns = $model->getColumnsFromColumnSet($columnSet);
        
        return $columns;
    }
    
    private function addValidation($validation)
    {
        Valid::replacer($validation->name, function($message, $attribute, $rule, $parameters) use($validation)
        {
            $message = $validation->error_message;
            foreach($parameters as $i => $p)
                $message = str_replace(':parameters['.$i.']', $p, $message);
            
            return $message;
        });
        
        Valid::extend($validation->name, function($attribute, $value, $parameters) use($validation)
        {
            $return = FALSE;
            eval(helper('clear_php_code', $validation->php_code));            
            return $return;
        });
    }
    
    public function attributes()
    {
        return $this->customAttributes;
    }
    
    public function messages()
    {
        return $this->customMessages; 
    }
    
    public function rules()
    {
        global $pipe;
        
        $selectTypesId = get_attr_from_cache('column_gui_types', 'name', 'select', 'id');
        $disabledColumns = ['id', 'updated_at', 'created_at', 'user_id', 'own_id'];
        
        $rules = [];
        
        $singleColumn = $this->input('single_column');
        
        $columns = $this->getColumns();
        foreach($columns as $column)
        {
            global $pipe;
            
            if(in_array($column->name, $disabledColumns)) continue;
            if(strlen($singleColumn) > 0 && $column->name != $singleColumn) continue;
         
            $this->customAttributes[$column->name] = $column->display_name;
            
            $rules[$column->name] = '';
            
            if(strlen($column->column_validation_ids) == 0) continue;
            
            $validationIds = json_decode($column->column_validation_ids);
            
            foreach($validationIds as $validationId)
            {
                $validation = get_attr_from_cache('column_validations', 'id', $validationId, '*');
                
                $validator = explode(':', $validation->validation_with_params)[0];
                
                $validator = get_attr_from_cache('validations', 'name', $validator, '*');
                if($validator != null) $this->addValidation ($validator);
                
                if($validation->validation_with_params == 'unique')
                {
                    $validation->validation_with_params = 'unique:'.$pipe['table'];
                    
                    if($this->segment(7) == 'update')
                    {
                        $old = get_attr_from_cache($pipe['table'], 'id', $this->segment(6), $column->name);
                        $new = $this->input($column->name);
                        
                        if($old == $new)
                            $validation->validation_with_params = '';
                    }
                }
                else if(strstr($validation->validation_with_params, 'required_for:'))
                {
                    $params = explode(':', $validation->validation_with_params)[1];
                    $params = explode(',', $params);
                    
                    if(in_array($pipe['table'], $params)) $temp = 'required';
                    else $temp = 'nullable';
                    
                    $rules[$column->name] .= $temp . '|';
                }
                    
                $rules[$column->name] .= $validation->validation_with_params . '|';
            }
            
            if($column->column_gui_type_id == $selectTypesId)
                if(strstr($rules[$column->name], 'numeric|'))
                    $this->customMessages[$column->name.'.numeric'] = ':attribute için bir değer seçmelisiniz!';
        }
        
        foreach(array_keys($_FILES) as $fileColumnName)
        {
            $rules[$fileColumnName] .= '|array';
            $rules[$fileColumnName.'.*'] = 'mimes:png,xml,gif,jpeg,jpg,txt,pdf,doc,docx,ncz,kmz,bin,dat,xls,xlsx,xlsm,rar,dwg,mpga|';
            
            $this->customAttributes[$fileColumnName.'.0'] = $this->customAttributes[$fileColumnName];
        }
        
        return $rules;
    }
    
    protected function failedValidation(Validator $validator) 
    {
        $this->validator = $validator;
    }
}
