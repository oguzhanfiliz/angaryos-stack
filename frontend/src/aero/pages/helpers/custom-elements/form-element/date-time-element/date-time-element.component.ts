import { Component, EventEmitter, Input, Output } from '@angular/core';
import { AeroThemeHelper } from './../../../aero.theme';
import { BaseHelper } from './../../../base';

declare var $: any;

@Component(
{
    selector: 'date-time-element',
    styleUrls: ['./date-time-element.component.scss'],
    templateUrl: './date-time-element.component.html'
})
export class DateTimeElementComponent
{
    @Input() value: string;
    @Input() name: string;
    @Input() class: string;
    @Input() placeholder: string;
    @Input() showFilterTypesSelect: boolean;
    @Input() filterType: string;

    val = "";

    @Output() changed = new EventEmitter();

    constructor(private aeroThemeHelper: AeroThemeHelper) {}

    ngAfterViewInit()
    {
        this.addInputMask();
    }

    ngOnChanges()
    {
        this.val = BaseHelper.dBDateStringToHumanString(this.value);
    }

    addInputMask()
    {
        $('[name="'+this.name+'"]').inputmask('d/m/y h:s:s', 
        {
            hourFormat: '24',
            alias: 'datetime',
            placeholder: '__/__/____ __:__:__',
            oncomplete: (event) => this.changed.emit(event),
            oncleared: (event) => this.changed.emit(event)
        });
    }
}