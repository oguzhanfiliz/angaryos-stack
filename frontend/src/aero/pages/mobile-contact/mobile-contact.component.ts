import { ActivatedRoute} from '@angular/router';
import { Component } from '@angular/core';
import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { MessageHelper } from './../helpers/message';
import { AeroThemeHelper } from './../helpers/aero.theme'; 

@Component(
{
    selector: 'mobile-contact',
    styleUrls: ['./mobile-contact.component.scss'],
    templateUrl: './mobile-contact.component.html',
})
export class MobileContactComponent
{
    baseUrl = '';
    
    constructor(
        private route: ActivatedRoute,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper,
        private messageHelper: MessageHelper
        )
    {
        this.baseUrl = BaseHelper.backendBaseUrl; 
        
        this.aeroThemeHelper.pageRutine();
        
        var th = this;
        setTimeout(() =>
        {
            th.aeroThemeHelper.pageRutine();
        }, 500);
    }
    
    call() 
    {
        window.location.href = "tel:+905554443355";
    }
    
    mail()
    {
        window.location.href = "mailto:iletisim@omersavas.com";
    }
    
    navigate(page) 
    { 
        this.generalHelper.navigate(page);
    }
    
    mapNavigate() 
    {
        window.open("maps:?q=39.4191108,29.9621009");
    }
}