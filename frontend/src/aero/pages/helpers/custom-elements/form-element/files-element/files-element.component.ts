import { Component, EventEmitter, Input, Output } from '@angular/core';
import { BaseHelper } from './../../../base';
import { DataHelper } from './../../../data';

declare var $: any;

@Component(
{
    selector: 'files-element',
    styleUrls: ['./files-element.component.scss'],
    templateUrl: './files-element.component.html'
})
export class FilesElementComponent
{
    @Input() defaultData: string;
    @Input() baseUrl: string;
    @Input() value: string;
    @Input() valueJson: string = "";
    @Input() class: string;
    @Input() name: string;
    @Input() columnName: string;
    @Input() placeholder: string;
    @Input() showFilterTypesSelect: boolean;
    @Input() filterType: string;
    @Input() upColumnName: string;
    @Input() createForm: boolean = false;

    @Output() changed = new EventEmitter();

    val = null;

    ngAfterViewInit()
    {
    }

    ngOnChanges()
    {   
        if(this.valueJson == "") return;
        
        this.val = BaseHelper.jsonStrToObject(this.valueJson);

        if(typeof this.val == "string") 
            this.val = BaseHelper.jsonStrToObject(this.val);

        if(this.val != null)
            for(var i = 0; i < this.val.length; i++)
            {
                this.val[i]['smallUrl'] = BaseHelper.getFileUrl(this.val[i], 's_');
                this.val[i]['bigUrl'] = BaseHelper.getFileUrl(this.val[i], 'b_');
                this.val[i]['url'] = BaseHelper.getFileUrl(this.val[i], '');
                this.val[i]['iconUrl'] = this.getFileIconUrl(this.val[i].file_name);
                this.val[i]['isImageFile'] = this.isImageFile(this.val[i].file_name);
            }
    }

    handleChange(event)
    {
        this.changed.emit(event);
    }

    getJson(obj)
    {
        return BaseHelper.objectToJsonStr(obj);
    }

    delete(file)
    {
        for(var i = 0; i < this.val.length; i++)
            if(file.file_name == this.val[i].file_name)
                this.val.splice(i,1);
    }

    isImageFile(file)
    {
        if(file == null) return false;
        if(file == "") return false;

        var imgExts = ["jpg", "jpeg", "png", "gif"]
        var temp = file.split('.');
        var ext = temp[temp.length-1];

        return imgExts.includes(ext.toLowerCase());
    }

    getFileIconUrl(fileName)
    {
        var temp = fileName.split('.');
        var ext = temp[temp.length-1];

        var iconBaseUrl = "assets/img/";
        
        switch(ext.toLowerCase())
        {
            default: return iconBaseUrl+"download_file.png";
        }
    }
}