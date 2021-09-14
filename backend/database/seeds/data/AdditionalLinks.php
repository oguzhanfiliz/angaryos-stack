<?php
use App\BaseModel;

$additional_links = 
[
    'clear_cache' => 
    [
        'additional_link_type_id' => $additional_link_types['standart']->id,
        'table_group_id' => $table_groups[0]->id,
        'name_basic' => 'Cache Temizle',
        'url' => '***baseUrl***/api/v1/***token***/missions/1?',
        'open_new_window' => TRUE,
        'payload' => '',
        'description' => ''
    ],
    'reload_user_auth' => 
    [
        'additional_link_type_id' => $additional_link_types['standart']->id,
        'table_group_id' => $table_groups[0]->id,
        'name_basic' => 'Yetki Yenile',
        'url' => '',
        'open_new_window' => FALSE,
        'payload' => '',
        'description' => ''
    ]
];

$additional_links['reload_user_auth']['payload'] = '
this.messageHelper.swalConfirm("Emin misiniz?", "Sunucudan güncel yetkiniz istenecek", "warning")
.then((r) =>
{
    if(r != true) return;
    
    var url = "'.env('APP_URL').'api/v1/"+BaseHelper.token+"/missions/1";
    $.ajax(
    {
        url: url,
        type: "GET",
        success: function(r)
        {
            BaseHelper.loggedInUserInfo = null;
            
            this.sessionHelper.getLoggedInUserInfo()
            .then((data) =>
            {
                this.messageHelper.sweetAlert("Yetkiniz başarı ile yenilendi", "Başarılı", "success");
            
                setTimeout(function () { window.location.reload(); }, 500);
            });
        },
        error: function(e)
        {
            console.log(e);

            var r = e.responseJSON;
            if(typeof r["status"] == "undefined") return this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            if(r["status"] != "success") return this.messageHelper.sweetAlert("Bir hata oluştu: " + r["data"]["message"], "Hata", "warning");
        }
    });
});
';

$temp = $this->get_base_record();

foreach($additional_links as $name => $data)
{
    $data = array_merge($temp, $data);
    
    $additional_links[$name] = new BaseModel('additional_links', $data);
    $additional_links[$name]->save();
}