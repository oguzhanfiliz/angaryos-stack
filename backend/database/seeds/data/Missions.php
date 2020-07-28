<?php
use App\BaseModel;

$missions = [];

$missions['cache_clear']['name'] = 'Cache \'i Temizle';
$missions['cache_clear']['cron'] = '';
$missions['cache_clear']['php_code'] = '<?php 
\Cache::flush(); 
$return = \'Cache Cleared\';
?>';

$missions['trigger_data_entegrate']['name'] = 'Veri aktarıcıyı tetikle';
$missions['trigger_data_entegrate']['cron'] = '';
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

$missions['backup_db']['name'] = 'Veritabanını yedekle';
$missions['backup_db']['cron'] = '0 0 * * *';
$missions['backup_db']['php_code'] = "<?php

".'$containerId'." = exec('echo www | sudo -S docker container ls | grep postgis | awk -F \' \' \'{print $1}\' ');
if(strlen(".'$containerId'.") == 0)
{
    ".'$return'." = 'Yedeklemek için postgis container bulunamadı!';
    \Log::alert(".'$return'.");
    return;
}

".'$cmd'." = 'pg_dump -Fc postgres -U postgres -h postgresql -f /var/lib/postgresql/`date +%Y-%m-%d_%H:%M`.dump';
exec('echo www | sudo -S docker exec '.".'$containerId'.".' /bin/bash -c \''.".'$cmd'.".'\'');

".'$return'." = 'OK';

?>";

foreach($missions as $name => $array)
{
    $temp = $this->get_base_record();
    $temp['name'] = $array['name'];
    $temp['php_code'] = $array['php_code'];
    $temp['cron'] = $array['cron'];
    
    $missions[$name] = new BaseModel('missions', $temp);
    $missions[$name]->save();
}