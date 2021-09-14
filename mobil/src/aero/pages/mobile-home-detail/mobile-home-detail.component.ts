import { ActivatedRoute} from '@angular/router';
import { Component } from '@angular/core';

import { environment } from './../../../environments/environment';

import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { MessageHelper } from './../helpers/message';
import { AeroThemeHelper } from './../helpers/aero.theme';

@Component(
{
    selector: 'mobile-home-detail',
    styleUrls: ['./mobile-home-detail.component.scss'],
    templateUrl: './mobile-home-detail.component.html',
})
export class MobileHomeDetailComponent 
{
    item = null;
    
    constructor(
        private route: ActivatedRoute,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper,
        private messageHelper: MessageHelper
        )
    {
        var th = this;
        route.params.subscribe(val => 
        {
            var id = val.id;
            if(id < 1) 
            {
                this.messageHelper.sweetAlert("Aradığınız haber bulunamadı!", "Hata", "warning");
                return;
            }
            
            th.fillNews(id);
        }); 
    }
    
    fillNews(id)
    {
        var params = {
            "page":1,
            "limit": 1,
            "column_array_id": "0",
            "column_array_id_query": "0",
            "sorts": {},
            "filters":{"id": {"type":1, "guiType": "numeric", "filter": "1", "columnName": "id"}}
        };

        var url = BaseHelper.backendUrl+"public/tables/public_contents";

        this.sessionHelper.doHttpRequest("POST", url, {'params': BaseHelper.objectToJsonStr(params)})
        .then((data) => 
        {
            if(data['records'].length == 0)
            {
                this.messageHelper.sweetAlert("Aradığınız haber bulunamadı!", "Hata", "warning");
                return;
            }
            
            this.item = this.formatNews(data['records'])[0];
        })
        .catch((e) =>
        {
            if(e == '***') return;

            console.log(e);
            this.messageHelper.sweetAlert("Sunucudan haber alınırken bir hata oluştu! Lütfen daha sonra tekrar deneyin.", "Hata", "warning");
        });
    }
    
    formatNews(news)
    {        
        var formatted = [];
        
        for(var i = 0; i < news.length; i++)
        {
            var item = news[i];
            
            item['created_at'] = BaseHelper.dBDateTimeStringToHumanDateTimeString(item['created_at']);
            item['created_at'] = item['created_at'].substr(0, 16);
            
            if(typeof item['images'] == "undefined") item['images'] = "[]";
            if(item['images'] == null) item['images'] = "[]";
            item['images'] = BaseHelper.jsonStrToObject(item['images']);
            
            for(var j = 0; j < item['images'].length; j++)
            {
                item['images'][j]['mUrl'] = BaseHelper.getFileUrl(item['images'][j], 'm_');
            }            
            if(item['images'].length == 0) item['images'] = [{"mUrl": BaseHelper.noImageUrl}];
            
            formatted.push(item);
        }
        console.log(formatted[0]);
        return formatted;
    }
}