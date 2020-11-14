import { Component } from '@angular/core';
import { CallNumber } from '@ionic-native/call-number/ngx';
import { InAppBrowser } from '@ionic-native/in-app-browser/ngx';

import { GeneralHelper } from './../helpers/general';

@Component({
  selector: 'app-tab3',
  templateUrl: 'tab3.page.html',
  styleUrls: ['tab3.page.scss']
})
export class Tab3Page {

  constructor(
    private callNumber: CallNumber,
    private inAppBrowser: InAppBrowser,
    private generalHelper: GeneralHelper
  ) 
  {

  }

  call()
  {
    this.callNumber.callNumber('02742236333', true);
  }

  openInBrowser(url)
  {
    const browser = this.inAppBrowser.create(url, '_system');
  }

  openInNavigateApp()
  {
    let destination = "35.402717,40.0133833";
    let label = "Angaryos Mobil Uygulama"

    var info = this.generalHelper.getDeviceInfo();
    switch(info['clientOs'])
    {
      case 'ios': window.open('maps://?q=' + destination, '_system'); break;
      default: window.open('geo:0,0?q=' + destination + '(' + label + ')', '_system');
    }
  }

}
