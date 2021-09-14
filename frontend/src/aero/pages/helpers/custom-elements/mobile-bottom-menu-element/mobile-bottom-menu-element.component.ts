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
  
    constructor(
        private messageHelper: MessageHelper,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
    ) 
    {    
        if(BaseHelper.isIos || BaseHelper.isAndroid) this.isNative = true;  
    }
    
    isActive(link)
    {
        switch(link)
        {
            case 'mobile-home':
                return window.location.href.indexOf('mobile-home') > -1;
            case 'admin-home':
                return window.location.href.indexOf('#/'+BaseHelper.angaryosUrlPath) > -1 || window.location.href.indexOf('#/login') > -1;
            case 'shortcuts':
                return false;
            case 'mobile-contact':
                return window.location.href.indexOf('mobile-contact') > -1;
            default: return false;
        }
    }
    
    navigate(page)
    {
        this.generalHelper.navigate(page);
    }
    
    openShortcutsModal()
    {
        this.messageHelper.sweetAlert("Malesef bu özellik şuan aktif değil!", "Kısayollarım", "info");
    }
}