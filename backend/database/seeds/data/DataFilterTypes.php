<?php
use App\BaseModel;


$data_filter_types = [];

$data_filter_types['list'] = 'Liste Filtresi';
$data_filter_types['update'] = 'Kayıt Düzenleme Filtresi';
$data_filter_types['delete'] = 'Kayıt Silme Filtresi';
$data_filter_types['restore'] = 'Kayıt Geri Yükleme Filtresi';
$data_filter_types['show'] = 'Kayıt Bilgi Kartı Görüntüleme Filtresi';
$data_filter_types['export'] = 'Kayıt Dışa Aktarma Filtresi';
//$data_filter_types['deleteds'] = 'Silinmiş Kayıtlardan Geri Yükleme Filtresi';
//bu bi süre dursun niye burda anlamadım

$temp = $this->get_base_record();

foreach($data_filter_types as $name => $display_name)
{
    $temp['name'] = $name;
    $temp['display_name'] = $display_name;
    
    $data_filter_types[$name] = new BaseModel('data_filter_types', $temp);
    $data_filter_types[$name]->save();
}