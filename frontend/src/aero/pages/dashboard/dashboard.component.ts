import {Component} from '@angular/core';
import {CompactType, DisplayGrid, Draggable, GridsterConfig, GridsterItem, GridType, Resizable} from 'angular-gridster2';

import Swal from 'sweetalert2';

import { BaseHelper } from './../helpers/base';
import { SessionHelper } from './../helpers/session';
import { MessageHelper } from './../helpers/message';
import { GeneralHelper } from './../helpers/general';
import { AeroThemeHelper } from './../helpers/aero.theme';

declare var $: any;
declare var c3: any;

interface Safe extends GridsterConfig 
{
  draggable: Draggable;
  resizable: Resizable;
}

@Component(
{
  selector: 'dashboard',
  styleUrls: ['./dashboard.component.scss'],
  templateUrl: './dashboard.component.html'
})
export class DashboardComponent 
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
              stop: (item) => { th.dashboardChanged(th, 'drag', item) }
            },
            resizable: 
            {
              enabled: true,
              stop: (item) => { th.dashboardChanged(th, 'resize', item) }
            }
        };
        
        this.fillDashboardFromLocal();
        this.fillDashboardsData();
        
        //this.test();
        
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
    
    resetDashboards()
    {
        this.messageHelper.swalConfirm("Emin misiniz?", "Tüm göstergeleriniz varsayılan yerlerine ve ayarlarına geri yüklenecek.", 'warning')
        .then((r) =>
        {
            if(r != true) return;
            
            BaseHelper.writeToLocal(this.getLocalKey("dashboards"), []);
            BaseHelper.writeToLocal(this.getLocalKey("deletedDashboards"), []);
            location.reload();
        });
    }

    removeItem($event: MouseEvent | TouchEvent, item): void 
    {
        $event.preventDefault();
        $event.stopPropagation();
        this.dashboards.splice(this.dashboards.indexOf(item), 1);
        
        if(!this.deletedDashboards.includes(item.dashboardId))
            this.deletedDashboards.push(item.dashboardId);
        
        this.dashboardChanged(this, 'remove', item);
    }
    
    addItem(deletedIndex, dashboardId)
    {
        this.deletedDashboards.splice(deletedIndex, 1);
        
        var temp = dashboardId.split('.');
        this.fillDashboardsItemData(temp[0], temp[1], temp[2]);
    }
    
    itemResized(item)
    {
        switch(item['class'])
        {
            case "GraphicXY":
                this.GraphicXYResized(item);
                break;
            case "GraphicPie":
                this.GraphicPieResized(item);
                break;
        }
    }
    
    GraphicXYResized(item)
    {
        var elementId = "#"+item['class']+"_"+item['subClass']+"_"+item['item'];
                
        $(elementId).css('height', '100%');
        $(elementId+' svg').css('height', '100%'); 
        $(elementId).css('max-height', 'none');
        $(elementId+' svg').css('max-height', 'none');

        c3.generate(item['data']);
    }
    
    GraphicPieResized(item)
    {
        //yukardaki tek fonksiton ile birleştirilebilir aynı kodlar çalışırsa
        var elementId = "#"+item['class']+"_"+item['subClass']+"_"+item['item'];
                
        $(elementId).css('height', '100%');
        $(elementId+' svg').css('height', '100%'); 
        $(elementId).css('max-height', 'none');
        $(elementId+' svg').css('max-height', 'none');

        c3.generate(item['data']);
    }
    
    dashboardChanged(th, type, item = null)
    {
        setTimeout(() =>
        {
            BaseHelper.writeToLocal(th.getLocalKey("dashboards"), th.dashboards);
            BaseHelper.writeToLocal(th.getLocalKey("deletedDashboards"), th.deletedDashboards);
            
            if(type == "resize") th.itemResized(item);
        }, 500);
    }

    fillDashboardFromLocal()
    {
        var key = this.getLocalKey("dashboards");
        this.dashboards = BaseHelper.readFromLocal(key);
        if(this.dashboards == null) this.dashboards = [];
        
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
                    var itemKey = itemNames[k];
                    var itemData = subClassData[itemKey];
                    
                    await this.fillDashboardsItemData(className, subClassName, itemData);
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
            case "GraphicXY":
                await this.fillDashboardsItemDataGraphicXY(dashboardId, subClassName, itemName);
                break;
            case "GraphicPie":
                await this.fillDashboardsItemDataGraphicPie(dashboardId, subClassName, itemName);
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
        await this.fillDashboardsItemDataStandartLoader(dashboardId, className, subClassName, itemName, {}, func);
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
        await this.fillDashboardsItemDataStandartLoader(dashboardId, className, subClassName, itemName, {}, func);
    }
    
    async fillDashboardsItemDataGraphicXY(dashboardId, subClassName, itemName)
    {
        var func = (th, dashboardId, subClassName, itemName, data) => 
        {
            data["bindto"] = "#GraphicXY"+"_"+subClassName+"_"+itemName;
            setTimeout(() => c3.generate(data), 1000);
            return data;
        };
                
        var className = "GraphicXY";
        var options =
        {
            cols: 6,
            rows: 3,
            minItemCols: 3,
            minItemRows: 2, 
            resizeEnabled: true,
        };
        await this.fillDashboardsItemDataStandartLoader(dashboardId, className, subClassName, itemName, options, func);
    }
    
    async fillDashboardsItemDataGraphicPie(dashboardId, subClassName, itemName)
    {
        console.log(dashboardId, subClassName, itemName);

        var func = (th, dashboardId, subClassName, itemName, data) => 
        {
            data["bindto"] = "#GraphicPie"+"_"+subClassName+"_"+itemName;
            setTimeout(() => c3.generate(data), 1000);
            return data;
        };
                
        var className = "GraphicPie";
        var options =
        {
            cols: 2,
            rows: 2,
            minItemCols: 2,
            minItemRows: 2, 
            resizeEnabled: true,
        };
        await this.fillDashboardsItemDataStandartLoader(dashboardId, className, subClassName, itemName, options, func);
    }
    
    async fillDashboardsItemDataStandartLoader(dashboardId, className, subClassName, itemName, options = {}, func = null)
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
                cols: 2,
                rows: 1,
                minItemCols: 2,
                minItemRows: 1, 
                resizeEnabled: false,
                data: data,
                class: className,
                subClass: subClassName,
                item: itemName,
                dashboardId: dashboardId
            };
            
            var keys = Object.keys(temp);
            for(var i = 0; i < keys.length; i++)
                if(typeof options[keys[i]] != "undefined") 
                    temp[keys[i]] = options[keys[i]];

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
                    dashboardData["cols"] = this.dashboards[i]["cols"];
                    dashboardData["rows"] = this.dashboards[i]["rows"];
                    
                    this.dashboards[i] = dashboardData;
                    
                    control = true;
                    break;
                }
            }

            if(!control) this.dashboards.push(dashboardData);
        }
        
        this.dashboardChanged(this, 'dataReload');
    }
      
    themeOperations()
    {
        this.aeroThemeHelper.addEventForFeature("layoutCommonEvents");
        this.aeroThemeHelper.addEventForFeature("standartElementEvents"); 
    }
}