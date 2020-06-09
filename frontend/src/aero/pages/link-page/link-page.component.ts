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
  firstPage = true;
   
  constructor(
    private generalHelper: GeneralHelper,
    private aeroThemeHelper: AeroThemeHelper,
    private sessionHelper: SessionHelper
    ) 
  {
    this.sessionHelper.getLoggedInUserInfo();
    
    this.aeroThemeHelper.pageRutine();
  }

  private keyEvent(event)
  {
    var keys = ['altKey', 'ctrlKey', 'shiftKey'];

    for(var i = 0; i < keys.length; i++)
      BaseHelper['pipe'][keys[i]] = event[keys[i]];
  }

  ngAfterViewInit() 
  {  
    if(BaseHelper.readFromPipe('loadPageScriptsLightLoaded')) this.firstPage = false;
    
    this.setDropdownOpenEffect();
    this.aeroThemeHelper.loadPageScripts();
  }

  setDropdownOpenEffect()
  {
    var th = this;
      
    $('.dropdown').on('show.bs.dropdown', function(e)
    { 
      if(th.firstPage)
        $(this).find('.dropdown-menu').first().stop(true, true).slideUp(300);
      else
        $(this).find('.dropdown-menu').first().stop(true, true).slideDown(300);
    });

    $('.dropdown').on('hide.bs.dropdown', function(e)
    {
      if(th.firstPage)
        $(this).find('.dropdown-menu').first().stop(true, true).slideDown(300);
      else
        $(this).find('.dropdown-menu').first().stop(true, true).slideUp(300);
    });
  }

  getTablesGroups()
  {
    var rt = [];
    
    var temp = BaseHelper.loggedInUserInfo['menu']['tableGroups'];
    for(var i = 0; i < temp.length; i++)
      if(temp[i]['table_ids'].length > 0)
        rt.push(temp[i]);
        
    return rt;
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
    var url = BaseHelper.backendBaseUrl;

    if(tableGroup.image)
      url += tableGroup.image;
    else 
      url += 'uploads/2020/01/01/nomenuimage.png';
      
    return url;
  }
  
  goToPage(page)
  {
      this.generalHelper.navigate(page);
  }
  
  getDashboardImageUrl()
  {
      return BaseHelper.backendBaseUrl+"uploads/2020/01/01/dashboard.png";
  }
  
  linkClicked()
  {
    BaseHelper.writeToPipe('loadPageScriptsLoaded', false);
      
    setTimeout(() => 
    {
        BaseHelper.writeToPipe('loadPageScriptsLightLoaded', false);
        this.aeroThemeHelper.loadPageScriptsLight();
    }, 500);
  }
}
