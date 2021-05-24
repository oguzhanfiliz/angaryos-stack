<?php
use App\BaseModel;


$settings = [];

$settings['DB_SCHEMA'] = 'public';
$settings['REC_COUNT_PER_PAGE'] = 10;
$settings['DB_PROJECTION'] = 7932;
$settings['UPLOAD_PATH'] = 'uploads';
$settings['STAMP_TEXT'] = 'Angaryos';
$settings['PUBLIC_USER_ID'] = 2;
$settings['ROBOT_USER_ID'] = 3;
$settings['SHOW_DELETED_TABLES_AND_COLUMNS'] = '0';

$settings['SMS_URL'] = 'https://your.sms.send.url/?someData=data&gsmno=$TEL&message=$MESSAGE';

$settings['FB_AUTH_KEY'] = 'FirebaseAuthKey';
$settings['FB_BASE_TOPIC'] = '/topics/angaryos';

$settings['DATA_ENTEGRATOR_STATES'] = '{}';

$settings['DEBUG_USER_IDS'] = '[1]';

$settings['En son duyuru'] = '2021-05-23 10:30:00';


$temp = $this->get_base_record();

foreach($settings as $name => $value)
{
    $temp['name'] = $name;
    $temp['value'] = $value;
    
    $settings[$name] = new BaseModel('settings', $temp);
    $settings[$name]->save();
}