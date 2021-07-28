import { ActivatedRoute} from '@angular/router';
import { Component, EventEmitter, Input, Output, ChangeDetectorRef  } from '@angular/core';

import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { GuiTriggerHelper } from './../helpers/gui-trigger';
import { DataHelper } from './../helpers/data';
import { GeneralHelper } from './../helpers/general';
import { MessageHelper } from './../helpers/message';
import { AeroThemeHelper } from './../helpers/aero.theme';

declare var $: any;
 
@Component(
{
    selector: 'in-form-element',
    styleUrls: ['./form.component.scss'],
    templateUrl: './form.component.html'
})
export class FormComponent 
{
    @Input() id: string = "";
    @Input() inFormTableName: string = ""; 
    @Input() inFormColumnName: string = "";
    @Input() inFormIsDataTransport: boolean = false;
    @Input() inFormDataTransportSelectOptionsJson: string = "";
    @Input() inFormRecordId: number = 0;
    
    @Input() singleColumn: boolean = false;

    @Output() formSaved = new EventEmitter();
    @Output() formChanged = new EventEmitter();
    @Output() formLoad = new EventEmitter();
    @Output() inFormOpened = new EventEmitter();

    tableName = "";
    recordId = null;
    
    loading = false;

    data = null;
    
    temp = null;
    
    constructor(
        public route: ActivatedRoute,
        public sessionHelper: SessionHelper,
        public generalHelper: GeneralHelper,
        public aeroThemeHelper: AeroThemeHelper,
        public messageHelper: MessageHelper,
        public guiTriggerHelper: GuiTriggerHelper,
        private cdr: ChangeDetectorRef
        ) 
    {
        this.fillDefaultVariables();
        
        var th = this;
        setTimeout(() => route.params.subscribe(val => th.preLoadInterval(val)), 100);
    }
    
    ngOnChanges()
    {
        this.preLoadInterval(); 
    }
    
    preLoadInterval(val = null)
    {
        var params =
        {
            val: val,
            th: this
        };

        function func(params)
        {
            params.th.preLoad(params.val);
        }

        return BaseHelper.doInterval('formPreLoad', func, params, 200);
    }
    
    preLoad(val)
    {
        this.fillDefaultVariables();
        this.fillTableNameAndRecordId(val);
                
        this.dataReload();
        
        this.cdr.detectChanges();
        
        this.addEventForFeatures();
        this.addEventForThemeIcons();
        
        this.aeroThemeHelper.pageRutine();
    }
    
    fillDefaultVariables()
    {
        this.data = {};
        
        this.data['title'] = '';
        
        this.data['sectionClass'] = this.getSectionClass();
        
        this.data['column_set'] = [];
        this.data['column_set']['column_set_type'] = 'none';
        this.data['column_set']['column_arrays'] = [];
        
        this.data['messages'] = {};
        this.data['messagesJson'] = "";
    }
    
    fillTableNameAndRecordId(val = null)
    {
        if(this.inFormTableName.length == 0 && typeof val.tableName != "undefined")
        {
            this.tableName = val.tableName;
            this.recordId = val.recordId;
        }
        else if(this.inFormTableName.length > 0)
        {
            this.tableName = this.inFormTableName;
            this.recordId = this.inFormRecordId;
        }
        
        if(this.recordId == null) this.recordId = 0;
    }

    getSectionClass()
    {
        var c = "content";
        if(this.id.length > 0) c += " inFormElementSection";           
        return c;
    }
    
    getColumnSetId(type)
    {
        var tables = BaseHelper.loggedInUserInfo['auths']['tables'];
        
        if(typeof tables[this.tableName] == "undefined") return 0;
        if(typeof tables[this.tableName][type] == "undefined") return 0;
        if(typeof tables[this.tableName][type][0] == "undefined") return 0;
        
        return tables[this.tableName][type][0];
    }
    
    getParamsForFormData()
    {
        var type = null; 
        if(this.recordId == 0) type = "creates";
        else type = "edits";

        var params = {}
        params['column_set_id'] = this.getColumnSetId(type)
        if(this.singleColumn) params['single_column_name'] = this.inFormColumnName;

        return params;
    }
            
    dataReload()
    {
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+this.tableName+"/";
        if(this.recordId == 0) url += "create";
        else url += this.recordId + "/edit";

        var params = this.getParamsForFormData();

        var data = {'params': BaseHelper.objectToJsonStr(params)};
        this.sessionHelper.doHttpRequest("POST", url, data)
        .then((data) => this.dataLoaded(data));
    }
    
    dataLoaded(data)
    {
        this.data = this.fillDataAdditionalVariables(data);
        
        this.addEventForFeatures();
        setTimeout(() => 
        {
            this.changeColumnVisibilityGuiTrigger();
            this.formLoad.emit(data);
            
            setTimeout(() => this.triggerAllColumns(), 1000);
        }, 100); 
    }
    
