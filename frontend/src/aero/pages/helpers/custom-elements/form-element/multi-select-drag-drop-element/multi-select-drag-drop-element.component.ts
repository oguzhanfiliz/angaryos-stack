import { Component, EventEmitter, Input, Output } from '@angular/core';

import {CdkDragDrop, moveItemInArray, transferArrayItem} from '@angular/cdk/drag-drop';

import { BaseHelper } from './../../../base';
import { DataHelper } from './../../../data';
import { SessionHelper } from './../../../session';
import { GeneralHelper } from './../../../general';
import { MessageHelper } from './../../../message';

declare var $: any;

@Component(
{
    selector: 'multi-select-drag-drop-element',
    styleUrls: ['./multi-select-drag-drop-element.component.scss'],
    templateUrl: './multi-select-drag-drop-element.component.html'
})
export class MultiSelectDragDropElementComponent
{
    @Input() defaultData: string;
    @Input() recordJson: string; 
    @Input() baseUrl: string;
    @Input() type: string;
    @Input() value: string;
    @Input() valueJson: string = "";
    @Input() class: string;
    @Input() name: string;
    @Input() columnName: string;
    @Input() placeholder: string;
    @Input() showFilterTypesSelect: boolean;
    @Input() filterType: string;
    @Input() upColumnName: string;
    @Input() upFormId: string = "";
    @Input() createForm: boolean = false;
    
    staticElement = false;
    record = null;

    @Output() changed = new EventEmitter();

    //intervalId = -1;
    baseElementSelector = "";
    val = [];
    list = [];
    deleted = [];

    constructor(
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private messageHelper: MessageHelper
    ) 
    {
        setTimeout(() => 
        {
            if(this.upFormId.length > 0)
                this.baseElementSelector = '[ng-reflect-id="'+this.upFormId+'"] ';
                
            if(this.type == "multiselectdragdrop:static") this.staticElement = true;

            var elementId = this.baseElementSelector + ' .selected-list .dragdrop-box';
            
            $(document).on('click', elementId, function(e) 
            {
                $(elementId)
                .each((i, opt) => $(opt).removeClass('selected-option'));

                $(e.target).addClass('selected-option');
            });
            
            if(this.staticElement) this.fillListElements("***");

        }, 100);
    }



    /****    Events     *****/

    ngOnChanges()
    {
        if(typeof this.recordJson != "undefined" && this.recordJson != "")
            this.record = BaseHelper.jsonStrToObject(this.recordJson);
            
        if(this.createForm || this.valueJson.length > 0)
            this.fillSelectedElements();

        this.selectedChanged();
    }

    searchChanged(event)
    {
        var params =
        {
            event: event,
            th: this
        };

        function func(params)
        {
            if(params.event.target.value.length == 0) return;

            params.th.fillListElements(params.event.target.value);
        }

        return BaseHelper.doInterval('multiSelectDragDropElementSearched', func, params, 1000);
    }

    drop(event: CdkDragDrop<string[]>) 
    {
        if (event.previousContainer === event.container)
            moveItemInArray(event.container.data, event.previousIndex, event.currentIndex);
        else 
        {
            if(event.container.id == "cdk-drop-list-0")
                this.addToDeletedList(event.previousContainer.data[event.previousIndex]);
            else
                this.removeFromDeletedList(event.previousContainer.data[event.previousIndex]);
            
            transferArrayItem(event.previousContainer.data,
                                event.container.data,
                                event.previousIndex,
                                event.currentIndex);
        }

        this.selectedChanged();
    }
    
    addToDeletedList(item)
    {
        for(var i = 0; i < this.deleted.length; i++)
            if(this.deleted[i]['source'] == item['source'])
                return;
                
        this.deleted.push(item);
    }
    
    removeFromDeletedList(item)
    {
        for(var i = 0; i < this.deleted.length; i++)
            if(this.deleted[i]['source'] == item['source'])
            {
                this.deleted.splice(i, 1);
                return;
            }
    }

