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
    
    public data = null;
    public tableName = "";
    public recordId = -1;
    public defaultLimit = 3;
    public editable = false;
    
    constructor(
        private route: ActivatedRoute,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper
        ) 
    {
        this.fillDefaultVariables();
        
        var th = this;
        setTimeout(() => route.params.subscribe(val => th.preLoadInterval(val)), 100);
    }

    ngOnChanges()
    {
        this.preLoadInterval(); 
    }

    preLoadInterval(val = null)
    {
        var params =
        {
            val: val,
            th: this
        };

        function func(params)
        {
            params.th.preLoad(params.val);
        }

        return BaseHelper.doInterval('formPreLoad', func, params, 200);
    }
    
    preLoad(val)
    {
        this.fillDefaultVariables();
        
        this.fillTableNameAndRecordId(val);
        
        this.addEventForFeatures();
        this.addEventForThemeIcons();
        
        this.dataReload(); 

        this.aeroThemeHelper.pageRutine();   
    }
    
    fillDefaultVariables()
    {
        this.data = {};
        
        this.data['title'] = '';
        
        this.data['column_set'] = [];
        this.data['column_set']['column_set_type'] = 'none';
        this.data['column_set']['column_arrays'] = [];
    }
    
    fillEditable()
    {
        if(typeof BaseHelper.loggedInUserInfo['auths'] == "undefined") return;
        if(typeof BaseHelper.loggedInUserInfo['auths']['tables']== "undefined") return;
        if(typeof BaseHelper.loggedInUserInfo['auths']['tables'][this.tableName] == "undefined") return;
        if(typeof BaseHelper.loggedInUserInfo['auths']['tables'][this.tableName]['edits'] == "undefined") return;
        this.editable = true;
    }
    
    fillTableNameAndRecordId(val)
    {
        if(this.inShowTableName == "")
        {
            this.tableName = val.tableName;
            this.recordId = val.recordId; 
        }
        else
        {
            this.tableName = this.inShowTableName;
            this.recordId = parseInt(this.id);
        }
    }
    
    getParamsForShow()
    {
        var auths = BaseHelper.loggedInUserInfo['auths'];
        
        var params = 
        {
            column_set_id: auths['tables'][this.tableName]['shows'][0],
        };

        return params;
    }

    dataReload()
    {
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+this.tableName+"/"+this.recordId;
        var params = this.getParamsForShow();
        var data = {'params': BaseHelper.objectToJsonStr(params)};
        
        this.sessionHelper.doHttpRequest("GET", url, data)
        .then((data) => this.dataLoaded(data));
    }
    
    dataLoaded(data)
    {
        this.data = this.fillDataAdditionalVariables(data);
        
        this.fillEditable();
        
        this.addEventForFeatures();
        this.showLoad.emit(data);
    }
    
    fillDataAdditionalVariables(data)
    {
        data['title'] = DataHelper.getTitleOrDefault(data['column_set']['name'], 'Bilgi Kartı');
        
        data['record']['json'] = BaseHelper.objectToJsonStr(data['record']);
            
        for(var i = 0; i < data['column_set']['column_arrays'].length; i++)
        {
            var json = BaseHelper.objectToJsonStr(data['column_set']['column_arrays'][i]);
            data['column_set']['column_arrays'][i]['json'] = json;
            
            var title = DataHelper.getTitleOrDefault(data['column_set']['column_arrays'][i]['name_basic'], '');
            data['column_set']['column_arrays'][i]['title'] = title;
        }  
        
        return data;
    }
    
    edit()
    {
        this.generalHelper.navigate("table/"+this.tableName+"/"+this.recordId+"/edit");
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
