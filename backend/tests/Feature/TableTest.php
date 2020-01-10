<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TableTest extends TestCase
{
    use TestTrait;
    
    public function testGetTableDataBasic()
    {
        $paramsSets = $this->getParamsForTableDataBasic();
        
        $url = $this->getBaseUrlWithToken();
        
        foreach($paramsSets as $params)
        {
            $u = $url . 'tables/settings?params=' . json_encode($params);
        
            $response = $this->get($u);
            $this->controlTableDataBasic($response);
        }
    }
    
    private function standartTest($url, $functionName)
    {
        $url = $this->getBaseUrlWithToken() . $url;
        
        $response = $this->get($url);
        
        $controlFunctionName = str_replace('testGet', 'control', $functionName);
        $this->{$controlFunctionName}($response);
    }
    
    public function testGetTableDataFull()
    {
        $url = 'tables/departments?params={"column_array_id":1,"column_array_id_query":1,"limit":10,"sorts":{"id":true,"name":true,"manager_id":false,"state":true,"user_id":false,"updated_at":true,"mudur":true,"description":true,"db_type_id":true,"test_yeni_isim":true},"filters":{"id":{"type":3,"guiType":"numeric","filter":"5","check":true},"name":{"type":2,"guiType":"string","filter":"bilgi","check":true},"manager_id":{"type":1,"guiType":"select","filter":["1"]},"state":{"type":1,"guiType":"boolean","filter":true},"user_id":{"type":1,"guiType":"select","filter":["1"]},"updated_at":{"type":3,"guiType":"dateTime","filter":"2019-12-03 12:28:40","check":true},"mudur":{"type":1,"guiType":"string","filter":"adm"},"description":{"type":1,"guiType":"string","filter":"admin"},"test_yeni_isim":{"type":1,"guiType":"string","filter":"adm"}},"page":1}';
        $this->standartTest($url, __FUNCTION__);
    }
    
    public function testGetSelectColumnData()
    {
        $url = 'tables/departments/getSelectColumnData/user_id?search=***';
        $this->standartTest($url, __FUNCTION__);
    }
    
    public function testGetSelectColumnDataInRelationTableData()
    {
        $url = 'tables/departments/1/getRelationTableData/1:1:2/getSelectColumnData/own_id?search=***';
        $this->standartTest($url, __FUNCTION__);
    }
    
    public function testGetInfoData()
    {
        $url = 'tables/departments/1?params={"column_set_id":1}';
        $this->standartTest($url, __FUNCTION__);
    }
    
    public function testGetRelationTableData()
    {
        $url = 'tables/departments/1/getRelationTableData/1:1:2?params={"page":1,"limit":"2","column_array_id":"2","column_array_id_query":"2","sorts":{"id":true},"filters":{"name":{"type":1,"guiType":"string","filter":"adm"},"id":{"type":1,"guiType":"numeric","filter":"1"},"iliskiden_personelin_md":{"type":1,"guiType":"string","filter":"bilgi"},"own_id":{"type":1,"guiType":"select","filter":["1"]}},"columns":["id","name","own_id","surname","iliskiden_personelin_md"]}&';
        $this->standartTest($url, __FUNCTION__);
    }
}
