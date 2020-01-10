import {Component, OnInit, EventEmitter, Input, Output, ViewEncapsulation} from '@angular/core';

@Component(
{
    selector: 'form-element',
    styleUrls: ['./form-element.component.scss'],
    templateUrl: './form-element.component.html',
    encapsulation: ViewEncapsulation.Native
})
export class FormElementComponent implements OnInit
{
    @Input() type: string;
    @Input() value: string;
    @Output() changed = new EventEmitter();

    constructor( ) { }

    handleChange(event) 
    {    
        this.value = event.target.value;
        this.changed.emit(event.target.value);
    }

    ngOnInit() {}
}