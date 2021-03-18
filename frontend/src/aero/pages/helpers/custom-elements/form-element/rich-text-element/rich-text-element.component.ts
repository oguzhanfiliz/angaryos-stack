import { Component, EventEmitter, Input, Output } from '@angular/core';

import { BaseHelper } from './../../../base';

declare var $: any;

@Component(
{
    selector: 'rich-text-element',
    styleUrls: ['./rich-text-element.component.scss'],
    templateUrl: './rich-text-element.component.html'
})
export class RichTextElementComponent
{
    @Input() defaultData: string;
    @Input() upFormId: string = "";
    @Input() value: string;
    @Input() name: string;
    @Input() class: string;
    @Input() placeholder: string;
    @Input() createForm: boolean = false;
    
    ngAfterViewInit()
    {
        $.getScript('assets/ext_modules/trumbowyg/dist/trumbowyg.min.js', () => this.setRichTextElement()); 
    }
    
    setRichTextElement()
    {
        this.value = filterXSS(this.value);
        
        var selector = " [name='"+this.name+"-rich-text']";
        if(this.upFormId.length > 0) selector = '[ng-reflect-id="'+this.upFormId+'"] '+selector;
        
        $.trumbowyg.svgPath = 'assets/ext_modules/trumbowyg/dist/ui/icons.svg';
        
        $(selector).trumbowyg({lang: 'tr'});
        
        setTimeout(() =>
        {
            $(selector).html(this.value);
        }, 500);
        
        var th = this;
        $('body').on('DOMSubtreeModified', selector, function()
        {
            function func()
            {
                var html = $(selector).html();

                var elementSelector = '[name="'+th.name+'"]';
                if(th.upFormId.length > 0) elementSelector = '[ng-reflect-id="'+th.upFormId+'"] '+elementSelector;

                $(elementSelector).val(html);
            }

            return BaseHelper.doInterval('update'+selector+'RichTextElement', func, null, 500);
        });
    }
}