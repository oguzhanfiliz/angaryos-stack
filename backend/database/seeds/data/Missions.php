<?php
use App\BaseModel;

$missions = [];

$missions['cache_clear']['name'] = 'Cache \'i Temizle';
$missions['cache_clear']['php_code'] = '<?php 
\Cache::flush(); 
$return = \'Cache Cleared\';
?>';

foreach($missions as $name => $array)
{
    $temp = $this->get_base_record();
    $temp['name'] = $array['name'];
    $temp['php_code'] = $array['php_code'];
    
    $missions[$name] = new BaseModel('missions', $temp);
    $missions[$name]->save();
}