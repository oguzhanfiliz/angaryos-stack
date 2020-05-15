<?php
use App\BaseModel;
//MimeTypeExtensionGuesser 

$validations = [];

$validations['numeric_min']['display_name'] = 'En az (nümerik)';
$validations['files_type']['display_name'] = 'Yalnızca resim';
$validations['files_count']['display_name'] = 'Dosya sayısı';
$validations['no_self']['display_name'] = 'Yetki grubuna kendini ekleyemesin';
$validations['name_not_start_deleted']['display_name'] = 'İsim "deleted_" ile başlayamasın';
$validations['name_not_be_deleted_records_name']['display_name'] = 'İsim silinmiş bit tablonun adı olamaz';
$validations['no_change']['display_name'] = 'Data değiştirilemez';
$validations['valid_validations']['display_name'] = 'Olmayan doğrulama kuralı yazılamaz!';
$validations['column_table_relation_control']['display_name'] = 'İlişkili kolon ilişki kontrolü';
$validations['cron']['display_name'] = 'Crontab sözdizimi kontrolü';
$validations['select_updated_at']['display_name'] = 'Veri aktarma ilişkisi için zorunlu kolon kontrolü';
$validations['only_fromdatasource_for_excel_type']['display_name'] = 'Excel veri kaynağı için sadece fromDataSource yönü seçilsin kontrolü';
$validations['required_for']['display_name'] = 'Boş geçilemez (şartlı)';



$validations['numeric_min']['php_code'] = '<?php
$return = (int)$value >= (int)$parameters[0];
?>';

$validations['files_type']['php_code'] = '<?php

if(is_array($value))
    foreach($value as $file)
    {
        $control = FALSE;
        if(is_array($parameters))
            foreach($parameters as $param)
            {
                if(strstr($file->getMimeType(), $param))
                {
                    $control = TRUE;
                    break;
                }
            }
        
        if($control == FALSE)
        {
            $return = FALSE;
            return;
        }
    }

$return = TRUE;
?>';

$validations['files_count']['php_code'] = '<?php
if(!is_array($value))
{
    $return = TRUE;
    return;
}
    
