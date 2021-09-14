import { Component } from '@angular/core';
import { BaseHelper } from './../../base';
import { MessageHelper } from './../../message';
import { SessionHelper } from './../../session';
import { GeneralHelper } from './../../general';


declare var $: any;

@Component(
{
    selector: 'mobile-app-bar-element',
    styleUrls: ['./mobile-app-bar-element.component.scss'],
    templateUrl: './mobile-app-bar-element.component.html'
})
export class MobileAppBarElementComponent
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
    
    ngAfterViewInit()
    {
        if(window.location.href.indexOf(BaseHelper.angaryosUrlPath) == -1)
        {
            $('#leftMenuToggleButton').remove();
            $('#baseBackButton').css('right', '5');
            
        }
    }
    
    toggleLeftMenu()
    {
      $('.navbar-nav').css('top', 50);

      if($('#leftsidebar').attr('class').indexOf('open') > -1)
      {
          $('#leftsidebar').removeClass('open');
          $('section').css('margin-right', '0');
          $('.navbar-nav').css('right', '-40');
      }
      else
      {
          $('#leftsidebar').addClass('open');
          $('section').css('margin-right', '40');
          $('.navbar-nav').css('right', '0');
      }
    }

    goBackPage()
    {
        this.generalHelper.goBackPage();
    }
}