    triggerAllColumns()
    {
        var columnArrays = this.data['column_set']['column_arrays'];
        for(var i = 0; i < columnArrays.length; i++)
        {
            var columnNames = Object.keys(columnArrays[i]['columns']);            
            for(var k = 0; k < columnNames.length; k++)
            {
                var columnName = columnNames[k];
                this.change({columnName: columnName});
            }
        }
    }
    
    changeColumnVisibilityGuiTrigger()
    {
        var data = this.getElementsData("GET");
        var columnNames = Object.keys(data);
        for(var i = 0; i < columnNames.length; i++)
            this.guiTriggerHelper.changeColumnVisibility(this.tableName, columnNames[i], this.getElementId(columnNames[i]), data);
            
        setTimeout(() => this.changedColumnVisibilityOnPipe(), 200);
    }
    
    changedColumnVisibilityOnPipe()
    {
        this['temp'] = Math.random();
    }
    
       
    fillDataAdditionalVariables(data)
    {
        var manyChar = ['ler','lar'];
        var tableDisplayName = data['table_info']['display_name'];
        var subChar= tableDisplayName.substr(tableDisplayName.length - 3);
        var currentTableName;
        if(manyChar.includes(subChar)){
            currentTableName = tableDisplayName.slice(0,-3);
        }else{
            currentTableName = tableDisplayName;
        }    
        data['title'] = DataHelper.getTitleOrDefault(data['column_set']['name'], this.recordId == 0 ? currentTableName+' Ekle' : 'Düzenle');
        
        var recordJson = ''; 
        if(typeof data['record'] != "undefined") recordJson = BaseHelper.objectToJsonStr(data['record']);
        else data['record'] = {};
        data['record']['json'] = recordJson;
        
        data['sectionClass'] = this.getSectionClass();
            
        for(var i = 0; i < data['column_set']['column_arrays'].length; i++)
        {
            var json = BaseHelper.objectToJsonStr(data['column_set']['column_arrays'][i]);
            data['column_set']['column_arrays'][i]['json'] = json;
            
            var title = DataHelper.getTitleOrDefault(data['column_set']['column_arrays'][i]['name_basic'], '');
            data['column_set']['column_arrays'][i]['title'] = title;
        }
        
        return data;  
    }
    
    
    getParamsForSave()
    {
        if(this.inFormIsDataTransport)
        {
            var params = this.getElementsData();
            this.formSaved.emit(params);
            return;
        }

        var params = this.getElementsData(BaseHelper.formSendMethod);
        
        if(this.inFormColumnName.length > 0)
        {
            if(BaseHelper.formSendMethod == "POST")
                params.append('in_form_column_name', this.inFormColumnName);
            else
                params['in_form_column_name'] = this.inFormColumnName;
        }

        if(this.singleColumn)
        {
            if(BaseHelper.formSendMethod == "POST")
                params.append('single_column', this.inFormColumnName);
            else
                params['single_column'] = this.inFormColumnName;
        }
        
        return params;
    }
    
    save()
    {
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+this.tableName+"/";
        if(this.recordId == 0) url += "store";
        else url += this.recordId + "/update";

        var params = this.getParamsForSave();
        if(typeof params == "undefined") return;
        
        this.sessionHelper.doHttpRequest(BaseHelper.formSendMethod, url, params) 
        .then((data) => this.saveSuccess(data));
    }

    saveSuccessMessageControl(data)
    {
        if(typeof data['message'] == "undefined")
        {
            this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            return false;
        }
        else if(data['message'] == 'error')
        {
            this.messageHelper.sweetAlert("Formda bazı hatalar var! Kontrol edin.", "Hata", "warning");

            this.fillFormErrorMessages(data['errors']);
            return false;
        }
        else if(data['message'] == 'success')
            return true;
        else
        {
            this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            return false;
        }
    }
    
    saveSuccess(data)
    {
        if(!this.saveSuccessMessageControl(data)) return;
        
        this.messageHelper.toastMessage("Kayıt başarılı", "success");

        if(this.singleColumn) this.formSaved.emit(data);
        else if(this.id.length == 0)
        {
            if(typeof BaseHelper.loggedInUserInfo.auths.tables[this.tableName]['lists'] == "undefined")
                this.formSaved.emit(data);
            else
            {
                var ext = ['subscribers', 'missions'];
                if(this.recordId > 0 && ext.includes(this.tableName)) return;
                
                if(this.data["table_info"]["e_sign"])
                {
                    var temp = BaseHelper.readFromPipe('basePageComponent');
                    temp.fillESigns();
                }

                this.generalHelper.navigate('table/'+this.tableName);
            }
        }    
        else
        {
            data['inFormTableName'] = this.inFormTableName;
            data['inFormColumnName'] = this.inFormColumnName;
            data['inelementId'] = this.id;
            data['inFormRecordId'] = this.inFormRecordId;
            data['inFormElementsData'] = this.getElementsData("GET");
            
            this.formSaved.emit(data); 
        }
    }
    
