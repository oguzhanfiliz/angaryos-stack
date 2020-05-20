<?php

namespace App\Http\Controllers;

use App\Libraries\ColumnClassificationLibrary;
use App\BaseModel;
use Event;
use DB;

trait GeneralControllerTrait
{
    private function UserImportRecordAuthControl($user)
    {
        if(!isset($user->auths['admin']['recordImport']))
            custom_abort("no.import.record.auth");
    }
    
    private function MoveUploadedFileToTempFolder($files)
    {
        $tempFolder = 'temps/';
        
        $paths = [];
        
        foreach($files as $file)
        {
            $orgPath = $file->getRealPath();
            
            $fileName = $file->getClientOriginalName();
            $path = $tempFolder.$fileName;

            $file->move($tempFolder, $fileName);

            chmod($path, 0777);
            
            copy($path, $orgPath);
            
            array_push($paths, $path);
        }

        return $paths;
    }
    
    
    
    
    private function ImportRecordsToTables($user, $paths)
    {
        global $pipe;
        $pipe['importRecordErrors'] = [];
        
        $return = [ 'data' => [], 'error' => [] ];
        
        DB::beginTransaction();
        
        foreach($paths as $path)
        {
            $json = file_get_contents($path);
            $data = json_decode($json, TRUE);
            
            $tableName = $data['tableName'];
            $data = $this->ImportRecordIfNotExistAndGetData($data, TRUE);
            $return['data'][$path] =
            [
                'tableName' => $tableName,
                'record' => $data
            ];   
        }
        
        DB::commit();
        
        $return['error'] = $pipe['importRecordErrors'];
        return $return;
    }
    
    private function IsRecordExist($data)
    {
        $recordData = $this->GetRecordForImportData($data);
        if($recordData == NULL) return FALSE;
            
        return $recordData;
    }
    
    private function ImportRecordIfNotExistAndGetData($data, $force = FALSE)
    {
        $control = $this->ValidateTableForImportRecord($data['tableName'], $data['columns']);        
        if(!$control) return FALSE;
        
        if(!$force)
        {
            $record = $this->IsRecordExist($data);
            if($record != FALSE) return $record;
        }
        
        $recordData = $this->GetRecordDataForRecordImportFromJsonDataObject($data);
        $recordData = $this->ReplaceRecordDataForRecordImport($recordData);
         
        
        $record = $this->IsRecordExist($data);
        if($record != FALSE) return $record;
        
        
        
        $newRecord = $this->CreateRecordForImportData($data['tableName'], $recordData);
        if($newRecord == FALSE) return FALSE;
        
        
        
        if(isset($data['relationColumnName'])) 
            $recordData = $this->GetRecordForImportData($data);
        else
            $recordData = $newRecord->toArray();
        
        return $recordData;
    }
    
    private function ValidateTableForImportRecord($tableName, $columns)
    {
        global $pipe;
        
        $this->CreateTableIfNotExistForImportRecord($tableName, $columns);
        $control = $this->ControlTableColumnsForImportRecord($tableName, $columns);
        
        if($control != [])
        {
            dd('ValidateTableForImportRecord');
            //tablo var ama bazı kolonlar yok hatası yaz
            //write error pipe  $tableName   $control
            array_push($pipe['importRecordErrors'], '');
            return FALSE;
        }
        
        return TRUE;
    }
    
    private function CreateTableIfNotExistForImportRecord($tableName, $columns)
    {
        $id = get_attr_from_cache('tables', 'name', $tableName, 'id');
        if($id != NULL) return;
        
        if(substr($tableName, 0, 19) == '[columnDataSources:')
        {
            $name = explode(': ', $tableName)[1];
            $name = substr($name, 0, -1);
            $dataSource = get_attr_from_cache('column_data_sources', 'name', $name, '*');
            if($dataSource != NULL) return;
        }
        
        dd('CreateTableIfNotExistForImportRecord', $tableName);
    }
    
