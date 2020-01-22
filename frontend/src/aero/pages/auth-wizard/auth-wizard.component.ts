import { ActivatedRoute} from '@angular/router';
import { Component } from '@angular/core';

import { types as typeList } from './types';

import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { MessageHelper } from './../helpers/message';
import { AeroThemeHelper } from './../helpers/aero.theme';

import {CdkDragDrop, moveItemInArray, transferArrayItem} from '@angular/cdk/drag-drop';

declare var $: any; 

@Component(
{
    selector: 'auth-wizard',
    styleUrls: ['./auth-wizard.component.scss'],
    templateUrl: './auth-wizard.component.html',
})
export class AuthWizardComponent 
{
    public tableName:string = "";
    public tableId:number = 0;
    public auths = [];

    public inFormColumnName = "";
    public inFormTableName = "";
    public inFormRecordId = 0;
    public inFormElementId = "";
    public inFormType = {};
    public currentListIndex = 0;

    public types = [];

    constructor(
        private route: ActivatedRoute,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper,
        private messageHelper: MessageHelper
        )
    {
        this.types = typeList;

        this.auths['search'] = [];
        for(var i = 0; i < this.types.length; i++)
            this.auths[this.types[i]['source']] = [];

        var th = this;
        setTimeout(() => {
            route.params.subscribe(val => 
            {
                th.tableName = val.tableName;
                th.tableId = val.tableId;
                //this.searchWithWord(th.tableName);
            });

            th.showCurrentList();
        }, 100);
    }

    prevList()
    {
        this.currentListIndex--;
        if(this.currentListIndex < 0)
            this.currentListIndex = this.types.length -1;

        this.showCurrentList();
    }

    nextList()
    {
        this.currentListIndex++;
        if(this.currentListIndex >= this.types.length)
            this.currentListIndex = 0;

        this.showCurrentList();
    }

    showCurrentList()
    {
        $('.auth-list').css('display', 'none');        
        $('#list'+this.currentListIndex).css('display', 'unset');

        this.searchAuth(this.types[this.currentListIndex]['search']);
    }

    drop(event: CdkDragDrop<string[]>, target) 
    {
        var source = event.previousContainer.data[event.previousIndex]['source'];
        if(target != 'search' && source.indexOf(this.tableName+":"+target) == -1)
        {
            this.messageHelper.toastMessage("Bu gruba ekleyemezsiniz! ("+target+")");
            return;
        }
        
        if (event.previousContainer === event.container) 
            moveItemInArray(event.container.data, event.previousIndex, event.currentIndex);
        else 
            transferArrayItem(event.previousContainer.data,
                                event.container.data,
                                event.previousIndex,
                                event.currentIndex);
    }

    createAuth(type)
    {
        this.inFormType = type;
        this.inFormTableName = type['table'];
        this.inFormColumnName = type['in_form_column'];

        this.inFormRecordId = 0;

        var rand = Math.floor(Math.random() * 10000) + 1;
        this.inFormElementId = "ife-"+rand;
        
        setTimeout(() => 
        {
            var modalId = '#'+this.inFormElementId+'inFormModal';
            $(modalId).modal('show');
        }, 100);
    }

    formChanged(event)
    {
        this.setElementDefaultDataAndHide(this.inFormElementId);
    }

    formLoad(event)
    {
        this.setElementDefaultDataAndHide(this.inFormElementId);       
    }

    setElementDefaultDataAndHide(inFormElementId)
    {
        var modalId = '#'+inFormElementId+'inFormModal';
        var tableIdElement = modalId+' [name="table_id"]';
        var nameElement = modalId+' [name="name_basic"]';
        var columnArrayTypeElement = modalId+' [name="column_array_type_id"]';

        $(tableIdElement).append("<option value='"+this.tableId+"'></option>")
        $(tableIdElement).val(this.tableId);
        $(modalId+' #table_id-group').css('display', 'none');

        $(columnArrayTypeElement).append("<option value='1'></option>")
        $(columnArrayTypeElement).val("1");
        $(modalId+' #column_array_type_id-group').css('display', 'none');

        var val = $("input[name='name']").val();
        $(nameElement).val(val+" ("+this.inFormType['display']+")");



        var filterTypeIds = {
            'restore': 4,
            'delete': 3,
            'export': 6
        }

        var filterTypeElement = modalId+' [name="data_filter_type_id"]';
        var filterId = filterTypeIds[this.inFormType['source']];

        $(filterTypeElement).append("<option value='"+filterId+"'></option>")
        $(filterTypeElement).val(filterId);
        $(modalId+' #data_filter_type_id-group').css('display', 'none');
        
        console.log(filterTypeElement);
    }

    getAuthItemForList(event)
    {
        var item;

        if(this.inFormType['search'].indexOf('filters:') > -1)
        {
            item = 
            {
                source: 'filters:'+this.tableName+':'+this.inFormType['source']+':'+event.in_form_data['source'],
                display: ' Tablolar '+this.tableName+' '+this.inFormType['display']+' Filtresi '+event.in_form_data['display']+' (id: '+event.in_form_data['source']+')'
            };
        }
        else
        {
            item = 
            {
                source: 'tables:'+this.tableName+':'+this.inFormType['source']+'s:'+event.in_form_data['source'],
                display: ' Tablolar '+this.tableName+' '+this.inFormType['display']+' '+event.in_form_data['display']+' (id: '+event.in_form_data['source']+')'
            };
        }

        return item;
    }

    inFormSavedSuccess(event)
    {
        $('#'+this.inFormElementId+'inFormModal').modal('hide');

        var item = this.getAuthItemForList(event);
        this.auths[this.inFormType['source']].push(item);
    }

    inFormOpened(data)
    {
        this.setElementDefaultDataAndHide(this.inFormElementId);
        this.setElementDefaultDataAndHide(data['ife']);
    }

    closeModal(id)
    {
        BaseHelper.closeModal(id);
    }

    searchAuth(search)
    {
        search = search.replace('tableName:', this.tableName+":");
        this.searchWithWord(search)
    }

    searchWithWord(word)
    {
        $('#auth_search').val(word)
        this.fillListElements(word);
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

    fillListElements(search)
    {   
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/auth_groups/getSelectColumnData/auths";
        var params = this.getParamsForSearch(search);
        
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

        this.auths['search'] = [];
        var control = false;
        for(var i = 0; i < list.length; i++)
        {
            var item = list[i];

            if(this.itemIsSelected(item['id']))
            {
                control = true;
                continue;
            } 

            this.auths['search'].push({source: item['id'], display: item['text']});
        }

        if(control) this.messageHelper.toastMessage("Bulunan bazı sonuçlar zaten listenizde", "info");
    }

    itemIsSelected(source)
    {
        var keys = Object.keys(this.auths);
        for(var i = 0; i < keys.length; i++)
        {
            if(keys[i] == 'search') continue;

            for(var j = 0; j < this.auths[keys[i]].length; j++)
                if(this.auths[keys[i]][j]['source'] == source) 
                    return true;
        }   

        return false;
    }
}