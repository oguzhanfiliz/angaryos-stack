<?php
use App\BaseModel;

$missions = [];

$missions['cache_clear']['name'] = 'Cache \'i Temizle';
$missions['cache_clear']['php_code'] = '<?php 
\Cache::flush(); 
$return = \'Cache Cleared\';
?>';

$missions['trigger_data_entegrate']['name'] = 'Veri aktarıcıyı tetikle';
$missions['trigger_data_entegrate']['php_code'] = '<?php
$id = (int)@$requests[\'id\'];
if($id < 1)
{
    $return = \'Geçersiz ID\';
    return;
}

\App\Jobs\DoSingleEntegrate::dispatch($id);
$return = "Görev kuyruğa eklendi";

?>';

foreach($missions as $name => $array)
{
    $temp = $this->get_base_record();
    $temp['name'] = $array['name'];
    $temp['php_code'] = $array['php_code'];
    
    $missions[$name] = new BaseModel('missions', $temp);
    $missions[$name]->save();
}