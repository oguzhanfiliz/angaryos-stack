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

    @Output() changed = new EventEmitter();

    val = null;

    ngAfterViewInit()
    {
    }

    ngOnChanges()
    {   
        if(this.valueJson == "") return;
        this.val = BaseHelper.jsonStrToObject(this.valueJson);
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
}