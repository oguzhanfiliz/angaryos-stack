import { ActivatedRoute} from '@angular/router';
import { Component } from '@angular/core';

import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { MessageHelper } from './../helpers/message';
import { AeroThemeHelper } from './../helpers/aero.theme';

declare var $: any;

@Component(
{
    selector: 'redirect',
    styleUrls: ['./redirect.component.scss'],
    templateUrl: './redirect.component.html'
})
export class RedirectComponent 
{    
    constructor(
        public route: ActivatedRoute,
        public sessionHelper: SessionHelper,
        public generalHelper: GeneralHelper,
        public aeroThemeHelper: AeroThemeHelper,
        public messageHelper: MessageHelper
        ) 
    {
        var th = this;
        route.params.subscribe(val => 
        {
            var url = window.location.href;
            url = url.replace(BaseHelper.baseUrl, "");
            var segments = url.split('/');
            
            th.router(segments, val);
        });
    }
    
    router(segments, val)
    {
        if(segments[3] == 'getRelationDataId') 
            this.getRelationDataId(val);
    }
    
    getRelationDataId(val)
    {
        var url = this.sessionHelper.getBackendUrlWithToken();
        if(url.length == 0) return;
        
        url += "tables/"+val["tableName"]+"/"+val["recordId"]+"/getRelationDataId/"+val["columnName"];
        console.log(url);
    }
}
