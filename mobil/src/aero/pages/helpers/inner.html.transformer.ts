import { Directive, ElementRef, HostListener } from '@angular/core';
import { Router } from '@angular/router';

import { BaseHelper } from './base';
import { SessionHelper } from './session';
import { GeneralHelper } from './general';
import { MessageHelper } from './message';

declare var $: any;

@Directive({
  selector: '[innerHtmlTransformer]'
})
export class InnerHtmlTransformerDirective 
{
    constructor(
        private el: ElementRef, 
        private router: Router,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private messageHelper: MessageHelper
    ) { }

    @HostListener('click', ['$event'])
    public onClick(event) 
    {
        var html = event.target.innerHTML;
    }
};