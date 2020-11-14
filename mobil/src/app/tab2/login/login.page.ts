import { Component, Input } from '@angular/core';
import { ModalController } from '@ionic/angular';

import { MessageHelper } from './../../helpers/message';
import { SessionHelper } from './../../helpers/session';
import { GeneralHelper } from './../../helpers/general';
import { BaseHelper } from './../../helpers/base';

@Component({
  selector: 'login',
  templateUrl: 'login.page.html',
  styleUrls: ['login.page.scss']
})
export class LoginPage
{
  public user = 
  {
    "email": "iletisim@omersavas.com", 
    "password": "1234Aa."
  };

  public loading = false;

  constructor( 
    private modalController: ModalController,
    private messageHelper: MessageHelper,
    private sessionHelper: SessionHelper,
    private generalHelper: GeneralHelper
  ) 
  { }

  validate()
  {
    if(this.user.password.length < 4)
    {
        this.messageHelper.toastMessage("Şifre en az 4 karakter olmalı");
        return false;
    }
    else if(this.user.email.length < 4)
    {
        this.messageHelper.toastMessage("Kullanıcı bilgisi en az 4 karakter olmalı");
        return false;
    }
    
    return true;
  }

  virtualDeviceControl()
  {
    var info = this.generalHelper.getDeviceInfo();
  
    if(typeof info.isVirtual == "undefined" || info.isVirtual == null || info.isVirtual == true)
		{
			this.messageHelper.sweetAlert("Bu uygulama yalnızca gerçek cihazlarda kullanılabilir!", 'Hata', 'danger');
			//return false;
		}

    return true;
  }

  doLogin() 
  {
    if(!this.virtualDeviceControl()) return;

    if(BaseHelper.debug) alert("LoginPage.1");
    if(this.loading) return;

    
    if(BaseHelper.debug) alert("LoginPage.2");
    if(!this.validate()) return;

    
    if(BaseHelper.debug) alert("LoginPage.3");
    this.loading = true;

    if(BaseHelper.debug) alert("LoginPage.4");
    this.sessionHelper.login(this.user.email, this.user.password)
    .then((data) => 
    {
      if(BaseHelper.debug) alert("LoginPage.5A");
      this.sessionHelper.setToken(data["token"]);

      if(BaseHelper.debug) alert("LoginPage.5B");
      this.sessionHelper.fillLoggedInUserInfo()
      .then((data) =>
      {
        if(BaseHelper.debug) alert("LoginPage.6A");
        this.loading = false;

        if(BaseHelper.debug) alert("LoginPage.6B");
        this.closeModal();

        if(BaseHelper.debug) alert("LoginPage.6C");
      })
      .catch((e) =>
      {
        if(BaseHelper.debug) alert("LoginPage.7A");
        this.loading = false;
        if(BaseHelper.debug) alert("LoginPage.7B");
      });
    })
    .catch((errorMessage) =>  
    {
      if(BaseHelper.debug) alert("LoginPage.8A");
      this.loading = false;

      if(BaseHelper.debug) alert("LoginPage.8B");
      if(errorMessage.indexOf("***") > -1) return;

      if(BaseHelper.debug) alert("LoginPage.8C");
      this.messageHelper.toastMessage("Doğrulama Hatası: "+errorMessage);
    });
  }

  closeModal()
  {
    this.modalController.dismiss({
      'dismissed': true
    });
  }
}