    fillFormErrorMessages(errors)
    {
        this.data['messages'] = {};
        this.data['messagesJson'] = "";

        var columnNames = Object.keys(errors);
        for(var i = 0; i < columnNames.length; i++)
        {
            var columnName = columnNames[i];
            var errorMessage = errors[columnName];
            
            if(typeof this.data['messages'][columnName] == "undefined") this.data['messages'][columnName] = [];
                
            var temp = 
            {
                type: 'danger',
                message: errorMessage
            };
            
            this.data['messages'][columnName].push(temp);            
        }

        this.data['messagesJson'] = BaseHelper.objectToJsonStr(this.data['messages']);
    }
    
    getElementsData(type = "POST")
    {
        var data = null;
        if(type == "GET") data = {};
        else data = new FormData();

        var columnArrays = this.data['column_set']['column_arrays'];
        for(var i = 0; i < columnArrays.length; i++)
        {
            var columnArray = columnArrays[i];
            if(columnArray['column_array_type'] != 'direct_data') continue;

            var columnNames = Object.keys(columnArray['columns']);            
            for(var k = 0; k < columnNames.length; k++)
            {
                var columnName = columnNames[k];
                this.appendColumnDataForGetElementsData(type, data, columnArray, columnName);
            }
        }

        var formType = null; 
        if(this.recordId == 0) formType = "creates";
        else formType = "edits";

        var column_set_id = BaseHelper.loggedInUserInfo.auths.tables[this.tableName][formType][0];
        if(type == "GET") data['column_set_id'] = column_set_id;
        else data.append('column_set_id', column_set_id);

        if(type == 'GET') data['id'] = this.recordId;
        else data.append('id', this.recordId);

        return data; 
    }
    
    getElementId(columnName)
    {
        var id = '[name="'+columnName+'"]';
        if(this.id.length > 0) id = '[ng-reflect-id="'+this.id+'"] ' + id;
        if(columnName == "name") id += ":last-child";
        
        return id;
    }
    
    appendColumnDataForGetElementsData(type, data, columnArray, columnName)
    {
        
        var guiType = columnArray['columns'][columnName]['gui_type_name'];
        
        if(type == "POST" && guiType == 'files')
        {
            var val = $(this.getElementId(columnName+"_old")).val(); 
            if(typeof val == "undefined") return;
            data.append(columnName+"_old", val);
                   
            var files = $(this.getElementId(columnName))[0].files;
            for(var l = 0; l < files.length; l++)
                data.append(columnName+"[]", files[l]);
                
            return;
        }
        
        var columnData = "";

        if(this.columnIsVisible(columnName))
        {
            var temp = $(this.getElementId(columnName)).val();
            if(typeof temp == "undefined") return;
            if(temp == null) temp = "";
            columnData = DataHelper.changeDataForFormByGuiType(guiType, temp);
        }

        if(type == "GET") data[columnName] = columnData;
        else data.append(columnName, columnData);
    }
    
    columnIsVisible(columnName)
    {
        var key = "formElementVisibility." + this.id + "." + columnName;
        var temp = BaseHelper.readFromPipe(key);

        if(temp == null) return true;
        
        return temp;
    }
    
    guiTriggerFunctionControl(colName, fncName)
    {
        if(typeof this.guiTriggerHelper[colName] == "undefined")
        {
            this.messageHelper.toastMessage("Tetikleme fonksiyonu yok: " + fncName, "error", 6000);
            return false;
        }
        
        return true;
    }
    
    change(event)
    {
        var params =
        {
            event: event,
            th: this
        };
        
        function func(params)
        {
            var data = params.th.getElementsData("GET");

            var tableName = params.th.tableName;
            var columnName = params.event.columnName;
            var elementId = params.th.getElementId(columnName);
            
            params.th.guiTriggerHelper.changeColumnVisibility(tableName, columnName, elementId, data);            

            var forAllColumns = params.th.data['gui_triggers']['all'];
            if(forAllColumns != null)
                for(var i = 0; i < forAllColumns.length; i++)
                    if(params.th.guiTriggerFunctionControl(forAllColumns[i], forCurrentColumns[i]))
                        params.th.guiTriggerHelper[forAllColumns[i]](tableName, columnName, elementId, data)
                        .then((data) => params.th.guiTriggered(columnName, data));
               
            var forCurrentColumns = params.th.data['gui_triggers'][columnName];
            if(forCurrentColumns != null)
                for(var i = 0; i < forCurrentColumns.length; i++)
                    if(params.th.guiTriggerFunctionControl(forCurrentColumns[i], forCurrentColumns[i]))
                        params.th.guiTriggerHelper[forCurrentColumns[i]](tableName, columnName, elementId, data)
                        .then((data) => params.th.guiTriggered(columnName, data));
                        
            setTimeout(() => params.th.changedColumnVisibilityOnPipe(), 200);
        }

        return BaseHelper.doInterval('formElementChanged'+event['columnName'], func, params, 500);
    }
    
