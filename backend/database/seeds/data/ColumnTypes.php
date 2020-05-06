<?php
use App\BaseModel;


$column_db_types = [];

$column_db_types['string'] = 'Kısa Yazı';
$column_db_types['text'] = 'Uzun Yazı';
$column_db_types['jsonb'] = 'JSON*';
$column_db_types['json'] = 'JSON';
$column_db_types['integer'] = 'Tam Sayı';
$column_db_types['float'] = 'Virgüllü Sayı';
$column_db_types['boolean'] = 'Mantıksal';
$column_db_types['date'] = 'Tarih';
$column_db_types['time'] = 'Saat';
$column_db_types['datetime'] = 'Tarih/Saat';
$column_db_types['point'] = 'Coğrafi Nokta';
$column_db_types['multipoint'] = 'Coğrafi Nokta (Çoklu)';
$column_db_types['linestring'] = 'Coğrafi Çizgi';
$column_db_types['multilinestring'] = 'Coğrafi Çizgi (Çoklu)';
$column_db_types['polygon'] = 'Coğrafi Alan';
$column_db_types['multipolygon'] = 'Coğrafi Alan (Çoklu)';

$temp = $this->get_base_record();

foreach($column_db_types as $schema_code => $display_name)
{
    $temp['name'] = $schema_code;
    $temp['display_name'] = $display_name;
    $temp['schema_code'] = $schema_code;
    
    $column_db_types[$schema_code] = new BaseModel('column_db_types', $temp);
    $column_db_types[$schema_code]->save();
}




$column_gui_types = [];

$column_gui_types['string'] = 'Kısa Yazı';
$column_gui_types['text'] = 'Uzun Yazı';
$column_gui_types['numeric'] = 'Sayı';
//$column_gui_types['email'] = 'E-Mail';
$column_gui_types['date'] = 'Tarih';
$column_gui_types['time'] = 'Saat';
//$column_gui_types['json'] = 'JSON';
$column_gui_types['jsonb'] = 'JSON*';
$column_gui_types['datetime'] = 'Tarih/Saat';
$column_gui_types['select'] = 'Açılır Liste';
$column_gui_types['multiselect'] = 'Açılır Liste (Çoklu)';
//$column_gui_types['radio'] = 'Çoktan Seçmeli';
$column_gui_types['boolean'] = 'Mantıksal';
$column_gui_types['password'] = 'Şifre';
//$column_gui_types['tel'] = 'Telefon';
$column_gui_types['files'] = 'Dosya/Resim';
//$column_gui_types['wkt'] = 'WKT';
$column_gui_types['point'] = 'Coğrafi Nokta';
$column_gui_types['linestring'] = 'Coğrafi Çizgi';
$column_gui_types['polygon'] = 'Coğrafi Alan';
$column_gui_types['multipoint'] = 'Coğrafi Nokta (Çoklu)';
$column_gui_types['multilinestring'] = 'Coğrafi Çizgi (Çoklu)';
$column_gui_types['multipolygon'] = 'Coğrafi Alan (Çoklu)';
//$column_gui_types['color'] = 'Renk';
//$column_gui_types['gender'] = 'Cinsiyet';
//$column_gui_types['tl'] = 'TL';
$column_gui_types['codeeditor:php'] = 'Kod Editörü (PHP)';
$column_gui_types['codeeditor:sql'] = 'Kod Editörü (SQL)';
$column_gui_types['codeeditor:html'] = 'Kod Editörü (HTML)';
$column_gui_types['multiselectdragdrop'] = 'Liste (Taşı Bırak)';
$column_gui_types['phone'] = 'Telefon';
$column_gui_types['money:tl'] = 'Para Birimi (TL)';
$column_gui_types['money:usd'] = 'Para Birimi (USD)';
$column_gui_types['rich_text'] = 'Zengin Metin Editörü';

$temp = $this->get_base_record();

foreach($column_gui_types as $name => $display_name)
{
    $temp['name'] = $name;
    $temp['display_name'] = $display_name;
    
    $column_gui_types[$name] = new BaseModel('column_gui_types', $temp);
    $column_gui_types[$name]->save();
}

