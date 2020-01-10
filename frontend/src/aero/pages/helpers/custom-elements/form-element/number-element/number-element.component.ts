import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component(
{
    selector: 'number-element',
    styleUrls: ['./number-element.component.scss'],
    templateUrl: './number-element.component.html'
})
export class NumberElementComponent
{
    @Input() value: string;
    @Input() name: string;
    @Input() class: string;
    @Input() placeholder: string;
    @Input() showFilterTypesSelect: boolean;
    @Input() filterType: string;

    @Output() changed = new EventEmitter();

    handleChange(event)
    {
        this.changed.emit(event);
    }
}