    selectedChanged()
    {
        var selected = [];
        for(var i = 0; i < this.val.length; i++)
            selected.push(this.val[i]['source']);

        this.addSelectElementValue(selected);
    }

    addSelectElementValue(selected)
    {
        setTimeout(() => 
        {
            $(this.baseElementSelector+' #'+this.columnName).html("");

            for(var i = 0; i < selected.length; i++)
                $(this.baseElementSelector+' #'+this.columnName).append("<option value='"+selected[i]+"'></option>");
        
            $(this.baseElementSelector+' #'+this.columnName).val(selected);
        }, 100);
    }



    /****   Data Functions  *****/    

    fillSelectedElements()
    {
        var temp = null;
        if(this.createForm && this.valueJson == "")
        {
            if(this.defaultData == null || this.defaultData == "") return;

            temp = BaseHelper.jsonStrToObject(this.defaultData);
        }
        else if(this.valueJson != null && this.valueJson != "")
            temp = BaseHelper.jsonStrToObject(this.valueJson);
                
        for(var i = 0; i < temp.length; i++)
        {
            var control = false;
            for(var j = 0; j < this.val.length; j++)
                if(this.val[j]['source'] == temp[i]['source'])
                {
                    this.val[j]['display'] = temp[i]['display'];
                    control = true;
                    break;
                }

            if(control) continue;

            this.val.push({source: temp[i]['source'], display: temp[i]['display']});
        }
    }

    fillListElements(search)
    {   
        var url = this.sessionHelper.getBackendUrlWithToken()+this.baseUrl+"/getSelectColumnData/"+this.columnName;
        var params = this.getParamsForSearch(search);
        
        if(!this.createForm) params['editRecordId'] = this.record['id'];
        
        this.generalHelper.startLoading();

        $.ajax(
        {
            url : url,
            type : "GET",
            data : params,
            success : (data) =>
            {
                this.generalHelper.stopLoading();
                this.searchSuccess(data);
            },
            error : (e) =>
            {
                this.generalHelper.stopLoading();
                this.messageHelper.toastMessage("Bir hata oluştu", "warning");
            }
        });
    }

    getParamsForSearch(search)
    {
        var params =
        {
            search: search,
            page: 1,
            limit: 500,
        }
        
        if(this.upColumnName.length > 0)
        {   
            params['upColumnName'] = this.upColumnName;
            params['upColumnData'] = $(this.baseElementSelector+' #'+this.upColumnName).val();
            
            var temp = BaseHelper.getAllFormsData(this.baseElementSelector);
            params['currentFormData'] = BaseHelper.objectToJsonStr(temp);
        }

        return params;
    }

    searchSuccess(data)
    {
        var list = data['results'];
        if(list.length == 0) 
        {
            this.messageHelper.toastMessage("Sonuç bulunamadı", "info");
            return;
        }

        if(list[0]['id'] == -9999) 
        {
            this.messageHelper.toastMessage(list[0]['text'], "warning");
            return;
        }

        this.list = [];
        var control = false;
        for(var i = 0; i < list.length; i++)
        {
            var item = list[i];

            if(this.itemIsSelected(item['id']))
            {
                control = true;
                continue;
            } 

            this.list.push({source: item['id'], display: item['text']});
        }

        if(control) this.messageHelper.toastMessage("Bulunan bazı sonuçlar zaten listenizde", "info");
    }

    itemIsSelected(source)
    {
        for(var i = 0; i < this.val.length; i++)
            if(this.val[i]['source'] == source) return true;

        return false;
    }

    selectAll()
    {
        for(var i = 0; i < this.list.length; i++)
            if(!this.itemIsSelected(this.list[i]['source']))
                this.val.push(this.list[i]);
            
        this.list = [];
        this.selectedChanged();
    }

    unSelectAll()
    {
        for(var i = 0; i < this.val.length; i++)
            this.list.push(this.val[i]);
            
        this.val = [];
        this.selectedChanged();
    }
}