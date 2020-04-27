import { ActivatedRoute} from '@angular/router';
import { Component, EventEmitter, Input, Output } from '@angular/core';

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
    @Input() inFormRecordId: number = 0;
    
    @Input() singleColumn: boolean = false;

    @Output() formSaved = new EventEmitter();
    @Output() formChanged = new EventEmitter();
    @Output() formLoad = new EventEmitter();
    @Output() inFormOpened = new EventEmitter();

    public tableName = "";
    public recordId = null;

    private loading = true;
    
    constructor(
        public route: ActivatedRoute,
        public sessionHelper: SessionHelper,
        public generalHelper: GeneralHelper,
        public aeroThemeHelper: AeroThemeHelper,
        public messageHelper: MessageHelper,
        public guiTriggerHelper: GuiTriggerHelper
        ) 
    {
        var th = this;
        setTimeout(() => 
        {
            route.params.subscribe(val => 
            {
                if(th.inFormTableName.length == 0 && typeof val.tableName != "undefined")
                {
                    th.tableName = val.tableName;
                    th.recordId = val.recordId;
                }
                else return;

                th.formRender();
            });
        }, 100);
    }

    ngOnChanges()
    {
        if(this.inFormTableName.length > 0)
        {
            this.tableName = this.inFormTableName;
            this.recordId = this.inFormRecordId;
        }
        else return;

        this.formRender();
    }

    formRender()
    {
        if(typeof this.recordId == "undefined") this.recordId = 0;

        this.addEventForFeatures();
        this.addEventForThemeIcons();

        setTimeout(() => {
            BaseHelper.deleteFromPipe(this.getLocalKey());
            this.loadData();
        }, 250);
    }



    /****    Gui Functions    ****/

    getMessageGroupId(columnName)
    {
        var id = this.getElementId(columnName)
                    .replace('[name="'+columnName+'"]', columnName)
                    .replace(':last-child', '')
                    .trim()
                    .replace('  ', ' ');

        if(id.indexOf('"ife-') > -1)
            id = id.replace(' '+columnName, ' #'+columnName);
        else 
            id = "#"+id;

        id += "-group";

        return id;
    }

    addFormElementMessage(columnName, type, message, cls)
    {
        columnName = columnName.split('.')[0];

        var html = "<span id='"+columnName+"-message-"+type+"' ";
        html += " style='margin-right: 5px;' ";
        html += " class='"+cls+" badge badge-"+type+"'>"+message+"</span>";

        var id = this.getMessageGroupId(columnName);

        $(id).append(html);
    }

    writeErrors(errors)
    {
        var cls = "validation-errors";
        $('.'+cls).remove();

        var columnNames = Object.keys(errors);
        for(var i = 0; i < columnNames.length; i++)
        {
            var messages = errors[columnNames[i]];
            for(var j = 0; j < messages.length; j++)
                this.addFormElementMessage(columnNames[i], 'danger', messages[j], cls)
        }
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
            var data = params.th.getElementsData();

            params.th.guiTriggerHelper.changeColumnVisibility(params.th.tableName, params.event.columnName, params.th.getElementId(params.event.columnName), data);            

            var forAllColumns = params.th.getData('gui_triggers.all');
            if(forAllColumns != null)
                for(var i = 0; i < forAllColumns.length; i++)
                {
                    if(typeof params.th.guiTriggerHelper[forAllColumns[i]] == "undefined")
                    {
                        params.th.messageHelper.toastMessage("Tetikleme fonksiyonu yok: " + forCurrentColumns[i], "error", 6000);
                        continue;
                    }
    
                    params.th.guiTriggerHelper[forAllColumns[i]](params.th.tableName, params.event.columnName, params.th.getElementId(params.event.columnName), data)
                    .then((data) => params.th.guiTriggered(params.event.columnName, data));
                }

            var forCurrentColumns = params.th.getData('gui_triggers.'+params.event.columnName);
            if(forCurrentColumns != null)
                for(var i = 0; i < forCurrentColumns.length; i++)
                {
                    if(typeof params.th.guiTriggerHelper[forCurrentColumns[i]] == "undefined")
                    {
                        params.th.messageHelper.toastMessage("Tetikleme fonksiyonu yok: " + forCurrentColumns[i], "error", 6000);
                        continue;
                    }

                    params.th.guiTriggerHelper[forCurrentColumns[i]](params.th.tableName, params.event.columnName, params.th.getElementId(params.event.columnName), data)
                    .then((data) => params.th.guiTriggered(params.event.columnName, data));
                }
        }

        return BaseHelper.doInterval('formElementChanged', func, params, 100);
    }

    getParamsForForm()
    {
        var type = null; 
        if(this.recordId == 0) type = "creates";
        else type = "edits";

        var columnSetId = 0;
        if(
            typeof BaseHelper.loggedInUserInfo.auths.tables[this.tableName] != "undefined"
            &&
            typeof BaseHelper.loggedInUserInfo.auths.tables[this.tableName][type] != "undefined"
            &&
            typeof BaseHelper.loggedInUserInfo.auths.tables[this.tableName][type][0] != "undefined"
            )

            columnSetId = BaseHelper.loggedInUserInfo.auths.tables[this.tableName][type][0];

        var params = 
        {
            column_set_id: columnSetId,
        };

        if(this.singleColumn)
            params['single_column_name'] = this.inFormColumnName;

        return params;
    }

    getLocalKey()
    {
        if(typeof BaseHelper.loggedInUserInfo == "undefined") return "";
        if(BaseHelper.loggedInUserInfo == null) return "";
        
        return "user:"+BaseHelper.loggedInUserInfo.user.id+"."+this.tableName+".form."+this.recordId;
    }

    getData(path = '')
    {
        var data = BaseHelper.readFromPipe(this.getLocalKey());
        if(data == null) return null;
        
        return DataHelper.getData(data, path);
    }

    getJson(data)
    {
        return BaseHelper.objectToJsonStr(data);
    }

    getKeys(obj)
    {
        return Object.keys(obj);
    }

    getSectionClass()
    {
        var c = "content";
        if(this.id.length > 0)
            c += " inFormElementSection";
            
        return c;
    }

    startLoading()
    {
        this.loading = true;
        this.generalHelper.startLoading();
    }

    stopLoading()
    {
        this.loading = false;
        this.generalHelper.stopLoading(); 
    }



    /****    Data Functions    ****/

    save()
    {
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+this.tableName+"/";
        if(this.recordId == 0)
            url += "store";
        else
            url += this.recordId + "/update";

        var params = null;

        if(BaseHelper.formSendMethod == "POST")
            params = this.getElementsDataForUpload(); 
        else
            params = this.getElementsData();
        
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

        this.startLoading();
        
        if(BaseHelper.formSendMethod == "POST")
            var request = this.sessionHelper.doHttpRequest("POST", url, params) 
        else
            var request = this.sessionHelper.doHttpRequest("GET", url, params) 
        
        request.then((data) => 
        {
            this.stopLoading();
            
            if(typeof data['message'] == "undefined")
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            else if(data['message'] == 'error')
                this.writeErrors(data['errors']);
            else if(data['message'] == 'success')
                this.saveSuccess(data);
            else
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
        })
        .catch((e) => { this.stopLoading(); });
    }

    saveSuccess(data)
    {
        if(!this.singleColumn)
            DataHelper.deleteDataOnPipe('list', this.tableName);

        DataHelper.deleteDataOnPipe('archive', this.tableName, this.recordId);
        DataHelper.deleteDataOnPipe('show', this.tableName, this.recordId);

        this.messageHelper.toastMessage("Kayıt başarılı", "success");

        if(this.singleColumn)
        {
            this.formSaved.emit(data);
        }
        else if(this.id.length == 0)
        {
            this.generalHelper.navigate('table/'+this.tableName);
        }    
        else
        {
            data['inFormTableName'] = this.inFormTableName;
            data['inFormColumnName'] = this.inFormColumnName;
            data['inelementId'] = this.id;
            data['inFormRecordId'] = this.inFormRecordId;
            this.formSaved.emit(data);
        }
    }

    getElementId(columnName)
    {
        var id = '[name="'+columnName+'"]';
        if(this.id.length > 0)
            id = '[ng-reflect-id="'+this.id+'"] ' + id;

        if(columnName == "name")
            id += ":last-child";
        
        return id;
    }

    getGuiTypeByColumnName(columnName)
    {
        var columnsArrays = this.getData('column_set.column_arrays');

        for(var j = 0; j < columnsArrays.length; j++)
        {
            var columnArray = columnsArrays[j];
            var columnNames = this.getKeys(columnArray.columns);
            
            for(var k = 0; k < columnNames.length; k++)
                if(columnName == columnNames[k])
                    return columnArray.columns[columnName]['gui_type_name'];
        }

        return "";
    }

    getElementsData()
    {
        var data = {};

        var columnArrays = this.getData('column_set.column_arrays')

        for(var i = 0; i < columnArrays.length; i++)
        {
            var columnArray = columnArrays[i];

            if(columnArray.column_array_type != 'direct_data') continue;

            var columnNames = this.getKeys(columnArray.columns);
            
            for(var k = 0; k < columnNames.length; k++)
            {
                var columnName = columnNames[k];
                var guiType = columnArray.columns[columnName]['gui_type_name'];

                var val = "";
                if(this.columnIsVisible(columnName))
                {
                    var temp = $(this.getElementId(columnName)).val();
                    if(typeof temp == "undefined") continue;
                    if(temp == null) temp = "";
                    
                    val = temp;
                }
                
                data[columnName] = DataHelper.changeDataForFormByGuiType(guiType, val);
            }
                
        }

        var type = null; 
        if(this.recordId == 0) type = "creates";
        else type = "edits";

        data['column_set_id'] = BaseHelper.loggedInUserInfo.auths.tables[this.tableName][type][0];

        return data;
    }
    
    columnIsVisible(columnName)
    {
        var key = "formElementVisibility." + this.id + "." + columnName;
        var temp = BaseHelper.readFromPipe(key);

        if(temp == null) return true;
        
        return temp;
    }

    getElementsDataForUpload()
    {
        var data = new FormData();

        var columnArrays = this.getData('column_set.column_arrays')

        for(var i = 0; i < columnArrays.length; i++)
        {
            var columnArray = columnArrays[i];

            if(columnArray.column_array_type != 'direct_data') continue;

            var columnNames = this.getKeys(columnArray.columns);
            
            for(var k = 0; k < columnNames.length; k++)
            {
                var columnName = columnNames[k];
                var guiType = columnArray.columns[columnName]['gui_type_name'];

                var val = "";
                if(guiType == 'files')
                {
                    if(this.columnIsVisible(columnName))
                    {
                        var files = $(this.getElementId(columnName))[0].files;
                        for(var l = 0; l < files.length; l++)
                            data.append(columnName+"[]", files[l]);
                    }
                    
                    val = $(this.getElementId(columnName+"_old")).val();
                    if(typeof val == "undefined") continue;
                    data.append(columnName+"_old", val);
                }
                else
                {
                    if(this.columnIsVisible(columnName))
                    {
                        var temp = $(this.getElementId(columnName)).val();
                        if(typeof temp == "undefined") continue;
                        if(temp == null) temp = "";
                        
                        val = temp;
                    }
                    
                    var temp = DataHelper.changeDataForFormByGuiType(guiType, val);
                    data.append(columnName, temp);
                }
            }
                
        }

        var type = null; 
        if(this.recordId == 0) type = "creates";
        else type = "edits";

        var column_set_id = BaseHelper.loggedInUserInfo.auths.tables[this.tableName][type][0];
        data.append('column_set_id', column_set_id);

        return data;
    }

    loadData()
    {
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+this.tableName+"/";
        if(this.recordId == 0)
            url += "create";
        else
            url += this.recordId + "/edit";

        var params = this.getParamsForForm();

        this.loading = true;
        this.generalHelper.startLoading();

        this.sessionHelper.doHttpRequest("GET", url, {'params': BaseHelper.objectToJsonStr(params)})
        .then((data) => 
        {
            BaseHelper.writeToPipe(this.getLocalKey(), data);
            setTimeout(() => {
                this.changeColumnVisibilityGuiTrigger();
                this.formLoad.emit(data);
            }, 100);
            
         
            this.loading = false;
            this.generalHelper.stopLoading();
            this.addEventForFeatures();
        })
        .catch((e) => 
        { 
            this.loading = false;
            this.generalHelper.stopLoading(); 
        });
    }

    changeColumnVisibilityGuiTrigger()
    {
        var data = this.getElementsData();
        var columnNames = Object.keys(data);
        for(var i = 0; i < columnNames.length; i++)
            this.guiTriggerHelper.changeColumnVisibility(this.tableName, columnNames[i], this.getElementId(columnNames[i]), data);
    }
     


    /****    Events Functions    ****/

    inFormload(data)
    {
        this.inFormOpened.emit(data);
    }

    inFormSavedSuccess(data)
    {
        var elementId = this.getElementId(data['inFormColumnName']);
        
        var guiType = this.getGuiTypeByColumnName(data['inFormColumnName']);
        switch (guiType) 
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

        var data = BaseHelper.readFromPipe(this.getLocalKey());
        var control = false;
        if(typeof data['record'] == "undefined")
        {
            data['record'] = {};
            data['record'][columnName] = [];
        }
        else
        {
            var control = false;
            for(var i = 0; i < data['record'][columnName].length; i++)
                if(data['record'][columnName][i]['source'] == inFormData['source'])
                {
                    data['record'][columnName][i]['display'] = inFormData['display'];
                    control = true;
                }
        }

        if(!control)
            data['record'][columnName].push(
            {
                source: inFormData['source'], 
                display: inFormData['display']
            });
        
        BaseHelper.writeToPipe(this.getLocalKey(), data);
    }

    inFormSavedSuccessMultiSelect(elementId, data)
    {
        var val = $(elementId).val();
        
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

    guiTriggered(columnName, data = null)
    {
        var cls = columnName+"-message";
        
        var temp = this.getElementId(cls);
        temp = temp.replace('#', '.');
        $(temp).remove();
        
        if(data == null) return;

        var types = Object.keys(data);
        for(var i = 0; i < types.length; i++)
            this.addFormElementMessage(columnName, types[i], data[types[i]], cls);
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
