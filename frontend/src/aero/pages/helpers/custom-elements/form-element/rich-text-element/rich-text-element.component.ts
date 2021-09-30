import { Component, EventEmitter, Input, Output } from '@angular/core';

import { BaseHelper } from './../../../base';
import { AeroThemeHelper } from './../../../aero.theme';

declare var $: any;
declare var filterXSS: any;

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
    
    constructor( public aeroThemeHelper: AeroThemeHelper )
    { }
    
    ngAfterViewInit()
    {
        BaseHelper.getScript('assets/ext_modules/trumbowyg/dist/trumbowyg.min.js', () => this.setRichTextElement()); 
    }
    
    setRichTextElement()
    {
        this.value = filterXSS(this.value);
        
        var selector = " [name='"+this.name+"-rich-text']";
        if(this.upFormId.length > 0) 
            selector = '#'+this.upFormId+'inFormModal '+selector;
        
        $.trumbowyg.svgPath = 'assets/ext_modules/trumbowyg/dist/ui/icons.svg';
        
        $(selector).trumbowyg({lang: 'tr'});
        
        setTimeout(() =>
        {
            $(selector).html(this.value);
            this.aeroThemeHelper.pageRutine();
        }, 750);
        
        var th = this;
        $('body').on('DOMSubtreeModified', selector, function()
        {
            function func()
            {
                var html = $(selector).html();

                var elementSelector = '[name="'+th.name+'"]';
                if(th.upFormId.length > 0) 
                    elementSelector = '#'+th.upFormId+'inFormModal '+elementSelector;

                $(elementSelector).val(html);
            }

            return BaseHelper.doInterval('update'+selector+'RichTextElement', func, null, 1000);
        });
    }
}