import { Component, EventEmitter, Input, Output } from '@angular/core';

import { BaseHelper } from './../../base';
import { DataHelper } from './../../data';
import { MessageHelper } from './../../message';
 
declare var $: any;

@Component(
{
    selector: 'column-array-form-element', 
    styleUrls: ['./column-array-form-element.component.scss'],
    templateUrl: './column-array-form-element.component.html'
})
export class ColumnArrayFormElementComponent
{
    @Input() columnArrayJson: string;
    @Input() recordJson: string;
    @Input() tableName: string;
    @Input() upFormId: string = "";

    @Output() dataChanged = new EventEmitter();
    @Output() formSaved = new EventEmitter();
    @Output() inFormOpened = new EventEmitter();

    inFormColumnName = "";
    inFormTableName = "";
    inFormRecordId = 0;
    inFormElementId = "";

    columnArray = null;
    record = null;

    constructor(
        private messageHelper: MessageHelper
    ) { }



    /****    Event Functions    ****/
    
    ngOnChanges()
    {
        if(typeof this.columnArrayJson != "undefined" && this.columnArrayJson != "")
            this.columnArray = BaseHelper.jsonStrToObject(this.columnArrayJson);

        if(typeof this.recordJson != "undefined" && this.recordJson != "")
            this.record = BaseHelper.jsonStrToObject(this.recordJson);
    }

    changed(columnName, event)
    {
        event['columnName'] = columnName;
        this.dataChanged.emit(event);
    }

    inFormSavedSuccess(data)
    {
        this.formSaved.emit(data);
        this.closeModal(this.inFormElementId+'inFormModal');
    }

    inFormload(data)
    {
        data['ife'] = this.inFormElementId;
        this.inFormOpened.emit(data);
    }

    addRelationRecord(columnName)
    {
        this.inFormTableName = this.getDataFromColumnArray('columns.'+columnName+'.relation.table_name');
        this.inFormColumnName = columnName;

        this.inFormRecordId = 0;

        var rand = Math.floor(Math.random() * 10000) + 1;
        this.inFormElementId = "ife-"+rand;
        
        setTimeout(() => 
        {
            $('#'+this.inFormElementId+'inFormModal').modal('show');
        }, 100);
    }

    editRelationRecord(columnName)
    {
        this.inFormTableName = this.getDataFromColumnArray('columns.'+columnName+'.relation.table_name');
        this.inFormColumnName = columnName;
        
        this.inFormRecordId = this.getSelectedOptionValue(columnName);
        if(this.inFormRecordId < 1) return;

        var rand = Math.floor(Math.random() * 10000) + 1;
        this.inFormElementId = "ife-"+rand;
        
        setTimeout(() => 
        {
            $('#'+this.inFormElementId+'inFormModal').modal('show');
        }, 100);
    }

    getSelectedOptionValue(columnName)
    {
        var elementId = '[name="'+columnName+'"]';
        if(this.upFormId.length > 0)
            elementId =  '#'+this.upFormId+'inFormModal ' + elementId;

        var val = $(elementId).val();
        var guiType = this.getDataFromColumnArray('columns.'+columnName+'.gui_type_name');

        switch (guiType) 
        {
            case "select": 
                val = this.getSelectedOptionValueSelect(val);
                break;
            case "multiselect": 
                val = this.getSelectedOptionValueMultiSelect(val, elementId, columnName);                
                break;
            case 'multiselectdragdrop':
                val = this.getSelectedOptionValueMultiSelectDragDrop(elementId, columnName);                
                break;
            default:
                console.log("Value tipi geçersiz: " + (typeof val));
                val = -1;
                break;
        }

        if(val == 0) this.messageHelper.toastMessage("Bir kayıt seçmelisiniz!");
        else if(val < 0) this.messageHelper.toastMessage("Bir sorun oluştu!");
        
        return val
    }

    getSelectedOptionValueSelect(val)
    {
        if(val == "") val = 0;
        else val = parseInt(val);

        return val;
    }

    getSelectedOptionValueMultiSelectDragDrop(elementId, columnName)
    {
        //var modalId = elementId.replace('#'+columnName, '');
        var modalId = elementId.replace('[name="'+columnName+'"]', '');

        var selectedId = modalId + ' [ng-reflect-name="'+columnName+'"] .selected-list .selected-option';
        var selected = $(selectedId);
        if(selected.length != 1) return 0;

        return parseInt(selected.attr('source'));
    }

    getSelectedOptionValueMultiSelect(val, elementId, columnName)
    {
        var modalId = elementId.replace('[name="'+columnName+'"]', '');
        
        if(val.length == 0) return 0;
        else if(val.length == 1) return parseInt(val[0]);
        else
        {
            //var selected = $(elementId+'-group .selected-option');
            var selected = $(modalId+" #"+columnName+'-group .selected-option');
            if(selected.length != 1) return 0;

            var control = false;

            var selectedItem = selected.html().split('</span>')[1];
            var data = $(elementId).select2("data");
            for(var i = 0; i < data.length; i++)
                if(data[i].text == selectedItem)
                {
                    val = data[i].id;
                    control = true;
                    break;
                }
                
            if(!control)
            {
                this.messageHelper.toastMessage(selectedItem+" bulunamadı!");
                val = 0;
            }

            return val;
        }
    }



    /****    Gui Functions     *****/

    clearColumnData(columnName)
    {
        console.log(this.record);
    }

    columnIsVisible(columnName)
    {
        var key = "formElementVisibility." + this.upFormId + "." + columnName;
        var temp = BaseHelper.readFromPipe(key);

        if(temp == null) return true;
        
        return temp;
    }

    getDataFromColumnArray(path = '')
    {
        return DataHelper.getData(this.columnArray, path);
    }

    getDataFromRecord(path = '')
    {
        return DataHelper.getData(this.record, path);
    }

    getValue(columnName)
    {
        if(this.record == null) return "";

        return this.record[columnName];
    }

    getColumnNamesFromColumnArray(columnArray)
    {
        return Object.keys(columnArray.columns);
    }

    getJson(obj)
    {
        return BaseHelper.objectToJsonStr(obj);
    }

    closeModal(id)
    {
        BaseHelper.closeModal(id);
        setTimeout(() => 
        {
            if(this.upFormId.length > 0)
            {
                $('#'+this.upFormId+'inFormModal').css('overflow', 'auto');
                $('body').css('overflow', 'hidden');
            }
            else
            {
                $('body').css('overflow', 'auto');
            }
        }, 100);
    }
}