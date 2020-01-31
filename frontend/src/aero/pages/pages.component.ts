import { Component } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser'
import { CommonModule } from "@angular/common";

import { environment } from './../../environments/environment';

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
  //searchIntervalId = -1;

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

  getAppName()
  {
    return environment.appName.charAt(0).toUpperCase() + environment.appName.slice(1);
  }

  searchInMenuInputChanged(event) 
  {
    var params =
    {
        event: event,
        th: this
    };

    function func(params)
    {
        params.th.aeroThemeHelper.updateBaseMenu(params.event.target.value);
    }

    return BaseHelper.doInterval('searchInBaseMenu', func, params, 1000);
  }

  logout()
  {
    var token = BaseHelper.readFromLocal('realUserToken');
    if(token != null) this.sessionHelper.logoutForImitationUser();
    else this.sessionHelper.logout();
  }

  getLogoutButtonClass ()
  {
    var cls = 'mega-menu';
    var token = BaseHelper.readFromLocal('realUserToken');
    if(token != null) cls += " imitation-user-logout-button";

    return cls;
  }

  ngAfterViewInit() 
  {    
    this.aeroThemeHelper.loadPageScripts(); 
  }
} 