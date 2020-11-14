import { Component } from '@angular/core';

import { ModalController } from '@ionic/angular';

import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';

import { RegisterPage } from './register/register.page';
import { LoginPage } from './login/login.page';

@Component({
  selector: 'app-tab2',
  templateUrl: 'tab2.page.html',
  styleUrls: ['tab2.page.scss']
})
export class Tab2Page 
{
  loggedInUserInfo = null;
  kisitliKullanici = false;

  constructor(
    private modalController: ModalController,
    private sessionHelper: SessionHelper
  ) 
  {
    this.loggedInUserInfoChanged(null, BaseHelper.getLoggedInUserInfo());
    BaseHelper.loggedUserInfoChangedDelegate.push(this); 
  }

  public loggedInUserInfoChanged(token, info)
  {
    this.loggedInUserInfo = info;
    this.variablesUpdate();
  }

  public variablesUpdate()
  {
    this.kisitliKullanici = false;
    if(typeof BaseHelper.mobilAuths['kisitliKullanici'] != "undefined") this.kisitliKullanici = true;
  }

  async register()
  {
    const modal = await this.modalController.create({component: RegisterPage});
    return await modal.present();
  }

  async login()
  {
    const modal = await this.modalController.create({component: LoginPage});
    return await modal.present();
  }

}
