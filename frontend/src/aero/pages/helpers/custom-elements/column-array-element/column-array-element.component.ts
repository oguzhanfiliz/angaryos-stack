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

     isGeoColumn(columnName)
    {
        var geoColumns = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon'];
        var type = this.getDataFromColumnArray('columns.'+columnName+".gui_type_name");
        return geoColumns.includes(type);
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