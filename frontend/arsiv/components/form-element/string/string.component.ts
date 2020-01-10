import {Component, OnInit, EventEmitter, Input, Output, ViewEncapsulation} from '@angular/core';

@Component(
{
    selector: 'form-element-string',
    styleUrls: ['./string.component.scss'],
    templateUrl: './string.component.html',
    encapsulation: ViewEncapsulation.Native,
})
export class FormElementStringComponent implements OnInit
{
    @Input() value: string = "";
    @Output() changed = new EventEmitter();

    constructor( ) { }

    handleChange(event) 
    {    
        this.changed.emit(event.target.value);
    }

    ngOnInit() {}
}