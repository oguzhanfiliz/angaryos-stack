import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component(
{
    selector: 'string-element',
    styleUrls: ['./string-element.component.scss'],
    templateUrl: './string-element.component.html'
})
export class StringElementComponent
{
    @Input() default: string;
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