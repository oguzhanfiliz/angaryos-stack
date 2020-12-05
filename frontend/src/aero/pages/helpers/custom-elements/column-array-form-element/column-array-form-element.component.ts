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
    @Input() columnArrayJson: string = "";
    @Input() temp: string = "";
    @Input() recordJson: string = "";
    @Input() messagesJson: string = "";
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
    recordValueJson = null;
    messages = null;
    inFormDataTransportSelectOptions = null;

    constructor(
        private messageHelper: MessageHelper,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper
    ) 
    { 
        this.fillDefaultVariables();
        $.getScript('assets/ext_modules/select2/select2.min.js');
    }
    
    ngOnChanges()
    {
        this.fillVariables();        
    }
    
    fillDefaultVariables()
    {
        this.columnArray = 
        {
            id: 0,
            name: '',
            tree: '',
            columnNames: []
        };
        
        this.recordValueJson = {};
        
        this.inFormDataTransportSelectOptions = [];
    }
    
    fillVariables()
    {
        if(this.recordJson.length > 0) this.record = BaseHelper.jsonStrToObject(this.recordJson);
        else this.record = {};
            
        if(this.messagesJson.length > 0) this.messages = BaseHelper.jsonStrToObject(this.messagesJson);
        else this.messages = {};
            
        if(this.columnArrayJson.length > 0) this.columnArray = BaseHelper.jsonStrToObject(this.columnArrayJson);
        
        if(this.inFormDataTransportSelectOptionsJson.length > 0) this.inFormDataTransportSelectOptions = BaseHelper.jsonStrToObject(this.inFormDataTransportSelectOptionsJson);
        
            
        if(typeof this.columnArray['tree'] == "undefined") this.columnArray['tree'] = '';
        
        this.columnArray['columnNames'] = Object.keys(this.columnArray['columns']);
        for(var i = 0; i < this.columnArray['columnNames'].length; i++)
        {
            var columnName = this.columnArray['columnNames'][i];
            
            if(typeof this.messages[columnName] == "undefined") this.messages[columnName] = []; 
            
            if(typeof this.record[columnName] == "undefined") this.recordValueJson[columnName] = "";
            else this.recordValueJson[columnName] = BaseHelper.objectToJsonStr(this.record[columnName]);
            
            this.columnArray['columns'][columnName]['visible'] = this.columnIsVisible(columnName);
            
            if(typeof this.columnArray['columns'][columnName]['relation'] != "undefined") 
            {
                this.columnArray['columns'][columnName]['relationClass'] = 'tr-hover'; 
                this.columnArray['columns'][columnName]['isColumnRelationDataCreateAuth'] = this.isColumnRelationDataAuth(columnName, 'creates');
                this.columnArray['columns'][columnName]['isColumnRelationDataEditAuth'] = this.isColumnRelationDataAuth(columnName, 'edits'); 
            }
            else
            {
                this.columnArray['columns'][columnName]['relationClass'] = '';
            }
            
            this.columnArray['columns'][columnName]['columnInfo'] = this.getColumnInfo(columnName);
        }
    }
    
    showInfo(info)
    {
        this.messageHelper.sweetAlert(info, "Bilgi", "info");
    }
    
    getColumnInfo(columnName)
    {
        var info = this.columnArray['columns'][columnName]['column_info'];
        if(info == null) return "";        
        if(info.length == 0) return "";
        
        var obj = BaseHelper.jsonStrToObject(info);
        if(typeof obj[this.tableName] != "undefined") return obj[this.tableName];
        if(typeof obj["_all"] != "undefined") return obj["_all"];
        
        return "";
    }

    fillColumnVisibility()
    {
        this.columnArray['columnNames'] = Object.keys(this.columnArray['columns']);
        for(var i = 0; i < this.columnArray['columnNames'].length; i++)
        {
            var columnName = this.columnArray['columnNames'][i];
            this.columnArray['columns'][columnName]['visible'] = this.columnIsVisible(columnName);
        }
    }
    
    columnIsVisible(columnName)
    {
        var key = "formElementVisibility." + this.upFormId + "." + columnName;
        var temp = BaseHelper.readFromPipe(key);
        if(temp == null) return true;        
        return temp;
    }
    
    isColumnRelationDataAuth(columnName, type)
    {
        if(typeof this.columnArray['columns'][columnName]['relation'] == "undefined") return false;
        var tableName = this.columnArray['columns'][columnName]['relation']['table_name']

        if(typeof BaseHelper.loggedInUserInfo.auths == "undefined") return false;
        if(typeof BaseHelper.loggedInUserInfo.auths['tables'] == "undefined") return false;
        if(typeof BaseHelper.loggedInUserInfo.auths['tables'][tableName] == "undefined") return false;
        if(typeof BaseHelper.loggedInUserInfo.auths['tables'][tableName][type] == "undefined") return false;

        return true;
    }

    changed(columnName, event)
    {
        setTimeout(() => this.fillColumnVisibility(), 200);

        event['columnName'] = columnName;
        this.dataChanged.emit(event);
    }
    
    addRelationRecord(columnName)
    {
        this.inFormTableName = this.columnArray['columns'][columnName]['relation']['table_name'];
        this.inFormColumnName = columnName;

        this.inFormRecordId = 0;

        var rand = Math.floor(Math.random() * 10000) + 1;
        this.inFormElementId = "ife-"+rand;
        
        setTimeout(() => 
        {
            $('#'+this.inFormElementId+'inFormModal').modal('show')
            .on('hidden.bs.modal', () => this.setFormOverflow());
        }, 100);
    }
    
    editRelationRecord(columnName)
    {
        this.inFormTableName = this.columnArray['columns'][columnName]['relation']['table_name'];
        this.inFormColumnName = columnName;
        
        this.inFormRecordId = this.getSelectedOptionValue(columnName);
        if(this.inFormRecordId < 1) return;
   
        var rand = Math.floor(Math.random() * 10000) + 1;
        this.inFormElementId = "ife-"+rand;
        
        setTimeout(() => 
        {
            $('#'+this.inFormElementId+'inFormModal').modal('show')
            .on('hidden.bs.modal', () => this.setFormOverflow());
        }, 100);
    }
    
    getSelectedOptionValue(columnName)
    {
        var elementId = '[name="'+columnName+'"]';
        if(this.upFormId.length > 0) elementId =  '#'+this.upFormId+'inFormModal ' + elementId;

        var val = $(elementId).val();
        var guiType = this.columnArray['columns'][columnName]['gui_type_name'];

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
            //var selected = $(modalId+" #"+columnName+'-group .selected-option');
            var selected = $(modalId+" [ng-reflect-name='"+columnName+"'] .selected-option");
            if(selected.length != 1) return 0;

            var count = -1;
            var all = $(modalId+" [ng-reflect-name='"+columnName+"'] .select2-selection__choice");
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

            var temp = $(elementId).find(':selected')[count];
            var id = $(temp).attr('item-source');
            return id;                
        }
    }
    
    inFormload(data)
    {
        data['ife'] = this.inFormElementId;
        this.inFormOpened.emit(data);
    }
    
    inFormSavedSuccess(data)
    {
        this.closeModal(this.inFormElementId+'inFormModal');
        
        setTimeout(() =>
        {
            this.inFormColumnName = "";
            this.inFormTableName = "";
            this.inFormRecordId = 0;
            this.inFormElementId = "";
            this.inFormDataTransportSelectValues = {};
            
            this.formSaved.emit(data);
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
        var tableName = this.columnArray['columns'][columnName]['relation']['table_name'];
        var recordId = this.getSelectedOptionValue(columnName);
        
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+tableName+"/"+recordId+"/clone";
        
        this.sessionHelper.doHttpRequest("POST", url)
        .then((data) => this.cloneRelationRecordResponsed(data, tableName, columnName, recordId));
    }
    
    cloneRelationRecordResponsed(data, tableName, columnName, recordId)
    {
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
    }
    
    cloneSuccess(params)
    {
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+this.tableName;
        url += "/getSelectColumnData/"+params['columnName']+"?search="+params['data']['id']+"&page=1&limit=500";
        url += "&editRecordId="+params['recordId'];
        
        var upColumnName = this.columnArray['columns'][params['columnName']]['up_column_name'];
        if(upColumnName != null) url += "&upColumnName="+upColumnName;
                
        $.ajax(
        {
            url : url,
            type : "POST",
            data : params,
            success : (data) =>
            {
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
                this.messageHelper.toastMessage("Bir hata oluştu", "warning");
            }
        });
    }

    closeModal(id)
    {
        BaseHelper.closeModal(id);
    }
    
    setFormOverflow()
    {
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
    
    copyOrPasteColumnData($event, columnName)
    {
        if(event['ctrlKey']) this.copyColumnData(columnName);
        else if(event['altKey']) this.pasteColumnData(columnName);
    }
    
    copyColumnData(columnName)
    {
        if(this.record == null) return;
        if(typeof this.record[columnName] == "undefined") return;
        if(this.record[columnName] == null) return;
        
        var data = 
        {
            columnGuiType: this.columnArray['columns'][columnName]['gui_type_name'],
            columnData: this.record[columnName]
        };
        
        BaseHelper.writeToLocal('copyedColumnData', data);
        
        this.messageHelper.toastMessage("Kopyalandı!");
    }
    
    pasteColumnData(columnName)
    {
        var guiType = this.columnArray['columns'][columnName]['gui_type_name'];
        
        var data = BaseHelper.readFromLocal('copyedColumnData');        
        if(data == null) return;
        
        if(data['columnGuiType'] != guiType)
        {
            this.messageHelper.toastMessage("Kolon tipi uyumsuz!");
            return;
        }
        
        this.record[columnName] = data['columnData'];
        
        this.recordJson = BaseHelper.objectToJsonStr(this.record);
        this.recordValueJson[columnName] = BaseHelper.objectToJsonStr(this.record[columnName]);
        
        this.messageHelper.toastMessage("Yapıştırıldı!");
    }
}