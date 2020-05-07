import { Component, EventEmitter, Input, Output } from '@angular/core';
import { AeroThemeHelper } from './../../../aero.theme';
import { BaseHelper } from './../../../base';

declare var $: any;

@Component(
{
    selector: 'phone-element',
    styleUrls: ['./phone-element.component.scss'],
    templateUrl: './phone-element.component.html'
})
export class PhoneElementComponent
{
    @Input() defaultData: string;
    @Input() value: string;
    @Input() name: string;
    @Input() class: string;
    @Input() placeholder: string;
    @Input() showFilterTypesSelect: boolean;
    @Input() filterType: string;
    @Input() createForm: boolean = false;

    val = "";

    @Output() changed = new EventEmitter();

    constructor(private aeroThemeHelper: AeroThemeHelper) {}

    ngAfterViewInit()
    {
        this.elementOperations();
    }

    elementOperations()
    {      
        $.getScript('assets/themes/aero/assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js', 
            () => this.addInputMask());        
    }

    ngOnChanges()
    {
        this.val = this.value;
    }

    addInputMask()
    {
        $('[name="'+this.name+'"]').inputmask("0 999 999 99 99", 
        {
            placeholder: '0 ___ ___ __ __',
            oncomplete: (event) => this.changed.emit(event),
            onincomplete: (event) => this.changed.emit(event),
            oncleared: (event) => this.changed.emit(event)
        });
    }
}