    guiTriggered(columnName, data = null)
    {
        this.data['messages'] = {};
        this.data['messagesJson'] = "";
        
        if(data == null) return;
                
        var typeNames = Object.keys(data);
        for(var i = 0; i < typeNames.length; i++)
        {
            var typeName = typeNames[i];
            
            if(typeof this.data['messages'][columnName] == "undefined") this.data['messages'][columnName] = [];
                
            var temp = 
            {
                type: typeName,
                message: data[typeName]
            };
            
            this.data['messages'][columnName].push(temp);
        }

        this.data['messagesJson'] = BaseHelper.objectToJsonStr(this.data['messages']);
    }

    inFormload(data)
    {
        this.inFormOpened.emit(data);
    }
    
    inFormSavedSuccess(data)
    {
        var elementId = this.getElementId(data['inFormColumnName']);
        
        var guiType = this.getGuiTypeByColumnName(data['inFormColumnName']);
        switch (guiType.split(':')[0]) 
        {
            case 'select':
                this.inFormSavedSuccessSelect(elementId, data['in_form_data']);
                break;
            case 'multiselect':
                this.inFormSavedSuccessMultiSelect(elementId, data['in_form_data']);
                break;
            case 'multiselectdragdrop':
                this.inFormSavedSuccessMultiSelectDragDrop(elementId, data);
                break;
            default:
                alert("inForm başarılı elemente bir eleman ekle: " + guiType);
        }

        this.change({columnName: data['inFormColumnName']});
        this.formChanged.emit(data);
    }

    inFormSavedSuccessSelect(elementId, data)
    {
        var option = '<option value="'+data['source']+'">';
        option += data['display']+'</option>';

        $(elementId).html(option);
        $(elementId).val(data['source']);
    }

    inFormSavedSuccessMultiSelectDragDrop(elementId, data)
    {
        var columnName = data['inFormColumnName'];
        var inFormData = data['in_form_data'];

        var control = false;
        if(this.data['record']['json']  == "")
            this.data['record'][columnName] = [];
        else
        {
            var control = false;
            for(var i = 0; i < this.data['record'][columnName].length; i++)
                if(this.data['record'][columnName][i]['source'] == inFormData['source'])
                {
                    this.data['record'][columnName][i]['display'] = inFormData['display'];
                    control = true;
                }
        }

        if(!control)
            this.data['record'][columnName].push(
            {
                source: inFormData['source'], 
                display: inFormData['display']
            });
            
        this.data['record']['json'] = BaseHelper.objectToJsonStr(this.data['record']);
    }

    inFormSavedSuccessMultiSelect(elementId, data)
    {
        var val = $(elementId).val();
        
        var temp = [];
        for(var i = 0; i < val.length; i++) if(val[i].length > 0) temp.push(val[i]);
        
        val = temp;

        var option = '<option value="'+data['source']+'">';
        option += data['display']+'</option>';
        
        if(
            val.includes(data['source'])
            || val.includes(parseInt(data['source']))
            || val.includes(data['source'].toString())
        )
        {
            //$(elementId+' option[value="'+data['source']+'"]').insertAfter(option);
            $(elementId+' option[value="'+data['source']+'"]').remove();
            $(elementId).append(option);
        }
        else
        {
            $(elementId).append(option);
            val.push(data['source']); 
        }

        $(elementId).val(val);
    }
    
    getGuiTypeByColumnName(columnName)
    {
        var columnsArrays = this.data['column_set']['column_arrays'];
        for(var j = 0; j < columnsArrays.length; j++)
        {
            var columnArray = columnsArrays[j];
            
            var columnNames = Object.keys(columnArray['columns']);            
            for(var k = 0; k < columnNames.length; k++)
                if(columnName == columnNames[k])
                    return columnArray['columns'][columnName]['gui_type_name'];
        }

        return "";
    }

    addEventForFeatures()
    {
        this.aeroThemeHelper.addEventForFeature("standartElementEvents");

        setTimeout(() => 
        { 
            this.addSelect2ElementCss(); 
        }, 200);
    }

    addSelect2ElementCss()
    {
        $('.select2-container').css('margin', '0');
        $('.select2-selection').css('height', '35px');
        $('.select2-selection__rendered').css('padding-top', '3px')
    }

    addEventForThemeIcons()
    {
        this.aeroThemeHelper.addEventForFeature("mobileMenuButton");
        this.aeroThemeHelper.addEventForFeature("rightIconToggleButton");
    }
}
