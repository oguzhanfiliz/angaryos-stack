<?php
global $pipe;

if(is_string($validation))
    $validation = [$validation];

if($validation == []) return true;

$params = (Array)$params;
if(in_array('required', $validation) && !isset($params[$name])) 
    abort(helper('response_error', 'column.is.required: '.$name));

if(in_array('*auto*', $validation)) 
{
    $column = get_attr_from_cache('columns', 'name', $name, '*');
    if($column == NULL) 
    {
        $arr = ['required'];
    }
    else
    {
        $column->fillVariables();

        $arr = [];
        foreach($column->getRelationData('column_validation_ids') as $v)
        {
            $temp = explode(':', $v->validation_with_params)[0];
            $temp = get_attr_from_cache('validations', 'name', $temp, '*');
            if($temp != null)
            {
                Illuminate\Support\Facades\Validator::extend($temp->name, function($attribute, $value, $parameters) use($temp)
                {
                    $return = FALSE;
                    eval(helper('clear_php_code', $temp->php_code));            
                    return $return;
                });
            }
                
            if($v->validation_with_params == 'unique')
            {
                $v->validation_with_params = 'unique:'.$pipe['table'];
                
                if(\Request::segment(5) == $pipe['table'] && \Request::segment(6) == NULL)
                    $v->validation_with_params = '';
                if(\Request::segment(7) == 'update')
                {
                    $old = get_attr_from_cache($pipe['table'], 'id', $this->segment(6), $column->name);
                    $new = \Request::input($column->name);

                    if($old == $new)
                        $v->validation_with_params = '';
                }
            }
            
            array_push ($arr, $v->validation_with_params);
        }
    }
    
    $key = array_search('*auto*', $validation);
    $validation[$key] = implode('|', $arr);
}

$arr = $params[$name];
if(!is_array($arr)) $arr = [$arr];

foreach($arr as $data)
{
    $validator = Validator::make([$name => $data], [$name => implode('|', $validation) ]);
    if(!$validator->fails()) return true;

    $errors = $validator->errors()->getMessages();

    abort(helper('response_error', 'column.'.$name.'.validation.error: '.implode(', ', $errors[$name])));
}