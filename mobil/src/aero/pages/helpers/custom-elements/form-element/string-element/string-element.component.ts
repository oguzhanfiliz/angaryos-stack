import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component(
{
    selector: 'string-element',
    styleUrls: ['./string-element.component.scss'],
    templateUrl: './string-element.component.html'
})
export class StringElementComponent
{
    @Input() defaultData: string;
    @Input() value: string;
    @Input() name: string;
    @Input() class: string;
    @Input() placeholder: string;
    @Input() showFilterTypesSelect: boolean;
    @Input() filterType: string;
    @Input() createForm: boolean = false;

    @Output() changed = new EventEmitter();
    
    ngOnChanges()
    {
        if(this.createForm && this.defaultData.length > 0) this.value = this.defaultData;
    }

    handleChange(event)
    {
        this.changed.emit(event);
    }
    
    handleKeyDown(event)
    {
        switch(event.keyCode)
        {
            case 13://enter
                event['enterKey'] = true;
                this.changed.emit(event);
                break;
        }
    }
}