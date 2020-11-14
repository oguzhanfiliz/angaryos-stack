import { Component } from '@angular/core';

import { Platform } from '@ionic/angular';

import { MenuController } from '@ionic/angular';

//import { HttpHeaders } from '@angular/common/http';

import { BaseHelper } from './helpers/base';
import { GeneralHelper } from './helpers/general';
import { MessageHelper } from './helpers/message';
import { SessionHelper } from './helpers/session';

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss']
})
export class AppComponent 
{
  private loggedInUserInfo = null;
  private token = "";

  constructor(
    private platform: Platform,
    private menuController: MenuController,
    private generalHelper: GeneralHelper,
    private sessionHelper: SessionHelper,
    private messageHelper: MessageHelper
  ) 
  {
    this.fillVariablesFromLocal();
    this.testInternetConnection();
    
    BaseHelper.loggedUserInfoChangedDelegate.push(this);

    console.log("test.android.stdio");
  }

  internetConnectionError()
  {
    this.messageHelper.sweetAlert("İnternet bağlantınızda bir problem olabilir!", "Hata", "warning");
  }

  testInternetConnection()
  {
    var test = this.sessionHelper.testInternetConnection();
    
    if(test == null) this.internetConnectionError();
    else
    {
      test.then((data) => { })
      .catch((err) => 
      {
        this.internetConnectionError();
      });
    }
  }

  public fillVariablesFromLocal()
  {
    this.fillTokenFromLocal();
    this.fillLoggedInUserInfoFromLocal();
  }

  public fillTokenFromLocal()
  {
    this.generalHelper.readFromLocal("token")
    .then((token) =>
    {
      if(token == null) return;
      BaseHelper.setToken(token);        
      this.token = token;
    })
  }

  public loggedInUserInfoChanged(token, info)
  {
    this.token = token;
    this.loggedInUserInfo = info;
  }

  public fillLoggedInUserInfoFromLocal()
  {
    this.generalHelper.readFromLocal("loggedInUserInfo")
    .then((info) =>
    {
      if(info == null) return;
      BaseHelper.setLoggedInUserInfo(info);
      this.loggedInUserInfo = info;
    })
  }

  public openMainMenu()
  {
    this.menuController.enable(true, 'mainMenu');
    this.menuController.open('mainMenu');
  }

  public closeMainMenu()
  {
    this.menuController.close('mainMenu');
  }

  public logout()
  {
    this.sessionHelper.logout();
    this.closeMainMenu();
  }

  public closeApp()
  {
    if(typeof this.platform['exitApp'] == "function") this.platform['exitApp']();
    this.menuController.close('mainMenu');
  } 
}
