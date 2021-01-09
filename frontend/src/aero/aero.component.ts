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
    if(BaseHelper.token.length == 0) this.generalHelper.navigate('/login');
  }

  firebaseCloudMessageBegin()
  {
    this.messagingService.requestPermission()
    this.messagingService.receiveMessage()    
  }
} 