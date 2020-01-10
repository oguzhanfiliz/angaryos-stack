import { Component } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser'
import { CommonModule } from "@angular/common";

import { BaseHelper } from './helpers/base';
import { SessionHelper } from './helpers/session';
import { GeneralHelper } from './helpers/general';
import { AeroThemeHelper } from './helpers/aero.theme';

declare var $: any;

@Component({
  selector: 'pages',
  templateUrl: 'pages.component.html'
})
export class PagesComponent 
{
  user = {};
  searchIntervalId = -1;

  constructor(
        public sessionHelper: SessionHelper,
        public generalHelper: GeneralHelper,
        public aeroThemeHelper: AeroThemeHelper
        )
  { 
    this.fillVariablesFromLoggedInUserInfo();

    $('body').keydown((event) => this.keyEvent(event));
    $('body').keyup((event) => this.keyEvent(event));
  }

  private keyEvent(event)
  {
    var keys = ['altKey', 'ctrlKey', 'shiftKey'];

    for(var i = 0; i < keys.length; i++)
      BaseHelper['pipe'][keys[i]] = event[keys[i]];
  }

  private fillVariablesFromLoggedInUserInfo()
  {
    this.sessionHelper.getLoggedInUserInfo()
    .then((data) =>
    {
      this.user = data.user;
      this.aeroThemeHelper.updateBaseMenu();
    })
  }

  yetkiTemizle()
  {
    localStorage.removeItem('loggedInUserInfo');
  }

  searchInMenuInputChanged(event) 
  {
    if(this.searchIntervalId > -1) clearInterval(this.searchIntervalId);
    this.searchIntervalId = setInterval(() => 
    {
      clearInterval(this.searchIntervalId);
      this.aeroThemeHelper.updateBaseMenu(event.target.value);
    }, 1000);
  }

  public logout()
  {
    this.sessionHelper.logout()
  }

  ngAfterViewInit() 
  {    
    this.aeroThemeHelper.loadPageScripts(); 
  }
} 