    private function ControlTableColumnsForImportRecord($tableName, $columns)
    {
        if(substr($tableName, 0, 19) == '[columnDataSources:') return [];
        
        $tableModel = get_model_from_cache('tables', 'name', $tableName);
        $tableColumns = $tableModel->getRelationData('column_ids');
        
        foreach($tableColumns as $tableColumn)
            if(isset($columns[$tableColumn->name]))
                unset($columns[$tableColumn->name]);
            
        return $columns;
    }
    
    private function GetRecordForImportData($data)
    {
        if(isset($data['relation']))
            return $this->GetRecordForImportDataRelationRecord($data);
        else
            return $this->GetRecordForImportDataNonRelationRecord($data);
    }
    
    private function GetRecordForImportDataRelationRecord($data)
    {
        $column = get_model_from_cache('columns', 'name', $data['relationColumnName']);
        
        $columnData = $this->GetSelectColumnDataForGetRecordForImportDataRelationRecord($column, $data);
        
        $result = $this->GetSelectColumnResultForGetRecordForImportDataRelationRecord($column, $data, $columnData);
        
        return $this->GetSourceDataFromSelectColumnResultForGetRecordForImportDataRelationRecord($data, $result, $column, $columnData);
    }
    
    private function GetSelectColumnDataForGetRecordForImportDataRelationRecord($column, $data)
    {
        $params = helper('get_null_object');
        $params->data = $data;
        
        //display ile tespit yapılabiliyorsa getir yoksa source getir.
        $columnData = ColumnClassificationLibrary::relation(
                                                        $this, 
                                                        'GetColumnDataForDetectRelationRecord', 
                                                        $column, 
                                                        NULL, 
                                                        $params);
        
        return $columnData;
    }
    
    public function GetColumnDataForDetectRelationRecordForTableIdAndColumnIds($params)
    {
        $temp = get_attr_from_cache('columns', 'id', $params->relation->relation_display_column_id, 'name');
        return 
        [
            'type' => 'display',
            'resultName' => 'text',
            'columnName' => $temp
        ];
    }
    
    public function GetColumnDataForDetectRelationRecordForDataSource($params)
    {
        $data = $params->data['data'];
        $displayColumn = $data['_display_column_name'];
        
        return 
        [
            'type' => 'display',
            'resultName' => 'text',
            'columnName' => $displayColumn
        ];
    }
    
    private function GetSelectColumnResultForGetRecordForImportDataRelationRecord($column, $data, $columnData)
    {
        $table = new BaseModel($data['upTableName']);
        
        $params = helper('get_null_object');
        $params->page = 1;
        $params->search = $data['data'][$columnData['columnName']];
        $params->limit = 500;
        $params->table = $table;
        $params->upColumnName = '';
        $params->upColumnData = '';
        
        return $column->getSelectColumnData($params);
    }
    
    private function GetSourceDataFromSelectColumnResultForGetRecordForImportDataRelationRecord($data, $result, $column, $columnData)
    {
        global $pipe;
        if(count($result['results']) == 0) 
            return NULL;
        else if(count($result['results']) == 1) 
            $return = $result['results'][0]['id'];//id is source data everytime
        else 
        {
            foreach($result['results'] as $res)
                if($res[$columnData['resultName']] == $data['data'][$columnData['columnName']])
                {
                    //id is source data everytime
                    $return = $res['id'];
                    break;
                }
        }
        
        return $return;
    }
    
    private function GetRecordForImportDataNonRelationRecord($data)
    {
        $except = ['id', 'state'];
        
        $model = DB::table($data['tableName']);
        foreach($data['data'] as $columnName => $columnData)
        {
            if(is_array($columnData)) 
            {
                if(count($columnData) == 0) continue;
                if(strlen(@$columnData['tableName']) > 0) continue;                
                if(strlen(@$columnData[0]['tableName']) > 0) continue;
                
                if($columnName == 'tokens') continue;
                
                dd('GetRecordForImportDataNonRelationRecord', $columnName, $columnData);//continue;//dd($columnName, $columnData);
            }
            
            if(strlen($columnData) == 0) continue;
            if(in_array($columnName, $except)) continue;

            $model = $model->where($columnName, $columnData);
        }

        return $model->first();
    }
    