if($parameters[0] == \'<\')
    $return = (count($value) < $parameters[1]);
else if($parameters[0] == \'>\')
    $return = (count($value) > $parameters[1]);
else
    $return = (count($value) == $parameters[0]);
?>';

$validations['no_self']['php_code'] = '<?php
if(\Request::segment(7) != \'update\' || \Request::segment(5) != \'auth_groups\')
{
    $return = TRUE;
    return;
}

$id = (int)\Request::segment(6);
$value = json_decode($value);

$return = !in_array($id, $value);
?>';


$validations['name_not_start_deleted']['php_code'] = '<?php
global $pipe;
if($pipe["table"] != "tables" && $pipe["table"] != "columns")
{
    $return = TRUE;
    return;
}

$return = (substr($value, 0, 8) != "deleted_");
?>';

$validations['name_not_be_deleted_records_name']['php_code'] = '<?php
global $pipe;
if($pipe["table"] != "tables" && $pipe["table"] != "columns")
{
    $return = TRUE;
    return;
}

$temp = \DB::table($pipe["table"])->where("name", "deleted_".$value)->get();

$return = (count($temp) == 0);
?>';

$validations['no_change']['php_code'] = '<?php 
if(\Request::segment(7) != \'update\') 
{
    $return = TRUE;
    return;
}

$id = (int)\Request::segment(6);

global $pipe;

$temp = get_attr_from_cache($pipe[\'table\'], \'id\', $id, \'data_filter_type_id\');
$return = ($temp == $value);
?>';

$validations['valid_validations']['php_code'] = '<?php 
$temp = explode(\':\', $value)[0];
$temp = get_attr_from_cache(\'validations\', \'name\', $temp, \'id\');
$return = ($temp != NULL);
?>';

$validations['column_table_relation_control']['php_code'] = '<?php
$relationGuiTypeIds = [];
array_push($relationGuiTypeIds, get_attr_from_cache(\'column_gui_types\', \'name\', \'select\', \'id\'));
array_push($relationGuiTypeIds, get_attr_from_cache(\'column_gui_types\', \'name\', \'multiselect\', \'id\'));
array_push($relationGuiTypeIds, get_attr_from_cache(\'column_gui_types\', \'name\', \'multiselectdragdrop\', \'id\'));

$guiTypeId = \Request::input(\'column_gui_type_id\');
if(!in_array($guiTypeId, $relationGuiTypeIds)) 
{
    $return = TRUE;
    return;
}

$return  = (strlen($value) > 0);

?>';

$validations['cron']['php_code'] = '<?php
$result = preg_match( "/^((?:[1-9]?\d|\*)\s*(?:(?:[\/-][1-9]?\d)|(?:,[1-9]?\d)+)?\s*){5}$/", $value, $matches); 
$return  = (count($matches) == 2);
?>';

$validations['select_updated_at']['php_code'] = '<?php
$return = FALSE;

if($value == \'[]\') return;

$columnNames = [];
$value = json_decode($value); 
foreach($value as $columnRelationId)
{
    $columnId = get_attr_from_cache(\'data_source_col_relations\', \'id\', $columnRelationId, \'column_id\');
    $columnName = get_attr_from_cache(\'columns\', \'id\', $columnId, \'name\');
	array_push($columnNames, $columnName);
}

if(!in_array(\'updated_at\', $columnNames)) return;

$singleColumn = \Request::input(\'single_column\');
if(strlen($singleColumn) > 0)
{
    $recId = (int)\Request::segment(6);
    if($recId < 1) return;
    
    $dataSourceId = get_attr_from_cache(\'data_source_tbl_relations\', \'id\', $recId, \'data_source_id\');
}
else
{
    $dataSourceId = (int)\Request::input(\'data_source_id\');
    if($dataSourceId < 1) return;
}

$dataSourceTypeId = get_attr_from_cache(\'data_sources\', \'id\', $dataSourceId, \'data_source_type_id\');
$dataSourceTypeName = get_attr_from_cache(\'data_source_types\', \'id\', $dataSourceTypeId, \'name\');

if($dataSourceTypeName == \'postgresql\')
{
	$return = TRUE;
	return;
}      

if(!in_array(\'id\', $columnNames)) return;

$return = TRUE;
        
?>';

$validations['only_fromdatasource_for_excel_type']['php_code'] = '<?php
$return = TRUE;

$dataSourceId = (int)\Request::input(\'data_source_id\');
if($dataSourceId < 1) return;

$dataSourceTypeId = get_attr_from_cache(\'data_sources\', \'id\', $dataSourceId, \'data_source_type_id\');
$dataSourceTypeName = get_attr_from_cache(\'data_source_types\', \'id\', $dataSourceTypeId, \'name\');

if($dataSourceTypeName == \'excel\')
{
	$dataSourceDirectionName = get_attr_from_cache(\'data_source_directions\', \'id\', $value, \'name\');
	if($dataSourceDirectionName != \'fromDataSource\')
	{
		$return = FALSE;
		return;
	}
}
?>';

$validations['required_for']['php_code'] = '<?php
$return = TRUE;
?>';


$validations['numeric_min']['error_message'] = 'Değer en az :parameters[0] olmalıdır';
$validations['files_type']['error_message'] = 'Dosya tipi yalnızca :parameters[0] olabilir.';
$validations['files_count']['error_message'] = 'Dosya sayısı yalnızca :parameters[0] olabilir.';
$validations['no_self']['error_message'] = 'Yetkiye kendisini ekleyemezsiniz!';
$validations['name_not_start_deleted']['error_message'] = 'İsim "deleted_" ile başlayamaz';
$validations['name_not_be_deleted_records_name']['error_message'] = 'İsim silinmiş kayıtlardan birine ait. Bunu kullanamazsınız!';
$validations['no_change']['error_message'] = 'Bu veri değiştirilemez.';
$validations['valid_validations']['error_message'] = 'Böyle bir doğrulama kuralı yok!';
$validations['column_table_relation_control']['error_message'] = 'İlişkili kolon için bir data ilişkisi seçmelisiniz!';
$validations['cron']['error_message'] = 'Geçerisiz bir zamanlayıcı girdiniz! (cron syntax)';
$validations['select_updated_at']['error_message'] = 'Güncellenme zamanı ve id kolonu seçilmelidir!';
$validations['only_fromdatasource_for_excel_type']['error_message'] = 'Excel tipi için yalnızca fromDataSource seçilebilir';
$validations['required_for']['error_message'] = '//Mesaj otomatik gelecek';
        

$temp = $this->get_base_record();

foreach($validations as $name => $array)
{
    $temp['name'] = $name;
    $temp['display_name'] = $array['display_name'];
    $temp['php_code'] = $array['php_code'];
    $temp['error_message'] = $array['error_message'];
    
    $validations[$name] = new BaseModel('validations', $temp);
    $validations[$name]->save();
}




$column_validations = [];
$column_validations['required'] = NULL;
$column_validations['integer'] = NULL;
$column_validations['numeric'] = NULL;
$column_validations['unique'] = NULL;
$column_validations['date_format:"Y-m-d H:i:s"'] = NULL;
$column_validations['date_format:"Y-m-d"'] = NULL;
$column_validations['date_format:"H:i:s"'] = NULL;
$column_validations['boolean'] = NULL;
$column_validations['boolean_custom'] = NULL;
$column_validations['email'] = NULL;
$column_validations['numeric_min:1'] = NULL;
$column_validations['min:5'] = NULL;
$column_validations['files_type:image'] = NULL;
$column_validations['files_count:1'] = NULL;
$column_validations['no_self'] = NULL;
$column_validations['active_url'] = NULL;
$column_validations['unique:users,email'] = NULL;
$column_validations['url'] = NULL;
$column_validations['ip'] = NULL;
$column_validations['json'] = NULL;
$column_validations['nullable'] = NULL;
$column_validations['no_self'] = NULL;
$column_validations['name_not_start_deleted'] = NULL;
$column_validations['name_not_be_deleted_records_name'] = NULL;
$column_validations['no_change'] = NULL;
$column_validations['valid_validations'] = NULL;
$column_validations['column_table_relation_control'] = NULL;
$column_validations['cron'] = NULL;
$column_validations['select_updated_at'] = NULL;
$column_validations['only_fromdatasource_for_excel_type'] = NULL;
$column_validations['required_for:table1,table2'] = NULL;

$temp = $this->get_base_record();

foreach($column_validations as $validation => $null)
{
    $temp['validation_with_params'] = $validation;
    
    $column_validations[$validation] = new BaseModel('column_validations', $temp);
    $column_validations[$validation]->save();
}