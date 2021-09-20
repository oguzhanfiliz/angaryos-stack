import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgbModule, NgbDropdownConfig } from '@ng-bootstrap/ng-bootstrap';

import { PagesModule } from './pages/pages.module';

import { Injector, NgModule } from '@angular/core';
import { createCustomElement } from '@angular/elements';

import { DragDropModule } from '@angular/cdk/drag-drop';

import { FormsModule } from '@angular/forms';

import { HttpClientModule } from '@angular/common/http';

import { RouterModule } from '@angular/router';

import { AeroRoutingModule } from './aero-routing.module';
import { AeroComponent } from './aero.component';

import { MapComponent } from './pages/map/map.component';
import { PublicMapComponent } from './pages/public-map/public-map.component';

import { FullScreenMapElementComponent } from './pages/helpers/custom-elements/fullscreen-map-element/fullscreen-map-element.component';

import { LoginComponent } from './pages/login/login.component';

import { FormComponent } from './pages/form/form.component';
import { ColumnArrayFormElementComponent } from './pages/helpers/custom-elements/column-array-form-element/column-array-form-element.component';

import { MobileBottomMenuElementComponent } from './pages/helpers/custom-elements/mobile-bottom-menu-element/mobile-bottom-menu-element.component';
import { MobileAppBarElementComponent } from './pages/helpers/custom-elements/mobile-app-bar-element/mobile-app-bar-element.component';

import { GuiTriggerHelper } from './pages/helpers/gui-trigger';
import { MessageHelper } from './pages/helpers/message';
import { SessionHelper } from './pages/helpers/session';
import { GeneralHelper } from './pages/helpers/general';
import { AeroThemeHelper } from './pages/helpers/aero.theme';

//import { AngularFireMessagingModule } from '@angular/fire/messaging';
//import { AngularFireDatabaseModule } from '@angular/fire/database';
//import { AngularFireAuthModule } from '@angular/fire/auth';
//import { AngularFireModule } from '@angular/fire';
//import { MessagingService } from './pages/helpers/messaging.service';
import { environment } from '../environments/environment';
import { AsyncPipe } from '../../node_modules/@angular/common';

import { NativeAudio } from '@ionic-native/native-audio/ngx';

//import { HTTP as HttpClientNative } from '@ionic-native/http/ngx'; 

@NgModule({
  declarations: 
  [
    AeroComponent,
    LoginComponent,
    MapComponent,
    PublicMapComponent,
    FullScreenMapElementComponent,
  ],
  imports: 
  [
    AeroRoutingModule,
    BrowserModule,
    BrowserAnimationsModule,
    HttpClientModule,
    FormsModule,
    DragDropModule,
    PagesModule,
    NgbModule,
    //AngularFireDatabaseModule,
    //AngularFireAuthModule,
    //AngularFireMessagingModule,
    //AngularFireModule.initializeApp(environment.firebase),
  ],
  providers: 
  [
    GuiTriggerHelper,
    MessageHelper,
    SessionHelper,
    GeneralHelper,
    AeroThemeHelper,
    NgbDropdownConfig,
    //MessagingService,
    AsyncPipe,
    //HttpClientNative
    NativeAudio
  ],
  bootstrap: 
  [
    AeroComponent
  ],
  entryComponents: 
  [
    ColumnArrayFormElementComponent,
    FullScreenMapElementComponent,
    MobileBottomMenuElementComponent,
    MobileAppBarElementComponent
  ],
})
export class AeroModule 
{
  customElementList =
  {
    'fullscreen-map-element': FullScreenMapElementComponent
  };

  constructor(private injector: Injector) { }

  ngDoBootstrap()
  {
    var keys = Object.keys(this.customElementList);
    for(var i = 0; i < keys.length; i++)
      customElements.define(keys[i], createCustomElement(this.customElementList[keys[i]], {injector: this.injector}));
  }
}