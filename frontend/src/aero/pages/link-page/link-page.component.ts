import {Component} from '@angular/core';

import { BaseHelper } from './../helpers/base';
import { DataHelper } from './../helpers/data';
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
  filterString = "";
  tableGroups = [];
  additionalLinks = [];
   
  constructor(
    private generalHelper: GeneralHelper,
    private aeroThemeHelper: AeroThemeHelper,
    private sessionHelper: SessionHelper
    ) 
  {
    this.sessionHelper.getLoggedInUserInfo();
    
    this.aeroThemeHelper.pageRutine();
    this.tableGroups = this.getTablesGroups();
    
    this.fillAdditionalLinks();
  }
  
  private fillAdditionalLinks()
  {
    if(typeof BaseHelper.loggedInUserInfo['menu']['additionalLinks'] != "undefined")
        this.additionalLinks = BaseHelper.loggedInUserInfo['menu']['additionalLinks'];
        
    for(var i = 0; i < this.additionalLinks.length; i++)
        if(this.additionalLinks[i].table_group_id == null)
            this.additionalLinks[i].table_group_id = 0;
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
    
    this.aeroThemeHelper.loadPageScripts();
    this.openTableGroupList(999);
  }

  menuFilterChanged()
  {
    this.tableGroups = this.getTablesGroups();

    if(this.filterString.length == 0)
      setTimeout(() => this.openTableGroupList(999), 100);
  }

  getTablesGroups()
  {
    var rt = [];
    
    var temp = BaseHelper.loggedInUserInfo['menu']['tableGroups'];
    for(var i = 0; i < temp.length; i++)
    {
        if(this.filterString.length == 0)
          rt.push(temp[i]);
        else
        {
          var item = BaseHelper.getCloneFromObject(temp[i]);
          item['table_ids'] = [];
 
          var tableIds = temp[i]['table_ids'];
          if(tableIds != null)
            for(var j = 0; j < tableIds.length; j++)
            {
              var table = this.getTable(item['id'], tableIds[j])
              if(table == null) continue;

              if(
                table['name'].toLocaleLowerCase().indexOf(this.filterString) > -1
                ||
                table['display_name'].toLocaleLowerCase().indexOf(this.filterString) > -1
              )
                item['table_ids'].push(tableIds[j]);

            }

          if(item['table_ids'].length > 0) rt.push(item);
        }
    } 
    
    return rt;
  }

  getTable(tableGroupId, tableId)
  {
    var tables = BaseHelper.loggedInUserInfo['menu']['tables'][tableGroupId];
    if(typeof tables == "undefined") return null;
    
    for(var i = 0; i < tables.length; i++)
      if(tables[i]['id'] == tableId)
        return tables[i];
  }

  getTableName(tableGroupId, tableId)
  {
    var table = this.getTable(tableGroupId, tableId);
    if(table == null) return "";

    return table['display_name'];
  }

  getTableUrl(tableGroupId, tableId)
  {
    var tables = BaseHelper.loggedInUserInfo['menu']['tables'][tableGroupId];
    if(typeof tables == "undefined") return "";

    for(var i = 0; i < tables.length; i++)
      if(tables[i]['id'] == tableId)
        return BaseHelper.baseUrl+"table/"+tables[i]['name'];
  }

  openTableGroupList(i)
  {
    $('.table-group-list').css('display', 'none');
    $('#table-group-list-'+i).css('display', 'block');
  }

  getTableGroupImageUrl(tableGroup)
  {
    var url = BaseHelper.backendBaseUrl;

    if(tableGroup.image)
      url += 'uploads/'+tableGroup.image;
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
  
  additionalLinkClicked(additionalLink)
  {
    DataHelper.loadAdditionalLinkPayload(this, additionalLink);
    
    var url = DataHelper.getUrlFromAdditionalLink(additionalLink);
    
    if(additionalLink['open_new_window']) window.open(url);
    else window.location.href = url; 
  }
}
