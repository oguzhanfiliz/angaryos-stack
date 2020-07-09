import {Component} from '@angular/core';
import {CompactType, DisplayGrid, Draggable, GridsterConfig, GridsterItem, GridType, PushDirections, Resizable} from 'angular-gridster2';

import Swal from 'sweetalert2';

import { BaseHelper } from './../helpers/base';
import { SessionHelper } from './../helpers/session';
import { MessageHelper } from './../helpers/message';
import { GeneralHelper } from './../helpers/general';
import { AeroThemeHelper } from './../helpers/aero.theme';

declare var $: any;

interface Safe extends GridsterConfig 
{
  draggable: Draggable;
  resizable: Resizable;
  pushDirections: PushDirections;
}

@Component(
{
  selector: 'dashboard',
  styleUrls: ['./dashboard.component.scss'],
  templateUrl: './dashboard.component.html'
})
export class DashboardComponent implements OnInit 
{
    public dashboardAuths = [];
    public dashboardDatas = [];
  
    public options: Safe;
    public dashboards: Array<GridsterItem> = [];
    public deletedDashboards = [];
    
    
    constructor(
        private generalHelper: GeneralHelper,
        private sessionHelper: SessionHelper,
        private messageHelper: MessageHelper,
        private aeroThemeHelper: AeroThemeHelper
    ) 
    {
        this.aeroThemeHelper.pageRutine();

        if(typeof BaseHelper.loggedInUserInfo.auths['dashboards'] == "undefined") return;
        this.dashboardAuths = BaseHelper.loggedInUserInfo.auths['dashboards'];
    }
    
    ngOnInit() 
    {
        var th = this;
        
        this.options = 
        {
            gridType: GridType.Fixed,
            compactType: CompactType.None,
            mobileBreakpoint: 640,
            fixedColWidth: 130,
            fixedRowHeight: 130,
            keepFixedHeightInMobile: false,
            keepFixedWidthInMobile: false,
            draggable: 
            {
              enabled: true,
              stop: () => { th.dashboardChanged(th) }
            },
            resizable: 
            {
              enabled: true,
              stop: () => { th.dashboardChanged(th) }
            }
        };
        
        this.fillDashboardFromLocal();
        this.fillDashboardsData();
        
        this.themeOperations();
    }
    
    getLocalKey(name)
    {
        return "user:"+BaseHelper.loggedInUserInfo.user.id+".dashboards."+name;
    }
    
    getRecordCountPercent(data)
    {
      var p = (100 * data['count']) / data['all'];
      p = p * 100;
      var temp = parseInt((p).toString());

      return temp / 100;
    }
    
    getDataEntegratorStatus(message)
    {
        if(message == 'success.0.0') return 'success';
        else if(message == 'no.data') return 'no.data';
        else return 'continue';
    }

    removeItem($event: MouseEvent | TouchEvent, item): void 
    {
        $event.preventDefault();
        $event.stopPropagation();
        this.dashboards.splice(this.dashboards.indexOf(item), 1);
        
        if(!this.deletedDashboards.includes(item.dashboardId))
            this.deletedDashboards.push(item.dashboardId);
        
        this.dashboardChanged(this);
    }
    
    addItem(deletedIndex, dashboardId)
    {
        this.deletedDashboards.splice(deletedIndex, 1);
        
        var temp = dashboardId.split('.');
        this.fillDashboardsItemData(temp[0], temp[1], temp[2]);
    }
    
    dashboardChanged(th)
    {
        setTimeout(() =>
        {
            BaseHelper.writeToLocal(th.getLocalKey("dashboards"), th.dashboards);
            BaseHelper.writeToLocal(th.getLocalKey("deletedDashboards"), th.deletedDashboards);
        }, 500);
    }

    fillDashboardFromLocal()
    {
        var key = this.getLocalKey("dashboards");
        //this.dashboards = BaseHelper.readFromLocal(key);
        if(this.dashboards == null) this.dashboards = [];
        console.log(this.dashboards);
        key = this.getLocalKey("deletedDashboards");
        this.deletedDashboards = BaseHelper.readFromLocal(key);
        if(this.deletedDashboards == null) this.deletedDashboards = [];
    }

    async fillDashboardsData()
    {
        var classNames = Object.keys(this.dashboardAuths);
        for(var i = 0 ; i < classNames.length; i++)
        {
            var className = classNames[i];
            var classData = this.dashboardAuths[className];
            
            var subClassNames = Object.keys(classData);
            for(var j = 0; j < subClassNames.length; j++)
            {
                var subClassName = subClassNames[j];
                var subClassData = classData[subClassName];
                
                var itemNames = Object.keys(subClassData);
                for(var k = 0; k < itemNames.length; k++)
                {
                    var itemName = itemNames[k];
                    var itemData = subClassData[itemName];
                    
                    await this.fillDashboardsItemData(className, subClassName, itemName);
                }
            }
        }
    }
    
