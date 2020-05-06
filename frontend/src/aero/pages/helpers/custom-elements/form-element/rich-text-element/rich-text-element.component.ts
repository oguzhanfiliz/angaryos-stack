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
    @Input() default: string;
    @Input() value: string;
    @Input() name: string;
    @Input() class: string;
    @Input() placeholder: string;
    
    ngAfterViewInit()
    {
        $.getScript('assets/ext_modules/trumbowyg/dist/trumbowyg.min.js', () => this.setRichTextElement()); 
    }
    
    setRichTextElement()
    {
        var id = this.name+"-rich-text";
        
        $.trumbowyg.svgPath = 'assets/ext_modules/trumbowyg/dist/ui/icons.svg';
        
        $("#"+id).trumbowyg({lang: 'tr'});
        
        setTimeout(() =>
        {
            $('#'+id).html(this.value);
        }, 500);
        
        var th = this;
        $('body').on('DOMSubtreeModified', "#"+id, function()
        {
            function func()
            {
                var html = $('#'+id).html();
                $('#'+th.name).val(html);
            }

            return BaseHelper.doInterval('update'+id+'RichTextElement', func, null, 500);
        });
    }
}