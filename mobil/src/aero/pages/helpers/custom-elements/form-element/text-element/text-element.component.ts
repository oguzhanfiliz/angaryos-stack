import { Component, EventEmitter, Input, Output } from '@angular/core';

import { BaseHelper } from './../../../base';

@Component(
{
    selector: 'text-element',
    styleUrls: ['./text-element.component.scss'],
    templateUrl: './text-element.component.html'
})
export class TextElementComponent
{
    @Input() defaultData: string;
    @Input() value: string = "";
    @Input() type: string;
    @Input() name: string;
    @Input() class: string;
    @Input() placeholder: string;
    @Input() showFilterTypesSelect: boolean;
    @Input() filterType: string;
    @Input() createForm: boolean = false;

    @Output() changed = new EventEmitter();
    
    ngAfterViewInit() 
    {
        if(this.type == "json" || this.type == "jsonb")
        {
            this.value = BaseHelper.replaceAll(this.value, '\\"', '"');
            
            if(this.value.length > 0 && this.value.substr(0, 1) == '"')
                this.value = this.value.substr(1, this.value.length-2);
        }
    }

    handleChange(event)
    {
        this.changed.emit(event);
    }
}