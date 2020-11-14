import { Component } from '@angular/core';

import { MessageHelper } from './../helpers/message';
import { GeneralHelper } from './../helpers/general';
import { SessionHelper } from './../helpers/session';
//import { BaseHelper } from './../helpers/base';

import { ReadContentPage } from './read-content/read-content.page';
import { ModalController } from '@ionic/angular';

@Component({
  selector: 'app-tab1',
  templateUrl: 'tab1.page.html',
  styleUrls: ['tab1.page.scss']
})
export class Tab1Page 
{
  public publicContents = [];

  constructor(
    private generalHelper: GeneralHelper,
    private messageHelper: MessageHelper,
    private sessionHelper: SessionHelper,
    private modalController: ModalController
  ) 
  {
    this.openInfoPageWithControl();
  }

  openInfoPageWithControl()
  {
    this.generalHelper.readFromLocal("lastSeenInfoPage") 
    .then((val) => 
    {
      if(val < 1) this.openInfoPage();
      else this.fillPublicContents();
    });
  }

  openInfoPage()
  {
    this.messageHelper.swalConfirm(
      "Hoşgeldiniz", 
      "Bu uygulama Angaryos framework 'ün mobil uygulamasıdır. Çeşitli haberler ve şahsınıza ait bildirimleri alabilirsiniz", 
      "info", 
      'Tekrar Gösterme', 
      'Kapat')
    .then(data =>
    {
      this.fillPublicContents();

      if(!data) return;

      this.generalHelper.writeToLocal("lastSeenInfoPage", 1);
    });
  }

  fillPublicContents()
  {
    this.generalHelper.readFromLocal("publicContents")
    .then((val) => 
    {
      if(val) 
      {
        this.publicContents = val;
        return;
      }
      
      this.sessionHelper.getPublicContents()
      .then((publicContents) =>
      {
        for(var i = 0; i < publicContents.length; i++)
        {
          publicContents[i]['imageUrls'] = [];
          var images = this.generalHelper.jsonStrToObject(publicContents[i]['images']);
          for(var j = 0; j < images.length; j++)
          {
            publicContents[i]['imageUrls'].push(this.generalHelper.getFileUrl(images[j], "m_"));
          }

          if(publicContents[i]['imageUrls'].length == 0)
            publicContents[i]['imageUrls'][0] = this.generalHelper.getFileUrl(null);
        }

        this.generalHelper.writeToLocal("publicContents", publicContents, 1000 * 60 * 6);
        this.publicContents = publicContents;
      });
    });
  }

  async readContent(content)
  {
    const modal = await this.modalController.create({
      component: ReadContentPage,
      componentProps: 
      {
        data: content
      }
    });
    return await modal.present();
  }
}
