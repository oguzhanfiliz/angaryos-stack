import { Directive, ElementRef, HostListener } from '@angular/core';
import { Router } from '@angular/router';

import { BaseHelper } from './base';
import { SessionHelper } from './session';
import { GeneralHelper } from './general';

declare var $: any;

@Directive({
  selector: '[innerHtmlTransformer]'
})
export class InnerHtmlTransformerDirective 
{

    constructor(
        private el: ElementRef, 
        private router: Router,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper
    ) { }

    @HostListener('click', ['$event'])
    public onClick(event) 
    {
        var html = event.target.innerHTML;
        
        if(html.indexOf('type="relationDataInfo"') > -1)
            this.openRelationDataInfoPage(html, event);
    }
    
    openRelationDataInfoPage(html, event)
    {
        var url = html.split('info-url="')[1].split('"')[0];
        
        this.generalHelper.startLoading();
        
        this.sessionHelper.doHttpRequest("GET", url) 
        .then((data) => 
        {
            this.generalHelper.stopLoading();
            
            if(!this.authControlForRelationDataInfoPage(data)) 
            {
                this.messageHelper.toastMessage("Bilgi kartı için yetkiniz yok");  
                return;
            }
            
            this.generalHelper.navigate('table/'+data['tableName']+"/"+data['recordId'], event.ctrlKey);
        })
        .catch((e) => { this.generalHelper.stopLoading(); });
    }
    
    authControlForRelationDataInfoPage(data)
    {
        var auth = BaseHelper.loggedInUserInfo['auths']['tables'];
        if(typeof auth[data['tableName']] == "undefined") return false;
        if(typeof auth[data['tableName']]['shows'] == "undefined") return false;
        
        return true;
    }

};