import { Component, Input } from '@angular/core';
import { ModalController } from '@ionic/angular';

import { SessionHelper } from './../../helpers/session';
import { MessageHelper } from './../../helpers/message';
import { BaseHelper } from './../../helpers/base';

@Component({
  selector: 'register',
  templateUrl: 'register.page.html',
  styleUrls: ['register.page.scss']
})
export class RegisterPage
{
  public user = 
  {
    "tc": "",
    "name_basic": "",
    "surname": "",
    "email": "", 
    "password": "",
    "column_set_id": 0,
    "id": 0
  };

  constructor( 
    private modalController: ModalController,
    private sessionHelper: SessionHelper,
    private messageHelper: MessageHelper
  ) 
  { }

  closeModal()
  {
    this.modalController.dismiss({
      'dismissed': true
    });
  }

  toggleDebugging()
  {
    BaseHelper.debug = !BaseHelper.debug;
    this.messageHelper.toastMessage("debug: " + (BaseHelper.debug ? 'true' : 'false'));
  }

  save()
  {
    var url = this.sessionHelper.backendUrl;
    url += "public/tables/register_requests/store";

    this.sessionHelper.doHttpRequest("POST", url, this.user)
    .then((data) =>
    {
      if(data["message"] == "success")
      {
        this.closeModal();
        this.messageHelper.sweetAlert("Üyelik talebiniz başarı ile alınmıştır. Onaylandığında tarafınıza bilgi verilecektir.", "Başarılı", "success");
      }
      else if(data["message"] == "error")
      {
        var keys = Object.keys(data["errors"]);
        var error = data["errors"][keys[0]][0];

        this.messageHelper.sweetAlert(error, "Hata", "warning");
      }
      else
      {
        this.messageHelper.toastMessage("Bekelnmedik bir cevap geldi!");
        console.log(data);
      }
    })
    .catch((err) =>
    {
      this.messageHelper.toastMessage("Bekelnmedik bir hata oluştu!");
      console.log(err);
    })
  }
}
