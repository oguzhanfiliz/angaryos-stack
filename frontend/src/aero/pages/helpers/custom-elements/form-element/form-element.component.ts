import { Component, EventEmitter, Input, Output, /*ViewEncapsulation*/ } from '@angular/core';
//import { SessionHelper } from './../../session';

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
    @Input() baseUrl: string;
    @Input() name: string;
    @Input() guiType: string;
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
    
    
    @Output() changed = new EventEmitter();

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