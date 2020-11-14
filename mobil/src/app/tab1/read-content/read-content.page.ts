import { Component, Input } from '@angular/core';
import { ModalController } from '@ionic/angular';

@Component({
  selector: 'read-content',
  templateUrl: 'read-content.page.html',
  styleUrls: ['read-content.page.scss']
})
export class ReadContentPage
{
  @Input() data: object = null;

  slideOpts = 
  {
    initialSlide: 1,
    speed: 400
  };
  
  constructor( 
    private modalController: ModalController
  ) 
  {
    if(this.data == null)
      this.data = 
      {
        id: 0,
        name_basic: "",
        content: "",
        summary: "",
        images: "",
        imageUrls: [],
        updated_at: "",
        user_id: ""
      };
  }

  closeModal()
  {
    this.modalController.dismiss({
      'dismissed': true
    });
  }
}