    async fillDashboardsItemData(className, subClassName, itemName)
    {
        var dashboardId = className+"."+subClassName+"."+itemName;
        
        switch(className)
        {
            case "RecordCount":
                await this.fillDashboardsItemDataRecordCount(dashboardId, subClassName, itemName);
                break;
            case "RefreshableNumber":
                await this.fillDashboardsItemDataRefreshableNumber(dashboardId, subClassName, itemName);
                break;
            case "DataEntegratorStatus":
                await this.fillDashboardsItemDataDataEntegratorStatus(dashboardId, subClassName, itemName);
                break;
                 
            default: 
                console.log(className+" tipi bulunamadı!");
        }
    }
    
    async fillDashboardsItemDataRecordCount(dashboardId, subClassName, itemName)
    {
        var func = (th, dashboardId, subClassName, itemName, data) => 
        {
            data['percent'] = th.getRecordCountPercent(data);
            return data;
        };
        
        var className = "RecordCount";
        await this.fillDashboardsItemDataStandartLoader(dashboardId, className, subClassName, itemName, 2, 1, func);
    }
    
    async fillDashboardsItemDataRefreshableNumber (dashboardId, subClassName, itemName)
    {
        var className = "RefreshableNumber";
        await this.fillDashboardsItemDataStandartLoader(dashboardId, className, subClassName, itemName)
    }
    
    async fillDashboardsItemDataDataEntegratorStatus(dashboardId, subClassName, itemName)
    {
        var func = (th, dashboardId, subClassName, itemName, data) => 
        {
            data['status'] = th.getDataEntegratorStatus(data['message']);
            
            if(data['status'] != "continue") return data;
            
            data['percent'] = data['detail']['percent'];
            data['type'] = data['detail']['type'];
            data['count'] = data['detail']['count'];
            data['step'] = data['detail']['step'];
            
            return data;
        };
        
        var className = "DataEntegratorStatus";
        await this.fillDashboardsItemDataStandartLoader(dashboardId, className, subClassName, itemName, 2, 1, func);
    }
    
    async fillDashboardsItemDataStandartLoader(dashboardId, className, subClassName, itemName, cols = 2, rows = 1, func = null)
    {
        if(this.deletedDashboards.includes(dashboardId)) return;
        
        var url = this.sessionHelper.getBackendUrlWithToken()+"dashboards/getData/dashboards:"+className+":"+subClassName+":"+itemName;
        var th = this;

        await this.sessionHelper.doHttpRequest("GET", url) 
        .then(async (data) => 
        {
            if(func != null) data = func(th, dashboardId, subClassName, itemName, data);
            
            var temp = 
            {
                cols: cols,
                rows: rows,
                //minItemCols: 2,
                //minItemRows: 1, 
                resizeEnabled: false,
                data: data,
                class: className,
                subClass: subClassName,
                item: itemName,
                dashboardId: dashboardId
            };

            th.dashboardDatas[className+"."+subClassName+"."+itemName] = temp;
            
            th.fillDashboardWithActualData();
            
            await BaseHelper.sleep(1000);
        })
        .catch((e) =>  th.messageHelper.toastMessage("Bazı göstergeler için data alınamadı!"));
    }
        
    fillDashboardWithActualData()
    {
        var dashboardIds = Object.keys(this.dashboardDatas);
        for(var j = 0; j < dashboardIds.length; j++)
        {
            var dashboardId = dashboardIds[j];
            var dashboardData = this.dashboardDatas[dashboardId];
            
            var control = false;
            for(var i = 0; i < this.dashboards.length; i++)
            {
                var dash = this.dashboards[i];

                if(dash['dashboardId'] == dashboardId)
                {
                    dashboardData["x"] = this.dashboards[i]["x"];
                    dashboardData["y"] = this.dashboards[i]["y"];
                    
                    this.dashboards[i] = dashboardData;
                    
                    control = true;
                    break;
                }
            }

            if(!control) this.dashboards.push(dashboardData);
        }
        
        this.dashboardChanged(this);
    }
      
    themeOperations()
    {
        this.aeroThemeHelper.addEventForFeature("layoutCommonEvents");
        this.aeroThemeHelper.addEventForFeature("standartElementEvents"); 
    }
}