    private function GetRecordDataForRecordImportFromJsonDataObject($data)
    {
        $recordData = [];
        foreach($data['data'] as $columnName => $columnData)
        {
            if(is_array($columnData))
                if(isset($columnData['columns']) || isset($columnData[0]['columns']))
                {
                    if(isset($columnData['columns']))
                    {
                        $temp = $this->GetRelationDataFromSubTable($data, $columnName, $columnData);
                        if($temp == FALSE) return FALSE;
                        
                        $columnData = $temp;
                    }
                    else
                    {
                        foreach($columnData as $i => $columnDataItem)
                        {
                            $temp = $this->GetRelationDataFromSubTable($data, $columnName, $columnData[$i]);
                            if($temp == FALSE) return FALSE;
                            
                            $columnData[$i] = $temp;
                        }
                    }
                }
            
            $recordData[$columnName] = $columnData;
        }
        
        return $recordData;
    }
    
    private function GetRelationDataFromSubTable($data, $columnName, $columnData)
    {
        $columnData['relationColumnName'] = $columnName;
        $columnData['upTableName'] = $data['tableName'];
        

        //bu ikisinin sırasına tam karar verekedim. tablo yoksa ilişi atamaz iliş
        $columnTableRelationData = $this->ImportRecordIfNotExistAndGetData($columnData['relation']); //column table relation record
        if($columnTableRelationData == FALSE) return FALSE;

        global $pipe;
        if(isset($pipe['omer'])) $pipe['savas'] = TRUE;
        
        $subTableData = $this->ImportRecordIfNotExistAndGetData($columnData);//relation table record
        
        return $subTableData;
    }
    
    private function ReplaceRecordDataForRecordImport($data)
    {
        foreach($data as $columnName => $columnData)
        {
            $typeId = get_attr_from_cache('columns', 'name', $columnName, 'column_db_type_id');
            $typeName = get_attr_from_cache('column_db_types', 'id', $typeId, 'name');
            
            switch($typeName)
            {
                case 'jsonb':
                    if($columnData != NULL) $columnData = json_encode($columnData);
                    else if($columnData == []) $columnData = NULL;
                    break;
            }
            
            $data[$columnName] = $columnData;
        }
        
        return $data;
    }
    
    private function CreateRecordForImportData($tableName, $data)
    {
        global $pipe;
        $pipe['table'] = $tableName;
        $pipe['subscriberTypeOverride'] = 'import';
        
        $data['column_set_id'] = 0;
        $control = $this->ValidateRecordForCreate($tableName, $data);
        if(!$control) return FALSE;
        
        $controller = new \App\Http\Controllers\Api\V1\TableController();
        $params = $controller->getValidatedParamsForStore($data, TRUE);
        $record = Event::dispatch('record.store.requested', $params)[1];
        Event::dispatch('record.store.success', [$params, $record]);
        
        return $record;
    }
    
    private function ValidateRecordForCreate($tableName, $data)
    {
        global $pipe;
        
        unset($_FILES['files']);
        \Request::merge($data);
        
        $request = app('App\Http\Requests\BaseRequest');
        $errors = $request->validator->errors()->getMessages();
        
        if(count($errors) > 0) 
        {
            if(!isset($pipe['importRecordErrors'][$tableName]))
                $pipe['importRecordErrors'][$tableName] = [];
            
            array_push($errors, $data);
            array_push($pipe['importRecordErrors'][$tableName], $errors);
            
            if(isset($pipe['omer']) && isset($pipe['savas']) && isset($pipe['mehmet'])) dd($errors);
            return FALSE;
        }
        
        return TRUE;
    }
}
