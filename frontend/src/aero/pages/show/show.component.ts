import { ActivatedRoute} from '@angular/router';
import { Component, ElementRef, EventEmitter, Input, Output } from '@angular/core';
import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { AeroThemeHelper } from './../helpers/aero.theme';
import { DataHelper } from './../helpers/data';

declare var $: any;

@Component(
{
    selector: 'in-show-element',
    styleUrls: ['./show.component.scss'],
    templateUrl: './show.component.html',
})
export class ShowComponent 
{
    @Input() id: string = "";
    @Input() inShowTableName: string = ""; 
    
    @Output() showLoad = new EventEmitter();
    
    public data;
    public tableName = "";
    public recordId = -1;
    public defaultLimit = 3;
    
    constructor(
        private route: ActivatedRoute,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper
        ) 
    {
        var th = this;
        setTimeout(() => 
        {
            route.params.subscribe(val => 
            {
                if(th.inShowTableName.length == 0)
                {
                    th.tableName = val.tableName;
                    th.recordId = val.recordId; 
                }
                else
                {
                    th.tableName = th.inShowTableName;
                    th.recordId = parseInt(th.id);
                }

                th.addEventForFeatures();
                th.addEventForThemeIcons();
                th.dataReload(); 
                
                this.aeroThemeHelper.pageRutine();   
            });
        }, 100);
    }



    /****    Data Functions     ****/

    getJson(data)
    {
        return BaseHelper.objectToJsonStr(data);
    }

    getLocalKey()
    {
        if(typeof BaseHelper.loggedInUserInfo == "undefined") return "";
        if(BaseHelper.loggedInUserInfo == null) return "";
        
        return "user:"+BaseHelper.loggedInUserInfo.user.id+".tables/"+this.tableName+".show."+this.recordId+".data";
    }

    getData(path = '')
    {
        var data = BaseHelper.readFromPipe(this.getLocalKey()); 
        if(data == null) return null;
        
        return DataHelper.getData(data, path);
    }

    getParamsForShow()
    {
        var params = 
        {
            column_set_id: BaseHelper.loggedInUserInfo.auths.tables[this.tableName]['shows'][0],
        };

        return params;
    }

    dataReload()
    {
        if(this.getData() != null) return;

        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+this.tableName+"/"+this.recordId;
        var params = this.getParamsForShow();

        this.generalHelper.startLoading();

        this.sessionHelper.doHttpRequest("GET", url, {'params': BaseHelper.objectToJsonStr(params)})
        .then((data) => 
        {
            //data.column_set.column_set_type = 'group_box';

            BaseHelper.writeToPipe(this.getLocalKey(), data);

            this.generalHelper.stopLoading();
            this.addEventForFeatures();
            
            this.showLoad.emit(data);
        })
        .catch((e) => { this.generalHelper.stopLoading(); });
    }



    /****    Events Functions    ****/

    addEventForFeatures()
    {
        this.aeroThemeHelper.addEventForFeature("standartElementEvents");
    }

    addEventForThemeIcons()
    {
        this.aeroThemeHelper.addEventForFeature("mobileMenuButton");
        this.aeroThemeHelper.addEventForFeature("rightIconToggleButton");
    }
}
