import { Component, EventEmitter, Input, Output, OnInit, ViewChild, ElementRef } from '@angular/core';

declare var ace: any;
declare var $: any;

@Component(
{
    selector: 'code-editor-element',
    styleUrls: ['./code-editor-element.component.scss'],
    templateUrl: './code-editor-element.component.html'
})
export class CodeEditorElementComponent
{
    @Input() value: string;
    @Input() name: string;
    @Input() class: string;
    @Input() mode: string;
    @Input() type: string;

    @Output() changed = new EventEmitter();

    handleChange(event)
    {
        this.changed.emit(event);
    }

    codeChanged(editor)
    {
        $('#'+this.name).val(editor.getValue());
        
        const changeEvent = document.createEvent('Event');  
        changeEvent.initEvent('change', true, true);
        $('#'+this.name)[0].dispatchEvent(changeEvent);
    }

    ngOnInit () 
    {
        setTimeout(() => 
        {
            ace.config.set("workerPath", "assets/ext_modules/ace-builds/src-min/");

            var editor = ace.edit("editor"); 
            editor.setTheme("ace/theme/github");
            editor.session.setMode("ace/mode/"+this.type.split(':')[1]);
            editor.setValue(this.value);
            editor.clearSelection();

            editor.on("change", (e) => this.codeChanged(editor));
        }, 100);
     }
}