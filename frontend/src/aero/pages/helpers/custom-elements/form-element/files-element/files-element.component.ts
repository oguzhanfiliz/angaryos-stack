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

        for(var i = 0; i < this.val.length; i++)
        {
            this.val[i]['smallUrl'] = BaseHelper.getFileUrl(this.val[i], 's_');
            this.val[i]['bigUrl'] = BaseHelper.getFileUrl(this.val[i], 'b_');
            this.val[i]['url'] = BaseHelper.getFileUrl(this.val[i], '');
        }
    }

    handleChange(event)
    {
        this.changed.emit(event);
    }

    getData(path = '')
    {
        if(this.val == null) return null;

        return DataHelper.getData(this.val, path);
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

        var imgExts = ["jpg", "png", "gif"]
        var temp = file.split('.');
        var ext = temp[temp.length-1];

        return imgExts.includes(ext);
    }

    getFileIconUrl(fileUrl)
    {
        var temp = fileUrl.split('.');
        var ext = temp[temp.length-1];

        var iconBaseUrl = "assets/img/";
        
        switch(ext)
        {
            default: return iconBaseUrl+"download_file.png";
        }
    }
}