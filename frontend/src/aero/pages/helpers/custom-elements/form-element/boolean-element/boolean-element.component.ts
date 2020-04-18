import { Component, EventEmitter, Input, Output } from '@angular/core';
import { DataHelper } from './../../../data';

@Component(
{
    selector: 'boolean-element',
    styleUrls: ['./boolean-element.component.scss'],
    templateUrl: './boolean-element.component.html'
})
export class BooleanElementComponent
{
    @Input() default: string;
    @Input() value: string;
    @Input() guiType: string;
    @Input() class: string;
    @Input() name: string;
    @Input() placeholder: string;
    @Input() showFilterTypesSelect: boolean;
    @Input() filterType: string;

    displayNameForTrue = "true";
    displayNameForFalse = "false";

    @Output() changed = new EventEmitter();

    handleChange(event)
    {
        this.changed.emit(event);
    }

    getDisplayName(data)
    {
        if(data) return this.displayNameForTrue;
        else return this.displayNameForFalse;
    }

    constructor()
    {
        setTimeout(() => 
        {
            this.displayNameForTrue = DataHelper.convertDataByGuiTypeBoolean(this.guiType, true);
            this.displayNameForFalse = DataHelper.convertDataByGuiTypeBoolean(this.guiType, false);

            if(this.value.length > 0) return;
            if(this.default.length == 0) return;

            this.setDefaultData();
        }, 50);
    }

    setDefaultData()
    {
        if(this.default === "true")
            this.value = "true";
        else if(this.default === "false")
            this.value = "false";
    }
}