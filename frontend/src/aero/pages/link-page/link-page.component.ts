import {Component} from '@angular/core';

import { BaseHelper } from './../helpers/base';
import { SessionHelper } from './../helpers/session';
import { GeneralHelper } from './../helpers/general';
import { AeroThemeHelper } from './../helpers/aero.theme';

declare var $: any;

@Component(
{
  selector: 'link-page',
  styleUrls: ['./link-page.component.scss'],
  templateUrl: './link-page.component.html',
})
export class LinkPageComponent
{
  constructor(
    private generalHelper: GeneralHelper,
    private aeroThemeHelper: AeroThemeHelper,
    private sessionHelper: SessionHelper
    ) 
  {
    this.sessionHelper.getLoggedInUserInfo();
  }

  private keyEvent(event)
  {
    var keys = ['altKey', 'ctrlKey', 'shiftKey'];

    for(var i = 0; i < keys.length; i++)
      BaseHelper['pipe'][keys[i]] = event[keys[i]];
  }

  ngAfterViewInit() 
  {   
    this.aeroThemeHelper.addEventForFeature("layoutCommonEvents");
    this.aeroThemeHelper.addEventForFeature("standartElementEvents"); 
  }

  getTablesGroups()
  {
    return BaseHelper.loggedInUserInfo['menu']['tableGroups'];
  }
}
