import { ActivatedRoute} from '@angular/router';
import { Component } from '@angular/core';

import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { DataHelper } from './../helpers/data';
import { GeneralHelper } from './../helpers/general';
import { MessageHelper } from './../helpers/message';
import { AeroThemeHelper } from './../helpers/aero.theme';

declare var $: any;

@Component(
{
    selector: 'search',
    styleUrls: ['./search.component.scss'],
    templateUrl: './search.component.html'
})
export class SearchComponent 
{
    public words = "";
    public page = 1;
    public info = null;
    public results = {};
    public summary = {};
    
    constructor(
        public route: ActivatedRoute,
        public sessionHelper: SessionHelper,
        public generalHelper: GeneralHelper,
        public aeroThemeHelper: AeroThemeHelper,
        public messageHelper: MessageHelper
        ) 
    {
        var th = this;
        route.params.subscribe(val => 
        {
            th.words = val.words;

            th.searchOnAllTables(th.words);

            this.aeroThemeHelper.addEventForFeature("standartElementEvents");
            
            this.aeroThemeHelper.pageRutine();
        });

        setTimeout(() => 
        {
            this.aeroThemeHelper.addEventForFeature("layoutCommonEvents");
        }, 100);
    }

    dataControlForThisPage(tableName)
    {
        if(this.page > 1 && typeof this.results[tableName] == "undefined")
            return false;
        
        if(typeof this.results[tableName] != "undefined")
            if(this.page > this.results[tableName][1]['pages'])
                return false;

        return true;
    }

    async searchOnAllTables(words)
    {
        await this.sessionHelper.getLoggedInUserInfo()
        .then(async (info) =>
        {
            if(typeof info["auths"] == "undefined") return;
            if(typeof info["auths"]["tables"] == "undefined") return;

            this.info = info;

            var tables = info["auths"]["tables"];
            var tableNames = Object.keys(tables);
            for(var i = 0; i < tableNames.length; i++)
            {
                this.generalHelper.startLoading();

                try 
                {
                    if(!this.dataControlForThisPage(tableNames[i])) 
                    {
                        this.generalHelper.stopLoading();
                        continue;
                    }

                    await this.searchOnTable(tableNames[i], tables[tableNames[i]]);                    
                    this.generalHelper.stopLoading();                    
                    await BaseHelper.sleep(1000);    
                }
                catch (error) 
                {
                    this.generalHelper.stopLoading();
                    this.messageHelper.toastMessage("Bazı hatalar oluştu. Tüm sonuçlar görüntülenmiyor olabilir"); 
                    console.log(error);
                }
            }
        });
    } 

    async searchOnTable(tableName, table)
    {
        var url = this.sessionHelper.getBackendUrlWithToken()+"search/"+tableName+"/"+encodeURI(this.words);
        
        var th = this;

        await this.sessionHelper.doHttpRequest("POST", url, 
        {
            'column_array_id': table['lists'][0],
            'page': th.page
        })
        .then((data) => 
        {
            if(data['records'].length == 0) return;

            if(typeof th.results[tableName] == "undefined")  th.results[tableName] = [];
            th.results[tableName][th.page] = data;
        })
    }

    search()
    {
        var words = $('#searchInput').val();
        
        if(words == null || words.length == 0)
        {
            this.messageHelper.toastMessage("Aramak için birşeyler yazmalısınız!");
            return;
        }

        window.location.href = BaseHelper.baseUrl+"search/"+words;
        window.location.reload();
    }

    getTableNames()
    {
        if(this.results == null) return [];

        return Object.keys(this.results);
    }

    getRecords(tableName)
    {
        if(typeof this.results[tableName][this.page] == "undefined") return [];

        return this.results[tableName][this.page]['records'];
    }

    showInfoPage(tableName, record)
    {
        this.generalHelper.navigate("table/"+tableName+"/"+record['id']);
    }

    getInfoPageURL(tableName, record)
    {
        return BaseHelper.baseUrl+"table/"+tableName+"/"+record['id'];
    }

    getTableDisplayName(tableName)
    {
        if(typeof this.results[tableName] == "undefined")
            return tableName;

        return this.results[tableName][1]['table_info']['display_name'];
    }

    getRecordSummary(tableName, recordIndex)
    {
        var data = this.results[tableName][this.page];
        var record = data['records'][recordIndex];

        var key = tableName+"_"+record['id'];
        if(typeof this.summary[key] != "undefined") return this.summary[key];

        var exc = ["point", "multipoint", "linestring", "multilinestring", "polygon", "multipolygon", "files"];

        var summary = "";
        var columnNames = Object.keys(data['columns']);
        for(var i = 0; i < columnNames.length; i++)
        {
            var type = data['columns'][columnNames[i]]['gui_type_name'];
            if(exc.includes(type)) continue;

            var displayName = data['columns'][columnNames[i]]['display_name'];

            summary += displayName + ": ";
            summary += this.convertDataForGui(record, columnNames[i], type);
            summary += ",&nbsp&nbsp&nbsp";
        }
        
        this.summary[key] = summary;
        return this.summary[key];
    }

    convertDataForGui(record, columnName, type)
    {
        var data = DataHelper.convertDataForGui(record, columnName, type);
        return data;
    }

    getMaxPage()
    {
        if(this.results == null) return 0;

        var max = 0;

        var tableNames = Object.keys(this.results);
        for(var i = 0; i < tableNames.length; i++)
            if(max < this.results[tableNames[i]][1]['pages'])
                max = this.results[tableNames[i]][1]['pages'];

        return max;
    }

    getPageRange()
    {
        var r = [];
        for(var i = 1; i <= this.getMaxPage(); i++) r.push(i);
        return r;
    }

    setPage(page)
    {
        this.page = page;
        this.searchOnAllTables(this.words);
    }
}
