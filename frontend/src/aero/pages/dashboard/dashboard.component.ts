import {Component} from '@angular/core';

import { BaseHelper } from './../helpers/base';
import { SessionHelper } from './../helpers/session';
import { MessageHelper } from './../helpers/message';
import { GeneralHelper } from './../helpers/general';
import { AeroThemeHelper } from './../helpers/aero.theme';

declare var $: any;

@Component(
{
  selector: 'dashboard',
  styleUrls: ['./dashboard.component.scss'],
  templateUrl: './dashboard.component.html',
})
export class DashboardComponent
{
  public dashboardAuths = [];
  public dashboardDatas = [];

  constructor(
    private generalHelper: GeneralHelper,
    private sessionHelper: SessionHelper,
    private messageHelper: MessageHelper,
    private aeroThemeHelper: AeroThemeHelper
    ) 
    {
      if(typeof BaseHelper.loggedInUserInfo.auths['dashboards'] == "undefined")
        return;

      this.dashboardAuths = BaseHelper.loggedInUserInfo.auths['dashboards'];
      this.getDashboardsData();
    }

  ngAfterViewInit() 
  {   
    this.themeOperations();
  }

  themeOperations()
  {
    this.aeroThemeHelper.addEventForFeature("layoutCommonEvents");
    this.aeroThemeHelper.addEventForFeature("standartElementEvents"); 
  }

  getDashboardsData()
  {
    this.getDashboardsDataRecordCount();
  }

  isDashboardDataNull()
  {
    var keys = Object.keys(this.dashboardDatas);
    return keys.length == 0;
  }

  getSortedDashboardClass()
  {
    //getFromLocal

    return Object.keys(this.dashboardDatas);
  }

  getDashboardsByClassName(dashboardClass)
  {
    if(typeof this.dashboardDatas[dashboardClass] == "undefined")
      return [];

    return Object.keys(this.dashboardDatas[dashboardClass]);
  }

  getRecordCountPercent(data)
  {
    var p = (100 * data['count']) / data['all'];
    p = parseInt(p * 100);
    
    return p / 100;
  }

  async getDashboardsDataRecordCount()
  {
    if(typeof this.dashboardAuths['RecordCount'] == "undefined") return;
    
    var tableNames = Object.keys(this.dashboardAuths['RecordCount']);

    for(var i = 0; i < tableNames.length; i++)
    {
      var url = this.sessionHelper.getBackendUrlWithToken()+"dashboards/getData/dashboards:RecordCount:"+tableNames[i]+":0";

      var th = this;

      await this.sessionHelper.doHttpRequest("GET", url) 
      .then((data) => 
      {
        if(typeof th.dashboardDatas['RecordCount'] == "undefined")
          th.dashboardDatas['RecordCount'] = [];

        th.dashboardDatas['RecordCount'][tableNames[i]] = data;

        return true;
      })
      .catch((e) => 
      {
        this.messageHelper.toastMessage("Bazı göstergeler için data alınamadı!");
      });
    }
  }
}

