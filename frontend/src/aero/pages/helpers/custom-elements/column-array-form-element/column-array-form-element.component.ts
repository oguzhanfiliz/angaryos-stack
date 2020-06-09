import { Component, EventEmitter, Input, Output } from '@angular/core';

import { BaseHelper } from './../../base';
import { DataHelper } from './../../data';
import { MessageHelper } from './../../message';
import { SessionHelper } from './../../session';
import { GeneralHelper } from './../../general';
 
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
    @Input() createForm: boolean = false;
    @Input() inFormIsDataTransport: boolean = false;
    @Input() inFormDataTransportSelectOptionsJson: string = "";

    @Output() dataChanged = new EventEmitter();
    @Output() formSaved = new EventEmitter();
    @Output() inFormOpened = new EventEmitter();

    inFormColumnName = "";
    inFormTableName = "";
    inFormRecordId = 0;
    inFormElementId = "";
    inFormDataTransportSelectValues = {}

    columnArray = null;
    record = null;

    constructor(
        private messageHelper: MessageHelper,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper
    ) 
    { }



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
    
    cloneRelationRecord(columnName)
    {
        this.messageHelper.swalConfirm("Emin misiniz?", "Bu kaydı klonlamak istediğinize emin misiniz?", "warning")
        .then(async (r) =>
        {
            if(r != true) return;

            this.cloneRelationRecordConfirmed(columnName);
        });
    }
    
    cloneRelationRecordConfirmed(columnName)
    {
        var tableName = this.getDataFromColumnArray('columns.'+columnName+'.relation.table_name');
        var recordId = this.getSelectedOptionValue(columnName);
        
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+tableName+"/"+recordId+"/clone";
        
        this.generalHelper.startLoading();

        this.sessionHelper.doHttpRequest("GET", url)
        .then((data) => 
        {
            this.generalHelper.stopLoading();

            if(typeof data['message'] == "undefined")
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            else if(data['message'] == 'error')
            {
                var list = '';
                var keys = Object.keys(data['errors']);
                for(var i = 0; i < keys.length; i++)
                    for(var j = 0; j < data['errors'][keys[i]].length; j++)
                        list += ' - '+data['errors'][keys[i]][j] + '<br>';

                this.messageHelper.sweetAlert("Klon esnasında bazı hatalar oluştu!<br><br>"+(list), "Hata", "warning");
            }
            else if(data['message'] == 'success')
            {
                var params =
                {
                    inFormColumnName: columnName,
                    inFormRecordId: recordId,
                    inFormTableName: tableName,
                    in_form_data: {},//{source: 101, display: "test"}
                    data: data,
                    columnName: columnName,
                    tableName: tableName,
                    recordId: recordId,
                    inelementId: "ife-000",
                    message: "success"
                }

                this.cloneSuccess(params);
            }
            else
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
        })
        .catch((e) => { this.generalHelper.stopLoading(); });
    }
    
    cloneSuccess(params)
    {
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+this.tableName;
        url += "/getSelectColumnData/"+params['columnName']+"?search="+params['data']['id']+"&page=1&limit=500";
        url += "&editRecordId="+params['recordId'];
        
        var upColumnName = this.getDataFromColumnArray('columns.'+params['columnName']+'.up_column_name');
        if(upColumnName != null) url += "&upColumnName="+upColumnName;
        
        this.generalHelper.startLoading();
        
        $.ajax(
        {
            url : url,
            type : "GET",
            data : params,
            success : (data) =>
            {
                this.generalHelper.stopLoading();
                
                if(typeof data['results'] == 'undefined')
                {
                    this.messageHelper.sweetAlert("Klonlama yapıldı ama yeni kayıt bilgisi alınırken beklenmedik bir cevap geldi!", "Hata", "warning");
                }
                else
                {
                    for(var i = 0; i < data['results'].length; i++)
                        if(data['results'][i]['id'] == params['data']['id'])
                        { 
                            params['in_form_data']['source'] = data['results'][i]['id'];
                            params['in_form_data']['display'] = data['results'][i]['text'];
                            this.formSaved.emit(params);
                            this.messageHelper.toastMessage("Klonlama başarılı", "success");
                        }
                }
            },
            error : (e) =>
            {
                this.generalHelper.stopLoading();
                this.messageHelper.toastMessage("Bir hata oluştu", "warning");
            }
        });
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

        switch (guiType.split(':')[0]) 
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
            var selected = $(modalId+" #"+columnName+'-group .selected-option');
            if(selected.length != 1) return 0;

            var count = -1;
            var all = $(modalId+" #"+columnName+'-group .select2-selection__choice');
            for(var i = 0; i < all.length; i++)
                if(all[i] == selected[0])
                {
                    count = i;
                    break;
                }

            if(count == -1)
            {
                this.messageHelper.toastMessage("Seçili eleman getirilemedi!");
                return 0;
            }

            var data = $(elementId).select2("data");
            return data[count].id;                
        }
    }

    isColumnRelationDataAuth(columnName, type)
    {
        var tableName = this.getDataFromColumnArray('columns.'+columnName+'.relation.table_name');
        if(tableName == null) return false;

        if(typeof BaseHelper.loggedInUserInfo.auths == "undefined") return false;
        if(typeof BaseHelper.loggedInUserInfo.auths['tables'] == "undefined") return false;
        if(typeof BaseHelper.loggedInUserInfo.auths['tables'][tableName] == "undefined") return false;
        if(typeof BaseHelper.loggedInUserInfo.auths['tables'][tableName][type] == "undefined") return false;

        return true;
    }
    
    convertToObject(json)
    {
        return BaseHelper.jsonStrToObject(json);
    }



    /****    Gui Functions     *****/

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
        if(typeof obj == "string") return obj;
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