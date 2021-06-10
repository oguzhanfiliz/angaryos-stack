import { Component } from '@angular/core';

import { BaseHelper } from './pages/helpers/base';
import { GeneralHelper } from './pages/helpers/general';
import { MessagingService } from './pages/helpers/messaging.service';

@Component({
  selector: 'aero-root',
  template: '<router-outlet></router-outlet>'
})
export class AeroComponent 
{
  constructor(
      private generalHelper: GeneralHelper,
      private messagingService: MessagingService
  )
  {
    BaseHelper.preLoad();
    this.firebaseCloudMessageBegin();
    this.redirectToLoginIfLoggedOut()
  }

  firebaseCloudMessageBegin()
  {
    this.messagingService.requestPermission()
    this.messagingService.receiveMessage()    
  }
  
  redirectToLoginIfLoggedOut()
  {
    if(BaseHelper.token.length > 0) return;
      
    var ext = ['lppd', 'privacy-politica'];
    for(var i = 0; i < ext.length; i++)
    {
        var control = window.location.href.indexOf(ext[i]);
        if(control > -1) return;
    }
        
    this.generalHelper.navigate('/login');
  }
} 