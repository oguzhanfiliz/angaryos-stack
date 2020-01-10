import { Component } from '@angular/core';

import { BaseHelper } from './pages/helpers/base';
import { GeneralHelper } from './pages/helpers/general';

@Component({
  selector: 'aero-root',
  template: '<router-outlet></router-outlet>'
})
export class AeroComponent 
{
  constructor(
      private generalHelper: GeneralHelper
  )
  {
    BaseHelper.preLoad();
    if(BaseHelper.token.length == 0) this.generalHelper.navigate('/login');
  }
} 