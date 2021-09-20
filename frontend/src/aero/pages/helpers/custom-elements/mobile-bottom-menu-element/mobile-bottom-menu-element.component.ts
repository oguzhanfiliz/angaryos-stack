import { Component } from '@angular/core';
import { BaseHelper } from './../../base';
import { MessageHelper } from './../../message';
import { SessionHelper } from './../../session';
import { GeneralHelper } from './../../general';


declare var $: any;

@Component(
{
    selector: 'mobile-bottom-menu-element',
    styleUrls: ['./mobile-bottom-menu-element.component.scss'],
    templateUrl: './mobile-bottom-menu-element.component.html'
})
export class MobileBottomMenuElementComponent
{
    isNative = null;
    showShortcut = false;
  
    constructor(
        private messageHelper: MessageHelper,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
    ) 
    {    
        if(BaseHelper.isIos || BaseHelper.isAndroid) this.isNative = true; 
        if(BaseHelper.loggedInUserInfo != null) this.showShortcut = true; 
    }
    
    isActive(link)
    {
        var url = window.location.href;
        
        switch(link)
        {
            case 'mobile-home':
                return url.indexOf('mobile-home') > -1;
            case 'admin-home':
                return url.indexOf('#/'+BaseHelper.angaryosUrlPath) > -1 || url.indexOf('#/login') > -1;
            case 'shortcuts':
                return url.indexOf('shortcuts') > -1;
            case 'mobile-contact':
                return url.indexOf('mobile-contact') > -1;
            default: return false;
        }
    }
    
    navigate(page)
    {
        if(page == 'dashboard') BaseHelper.pipe["mobile-button-clicked"] = true;
        
        this.generalHelper.navigate(page);
    }
}