import { Component, EventEmitter, Input, Output } from '@angular/core';
import { BaseHelper } from './../../base';

@Component(
{
    selector: 'form-element',
    styleUrls: ['./form-element.component.scss'],
    templateUrl: './form-element.component.html',
    //encapsulation: ViewEncapsulation.Native
})
export class FormElementComponent
{
    @Input() type: string;
    @Input() recordJson: string; 
    @Input() baseUrl: string;
    @Input() name: string;
    @Input() guiType: string;
    @Input() defaultData: string;
    @Input() value: string;
    @Input() valueJson: string;
    @Input() class: string;
    @Input() columnName: string;
    @Input() placeholder: string;
    @Input() showFilterTypesSelect: boolean;
    @Input() filterType: string;
    @Input() upColumnName: number;
    @Input() upFormId: string;    
    @Input() srid: string = "";
    @Input() showClearDataButton: boolean = false;
    @Input() createForm: boolean = false;
    
    record = null;
    
    
    @Output() changed = new EventEmitter();
    
    ngOnChanges()
    {
        if(typeof this.recordJson != "undefined" && this.recordJson != "")
            this.record = BaseHelper.jsonStrToObject(this.recordJson);
    }

    handleChanged(event)
    {
        this.changed.emit(event);
    }

    isGeoType(type)
    {
        var geoColumns = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon'];
        return geoColumns.includes(type);
    }

    isGeoMultiple(type)
    {
        return type.indexOf('multi') > -1;
    }
}