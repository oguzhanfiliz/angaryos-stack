import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component(
{
    selector: 'password-element',
    styleUrls: ['./password-element.component.scss'],
    templateUrl: './password-element.component.html'
})
export class PasswordElementComponent
{
    @Input() name: string;
    @Input() class: string;
    @Input() placeholder: string;
}