import { Component, EventEmitter, Input, Output } from '@angular/core';
import { BaseHelper } from './../../../base';
import { MessageHelper } from './../../../message';

declare var jsonTree: any;
declare var $: any;

@Component(
{
    selector: 'jsonviewer-element',
    styleUrls: ['./jsonviewer-element.component.scss'],
    templateUrl: './jsonviewer-element.component.html'
})
export class JsonViewerElementComponent
{
    @Input() defaultData: string;
    @Input() value: string = "";
    @Input() name: string;
    @Input() type: string = "";
    @Input() readOnly: boolean = false;
    @Input() newPage: boolean = false;
    @Input() class: string;
    @Input() placeholder: string;
    @Input() recordJson: string;
    @Input() createForm: boolean = false;

    @Output() changed = new EventEmitter();
    
    record = null;
    
    constructor(
        private messageHelper: MessageHelper
    ) { }

    ngAfterViewInit()
    { 
        if(this.newPage) return;
        
        if(this.value == "") return;
        
        var id = this.getRecordData("id");
        if(id == "") return;
        
        var wrapper = document.getElementById(id+"-"+this.name);
        var data = BaseHelper.jsonStrToObject(this.value);        
        var tree = jsonTree.create(data, wrapper);
    }
    
    ngOnChanges()
    {              
        if(typeof this.recordJson != "undefined" && this.recordJson != "")
            this.record = BaseHelper.jsonStrToObject(this.recordJson);
            
        if(this.type.split(":")[1] == "newpage") this.newPage = true;
    }
    
    getRecordData(columnName)
    {
        if(this.record == null) return "";
        if(typeof this.record[columnName] == "undefined") return "";
        
        return this.record[columnName];
    }
    
    openInNewPage()
    {
        var left = (screen.width/2)-(640/2);
        var top = (screen.height/2)-(640/2);
        var newWin = open('url','Detay Görüntüleyici','height=640,width=640, top='+top+', left='+left);
        
        var html = "<html>";
            html += '<head>';
                html += '<link href="https://fonts.googleapis.com/css?family=PT+Mono" rel="stylesheet">';
                html += '<link href=" assets/ext_modules/jsonTreeViewer/libs/app/reset.css" rel="stylesheet">';
                html += '<link href=" assets/ext_modules/jsonTreeViewer/libs/app/app.css" rel="stylesheet">';
                html += '<link rel="stylesheet" href="assets/ext_modules/jsonTreeViewer/libs/jsonTree/jsonTree.css">';
                html += '<script src="assets/ext_modules/jsonTreeViewer/libs/jsonTree/jsonTree.js"></script>';
            html += '</head>';
            html += '<body>';
                html += '<br><div id="onizleme"></div>';
                html += '<script>';
                    html += 'var wrapper = document.getElementById("onizleme"); ';
                    html += 'var data = JSON.parse("'+BaseHelper.replaceAll(this.value, '"', '\\"')+'"); ';
                    html += 'var tree = jsonTree.create(data, wrapper); ';
                html += '</script>';
            html += '</body>';
        html += "</html>";
        
        newWin.document.write(html);
    }

    handleChange(event)
    {
        this.changed.emit(event);
    }
}