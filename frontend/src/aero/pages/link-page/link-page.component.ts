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
    this.aeroThemeHelper.loadPageScripts();
    this.setDropdownOpenEffect();
  }

  setDropdownOpenEffect()
  {
    $('.dropdown').on('show.bs.dropdown', function(e)
    { 
      $(this).find('.dropdown-menu').first().stop(true, true).slideUp(300);
    });

    $('.dropdown').on('hide.bs.dropdown', function(e)
    {
      $(this).find('.dropdown-menu').first().stop(true, true).slideDown(300);
    });
  }

  getTablesGroups()
  {
    return BaseHelper.loggedInUserInfo['menu']['tableGroups'];
  }

  getTableName(tableGroupId, tableId)
  {
    var tables = BaseHelper.loggedInUserInfo['menu']['tables'][tableGroupId];

    for(var i = 0; i < tables.length; i++)
      if(tables[i]['id'] == tableId)
        return tables[i]['display_name'];
  }

  getTableUrl(tableGroupId, tableId)
  {
    var tables = BaseHelper.loggedInUserInfo['menu']['tables'][tableGroupId];

    for(var i = 0; i < tables.length; i++)
      if(tables[i]['id'] == tableId)
        return BaseHelper.baseUrl+"table/"+tables[i]['name'];
  }

  openDropDown(i)
  {
    setTimeout(() => {
      $('.dropdown [data-toggle="dropdown"]:eq('+i+')').click();
    }, 100);
  }

  getTableGroupImageUrl(tableGroup)
  {
    return BaseHelper.backendBaseUrl+tableGroup.image;
  }
}
