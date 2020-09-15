import { Component, EventEmitter, Input, Output } from '@angular/core';
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

    relationTableLoaded = false;
    columnArray = null;
    record = null;

    constructor(private sanitizer:DomSanitizer) 
    {
        this.fillDefaultVariables(); 
    }

    ngOnChanges()
    {
        this.fillVariables();
    }
    
    fillDefaultVariables()
    {
        this.columnArray = 
        {
            name: '',
            tree: '',
            columnNames: []
        };
    }
    
    fillVariables()
    {
        if(typeof this.recordJson != "undefined" && this.recordJson != "")
            this.record = BaseHelper.jsonStrToObject(this.recordJson);
            
        if(typeof this.columnArrayJson != "undefined" && this.columnArrayJson != "")
            this.columnArray = BaseHelper.jsonStrToObject(this.columnArrayJson);
            
        if(typeof this.columnArray['tree'] == "undefined") this.columnArray['tree'] = '';
        this.columnArray['baseUrlForRelationDataTable'] = this.getBaseUrlForRelationDataTable();
        this.columnArray['columnNames'] = Object.keys(this.columnArray['columns']);
        for(var i = 0; i < this.columnArray['columnNames'].length; i++)
        {
            var columnName = this.columnArray['columnNames'][i];
            
            var columnType = this.getColumnType(columnName);
            this.columnArray['columns'][columnName]['columnType'] = columnType;
            if(columnType == 'default')
            {
                var html = this.getConvertedDataForGuiByColumnName(columnName);
                this.columnArray['columns'][columnName]['innerHtml'] = html;
            }
            else if(columnType == 'relation')
            {
                var relation = this.columnArray['columns'][columnName]['column_table_relation_id']
                this.columnArray['columns'][columnName]['relationJson'] = BaseHelper.objectToJsonStr(relation);
            }
            else if(columnType == 'file')
            { 
                var fileUrls = this.getFileUrls(this.record[columnName]);
                for(var j = 0 ; j < fileUrls.length; j++)
                {
                    fileUrls[j]['isImage'] = this.isImageFile(fileUrls[j]);
                    fileUrls[j]['iconUrl'] = this.getFileIconUrl(fileUrls[j]['org']);
                }
                
                this.columnArray['columns'][columnName]['fileUrls'] = fileUrls;
            }
        }
    }
    
    getColumnType(columnName)
    {
        var guiType = this.columnArray['columns'][columnName]['gui_type_name'];
        var relation = this.columnArray['columns'][columnName]['column_table_relation_id'];
        
        if(guiType == "files") return 'file';
        else if(guiType.split(':')[0] == "jsonviewer") return 'jsonviewer';
        else if(guiType == 'boolean:fastchange') return 'boolean:fastchange';
        else if(relation != null) return 'relation';
        else if(this.isGeoColumn(columnName)) return 'geo';
        else return 'default';
    }
    
    getConvertedDataForGuiByColumnName(columnName)
    {
        var guiType = this.columnArray['columns'][columnName]['gui_type_name'];
        var data = DataHelper.convertDataForGui(this.record, columnName, guiType, true);
        
        //console.log(data, this.sanitizer.bypassSecurityTrustHtml(data));
        return this.sanitizer.bypassSecurityTrustHtml(data);        
    }
    
    isGeoColumn(columnName)
    {
        var geoColumns = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon'];
        var guiType = this.columnArray['columns'][columnName]['gui_type_name'];
        return geoColumns.includes(guiType);
    }
    
    isImageFile(file)
    {
        if(file == null) return false;
        if(file == "") return false;

        var imgExts = ["jpg", "png", "gif"]
        var temp = file["big"].split('.');
        var ext = temp[temp.length-1];

        return imgExts.includes(ext.toLowerCase());
    }
    
    getFileIconUrl(fileUrl)
    {
        var temp = fileUrl.split('.');
        var ext = temp[temp.length-1];

        var iconBaseUrl = "assets/img/";
        
        switch(ext.toLowerCase())
        {
            default: return iconBaseUrl+"download_file.png";
        }
    }
    
    getFileUrls(data)
    {
        if(data == null) return [];

        if(typeof data == "string")
            data = BaseHelper.jsonStrToObject(data);

        var rt = [];
        for(var i = 0; i < data.length; i++)
        {
            var temp = {};
            temp['small'] = BaseHelper.getFileUrl(data[i], 's_');
            temp['big'] = BaseHelper.getFileUrl(data[i], 'b_');
            temp['org'] = BaseHelper.getFileUrl(data[i], '');

            rt.push(temp);
        }
        
        return rt;
    }
    
    getBaseUrlForRelationDataTable()
    {
        var url = "tables/"+this.tableName+"/"
        url += this.record['id']+"/getRelationTableData/"
        url += this.columnArray['tree'];
        return url;    
    }
    
    relationTableDataChanged(event)
    {
        this.relationTableLoaded = true;
    }
}