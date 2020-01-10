import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { NgModule } from '@angular/core';

import { FormsModule } from '@angular/forms';

import { HttpClientModule } from '@angular/common/http';

import { RouterModule } from '@angular/router';

import { AeroRoutingModule } from './aero-routing.module';
import { AeroComponent } from './aero.component';

import { LoginComponent } from './pages/login/login.component';

import { GuiTriggerHelper } from './pages/helpers/gui-trigger';
import { MessageHelper } from './pages/helpers/message';
import { SessionHelper } from './pages/helpers/session';
import { GeneralHelper } from './pages/helpers/general';
import { AeroThemeHelper } from './pages/helpers/aero.theme';

@NgModule({
  declarations: 
  [
    AeroComponent,
    LoginComponent
  ],
  imports: 
  [
    AeroRoutingModule,
    BrowserModule,
    BrowserAnimationsModule,
    HttpClientModule,
    FormsModule
  ],
  providers: 
  [
    GuiTriggerHelper,
    MessageHelper,
    SessionHelper,
    GeneralHelper,
    AeroThemeHelper
  ],
  bootstrap: 
  [
    AeroComponent
  ]
})
export class AeroModule {}
