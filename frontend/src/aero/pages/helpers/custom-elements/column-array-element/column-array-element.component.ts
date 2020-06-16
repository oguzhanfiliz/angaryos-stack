import { Component, EventEmitter, Input, Output } from '@angular/core';
import DOMPurify from 'dompurify';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';

import { BaseHelper } from './../../base';
import { DataHelper } from './../../data';
 
declare var $: any;

@Component(
{
    selector: 'column-array-element', 
    styleUrls: ['./column-array-element.component.scss'],
    templateUrl: './column-array-element.component.html'
})
export class ColumnArrayElementComponent
{
    @Input() columnArrayJson: string;
    @Input() recordJson: string;
    @Input() tableName: string;
    @Input() defaultLimit: number;

    loadedRelationTable = [];
    columnArray = null;
    record = null;

    constructor(private sanitizer:DomSanitizer) 
    { }

    ngOnChanges()
    {
        if(typeof this.columnArrayJson != "undefined" && this.columnArrayJson != "")
            this.columnArray = BaseHelper.jsonStrToObject(this.columnArrayJson);

        if(typeof this.recordJson != "undefined" && this.recordJson != "")
            this.record = BaseHelper.jsonStrToObject(this.recordJson);
    }

    getObjectKeys(obj)
    {
        return BaseHelper.getObjectKeys(obj)
    }
    
    getColumnRelationJson(columnName)
    {
        var relation = this.getDataFromColumnArray('columns.'+columnName+'.column_table_relation_id');
        return BaseHelper.objectToJsonStr(relation);
    }
    
    getColumnType(columnName)
    {
        if(this.isFileColumn(columnName)) return 'file';
        else if(this.isJsonViewerColumn(columnName)) return 'jsonviewer';
        else if(this.isRelationColumn(columnName)) return 'relation';
        else if(this.isGeoColumn(columnName)) return 'geo';
        else if(this.isBooleanFastChangeColumn(columnName)) return 'boolean:fastchange';
        else return 'default';
    }
    
    isRelationColumn(columnName)
    {
        var relation = this.getDataFromColumnArray('columns.'+columnName+'.column_table_relation_id');
        return relation != null;
    }

    isGeoColumn(columnName)
    {
        var geoColumns = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon'];
        var type = this.getDataFromColumnArray('columns.'+columnName+".gui_type_name");
        return geoColumns.includes(type);
    }
    
    isBooleanFastChangeColumn(columnName)
    {
        var type = this.getDataFromColumnArray('columns.'+columnName+'.gui_type_name');
        return type == "boolean:fastchange";
    }

    isFileColumn(columnName)
    {
        var type = this.getDataFromColumnArray('columns.'+columnName+'.gui_type_name');
        return type == "files";
    }
    
    isImageFile(file)
    {
        if(file == null) return false;
        if(file == "") return false;

        var imgExts = ["jpg", "png", "gif"]
        var temp = file["big"].split('.');
        var ext = temp[temp.length-1];

        return imgExts.includes(ext);
    }
    
    getFileIconUrl(fileUrl)
    {
        var temp = fileUrl.split('.');
        var ext = temp[temp.length-1];

        var iconBaseUrl = "assets/img/";
        
        switch(ext)
        {
            default: return iconBaseUrl+"download_file.png";
        }
    }
    
    isJsonViewerColumn(columnName)
    {
        var type = this.getDataFromColumnArray('columns.'+columnName+'.gui_type_name').split(":")[0];
        return type == "jsonviewer";
    }
    
    getJsonStrFromObject(obj)
    {
        return BaseHelper.objectToJsonStr(obj);
    } 

    getFileUrls(data)
    {
        if(data == null) return [];

        if(typeof data == "string")
            data = BaseHelper.jsonStrToObject(data);

        var rt = [];
        for(var i = 0; i < data.length; i++)
        {
            var temp = { };
            temp['small'] = BaseHelper.getFileUrl(data[i], 's_');
            temp['big'] = BaseHelper.getFileUrl(data[i], 'b_');
            temp['org'] = BaseHelper.getFileUrl(data[i], '');

            rt.push(temp);
        }
        
        return rt;
    }

    getDataFromColumnArray(path = '')
    {
        return DataHelper.getData(this.columnArray, path);
    }

    getDataFromRecord(path = '')
    {
        return DataHelper.getData(this.record, path);
    }

    getConvertedDataForGuiByColumnName(columnName)
    {
        var type = this.getDataFromColumnArray('columns.'+columnName+".gui_type_name");
        var data = DataHelper.convertDataForGui(this.getDataFromRecord(), columnName, type);
        
        return this.sanitizer.bypassSecurityTrustHtml(data);        
    }

    columnArrayIsRelationTable(columnArray)
    {
        return (typeof columnArray['tree']) != "undefined";
    }

    getColumnNamesFromColumnArray(columnArray)
    {
        return Object.keys(columnArray.columns);
    }

    getBaseUrlByColumnArray(columnArray)
    {
        var url = "tables/"+this.tableName+"/"
        url += this.getDataFromRecord('id')+"/getRelationTableData/"
        url += this.getDataFromColumnArray('tree');
        return url;    
    }

    relationTableDataChanged(columnArray, event)
    {
        this.loadedRelationTable[columnArray.id] = true;
    }

    relationTableIsLoaded(columnArray)
    {
        return (typeof this.loadedRelationTable[columnArray.id]) != "undefined";
    }
}