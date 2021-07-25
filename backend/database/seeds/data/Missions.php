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

$missions['announcement_control']['name'] = 'Duyuru zamanı kontrol';
$missions['announcement_control']['cron'] = '* * * * *';
$missions['announcement_control']['php_code'] = '<?php

$lib = new \App\Libraries\AnnouncementLibrary();
$lib->Control();

?>';

$missions['reset_password_request']['name'] = 'Şifre sıfırlama maili at';
$missions['reset_password_request']['cron'] = '';
$missions['reset_password_request']['php_code'] = '<?php

$u = read_from_response_data(\'user\');
$user = get_attr_from_cache(\'users\', \'email\', $u, \'*\');
if(!$user) $user = get_attr_from_cache(\'users\', \'tc\', $u, \'*\');
if(!$user)
{
    $return = \'OK\';
    return;
}

$token = Str::random(32).date("Y-m-d_H:i:s");
\DB::table(\'users\')->where(\'id\', $user->id)->update([\'remember_token\' => $token]);

$emailsWithNames =
[
    [
        \'email\' => $user->email,
        \'name\' => $user->name_basic.\' \'.$user->surname
    ]
];

$baseUrl = env(\'APP_URL\');
$html = \'Tarafımıza, sizin için bir şifre sıfırlama isteği ulaştı. Eğer talebi siz yapmadıysanız bu maili görmezden gelin. \';
$html .= \'Eğer şifre sıfırlama talebini siz yaptıysanız linki tıklayarak yeni şifrenizi alabilirsiniz. \';
$html .= \'<a href="\'.$baseUrl.\'api/v1/public/missions/6?remember_token=\'.$token.\'"> Şifremi Sıfırla </a>\';
$html .= \'<br><br>Eğer linki tıklayamıyorsanız bu bağlantıyı kopyalayabilirsiniz: \'.$baseUrl.\'api/v1/public/missions/6?remember_token=\'.$token;

$temp = \App\Libraries\MessageLibrary::sendMail(
    \'Şifre sıfırlama talebi\', 
    $html, 
    $emailsWithNames);

if(count($temp) == 0) $return = \'OK\';
else $return = \'FAIL\';

?>';

$missions['reset_password']['name'] = 'Şifre sıfırlama maili at';
$missions['reset_password']['cron'] = '';
$missions['reset_password']['php_code'] = '<?php

$token = read_from_response_data(\'remember_token\');

$temp = str_replace(\'_\', \' \', substr($token, 32));
$temp = new \Carbon\Carbon($temp);
$now = new \Carbon\Carbon();
$diff = $now->diffInMinutes($temp);

if($diff > 60) dd("Malesef şifre sıfırlama talebinizin süresi geçmiş! Tekrar Şifremi Unuttum butonuna tıklayın.");

$temp = \DB::table(\'users\')->where(\'remember_token\', $token)->get();
if(count($temp) == 0)
{
    \Log::alert(\'Geçersiz şifre sıfırlama linki: \'.$token);
    dd("Malesef bu link geçerli değil");
}

$user = $temp[0];

$password = Str::random(8) . \'.\' . rand(10, 99);

\DB::table(\'users\')->where(\'id\', $user->id)->update([
    \'password\' => \Hash::make($password),
    \'remember_token\' => \'\'
]);

$emailsWithNames =
[
    [
        \'email\' => $user->email,
        \'name\' => $user->name_basic.\' \'.$user->surname
    ]
];

$baseUrl = env(\'APP_URL\');
$html = \'Şifreniz talebiniz üzerine sıfırlanmıştır: \'.$password;

$temp = \App\Libraries\MessageLibrary::sendMail(
    \'Şifre sıfırlama tamamlandı\', 
    $html, 
    $emailsWithNames);

if(count($temp) == 0) dd("Şifre mail adresinize gönderildi");
else $return = dd("Şifre sıfırlanamadı!");

?>';

foreach($missions as $name => $array)
{
    $temp = $this->get_base_record();
    $temp['name'] = $array['name'];
    $temp['php_code'] = $array['php_code'];
    $temp['cron'] = $array['cron'];
    
    $missions[$name] = new BaseModel('missions', $temp);
    $missions[$name]->save();
}