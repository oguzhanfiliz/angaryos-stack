import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component(
{
    selector: 'text-element',
    styleUrls: ['./text-element.component.scss'],
    templateUrl: './text-element.component.html'
})
export class TextElementComponent
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

    handleChange(event)
    {
        this.changed.emit(event);
    }
}