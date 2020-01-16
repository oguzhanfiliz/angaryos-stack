import { ActivatedRoute} from '@angular/router';
import { Component, ElementRef } from '@angular/core';
import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { AeroThemeHelper } from './../helpers/aero.theme';
import { DataHelper } from './../helpers/data';

declare var $: any;

@Component(
{
    selector: 'show',
    styleUrls: ['./show.component.scss'],
    templateUrl: './show.component.html',
})
export class ShowComponent 
{
    public data;
    public tableName = "";
    public recordId = -1;
    public defaultLimit = 2;
    
    constructor(
        private route: ActivatedRoute,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper
        ) 
    {
        var th = this;
        route.params.subscribe(val => 
        {
            th.tableName = val.tableName;
            th.recordId = val.recordId; 

            th.addEventForFeatures();
            th.addEventForThemeIcons();
            th.dataReload();    
        });
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
