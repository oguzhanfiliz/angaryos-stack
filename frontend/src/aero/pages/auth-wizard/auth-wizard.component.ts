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

    public loading = false;

    public lastSelectedFilterType = "";

    public types = [];

    constructor(
        private route: ActivatedRoute,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper,
        private messageHelper: MessageHelper
        )
    {
        this.aeroThemeHelper.addEventForFeature("standartElementEvents"); 
        this.aeroThemeHelper.addEventForFeature("layoutCommonEvents"); 

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

        var want = this.tableName+":"+target;
        if(target == 'filters')
            want = 'filters:'+this.tableName;

        if(target != 'other' && target != 'search' && source.indexOf(want) == -1)
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

    getValidatedParamsForAuthSave()
    {
        var nameBasic = $('[name="name_basic"]').val();
        if(nameBasic == "")
        {
            this.messageHelper.toastMessage("Ad boş geçilemez!");
            return false;
        }

        var description = $('textarea[name="description"]').val()

        var params = 
        {
            description: description,
            name_basic: nameBasic,
            state: 1,
            in_form_column_name: 'auths',
            column_set_id: BaseHelper.loggedInUserInfo.auths.tables['auth_groups']['creates'][0]
        }

        var temp = [];
        for(var i = 0; i < this.types.length; i++)
        {
            var t = this.auths[this.types[i]['source']];
            for(var j = 0; j < t.length; j++)
                temp.push(t[j]['source']);
        }
        
        params['auths'] = BaseHelper.objectToJsonStr(temp);

        return params;
    }

    save()
    {
        var params = this.getValidatedParamsForAuthSave();
        if(params == false) return;

        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/auth_groups/store";
        
        this.startLoading();
        
        //this.sessionHelper.doHttpRequest("POST", url, params) 
        this.sessionHelper.doHttpRequest("GET", url, params) 
        .then((data) => 
        {
            this.stopLoading();
            
            if(typeof data['message'] == "undefined")
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            else if(data['message'] == 'error')
                this.writeErrors(data['errors']);
            else if(data['message'] == 'success')
                this.authSaveSuccess(data);
            else
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
        })
        .catch((e) => { this.stopLoading(); });
    }

    getJsonStrFromMultiSelectElement(elementName)
    {
        var temp = $('[name="'+elementName+'"]').val();
        var ids = [];
        for(var i = 0; i < temp.length; i++)
            if(temp[i].length > 0)
                ids.push(temp[i]);

        return BaseHelper.objectToJsonStr(ids);
    }

    authSaveSuccess(data)
    {
        var params = this.getValidatedParamsForAuthAssign(data['in_form_data']);

        var url = this.sessionHelper.getBackendUrlWithToken()+"assignAuth";
        
        this.startLoading();
        
        //this.sessionHelper.doHttpRequest("POST", url, params) 
        this.sessionHelper.doHttpRequest("GET", url, params) 
        .then((data) => 
        {
            this.stopLoading();
            
            if(typeof data['message'] == "undefined")
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            else if(data['message'] == 'error')
                this.writeErrors(data['errors']);
            else if(data['message'] == 'success')
                this.messageHelper.sweetAlert("Yetkiler başarı ile atandı!", "Başarılı", "success"); 
            else
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
        })
        .catch((e) => { this.stopLoading(); });
    }

    isAllUserChecked()
    {
        return $('[name="all_user"]').prop('checked');
    }

    getValidatedParamsForAuthAssign(data)
    {
        var userIds = this.getJsonStrFromMultiSelectElement('user_id');
        var departmentIds = this.getJsonStrFromMultiSelectElement('department_id');
        var auths = this.getJsonStrFromMultiSelectElement('auths');

        return {
            auth_id: data['source'],
            all_user: ($('[name="all_user"]').prop('checked') ? '1':'0'),
            user_ids: userIds,
            department_ids: departmentIds,
            auths: auths
        }
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
        
        this.setColumnsElementDefaultDataAndHide(modalId);
        this.setFilterssElementDefaultDataAndHide(modalId);
    }

    setFilterssElementDefaultDataAndHide(modalId)
    {
        var filterTypeIds = {
            'list': 1,
            'update': 2,
            'delete': 3,
            'restore': 4,
            'show': 5,
            'export': 6,

            'filters': 0
        }

        var filterTypeElement = modalId+' [name="data_filter_type_id"]';
        var filterId = filterTypeIds[this.inFormType['source']];

        if(filterId == 0)
        {
            $(filterTypeElement).change(() => 
            {
                var filterTypeId = $(filterTypeElement).val();

                var keys = Object.keys(filterTypeIds);
                for (var i = 0; i < keys.length; i++) 
                    if(filterTypeIds[keys[i]] == filterTypeId)
                    {
                        this.lastSelectedFilterType = keys[i];
                        break;
                    }
            });
        }
        else
        {
            $(filterTypeElement).append("<option value='"+filterId+"'></option>")
            $(filterTypeElement).val(filterId);
            $(modalId+' #data_filter_type_id-group').css('display', 'none');
        }
    }

    setColumnsElementDefaultDataAndHide(modalId)
    {
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
    }

    getAuthItemForList(event)
    {
        if(this.inFormType['source'] == 'filters')
            return {
                source: 'filters:'+this.tableName+':'+this.lastSelectedFilterType+':'+event.in_form_data['source'],
                display: ' Tablolar '+this.tableName+' '+this.lastSelectedFilterType+' Filtresi '+event.in_form_data['display']+' (id: '+event.in_form_data['source']+')'
            };
        else if(this.inFormType['search'].indexOf('filters:') > -1)
            return {
                source: 'filters:'+this.tableName+':'+this.inFormType['source']+':'+event.in_form_data['source'],
                display: ' Tablolar '+this.tableName+' '+this.inFormType['display']+' Filtresi '+event.in_form_data['display']+' (id: '+event.in_form_data['source']+')'
            };
        else
            return {
                source: 'tables:'+this.tableName+':'+this.inFormType['source']+'s:'+event.in_form_data['source'],
                display: ' Tablolar '+this.tableName+' '+this.inFormType['display']+' '+event.in_form_data['display']+' (id: '+event.in_form_data['source']+')'
            };
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
        for(var i = 0; i < list.length; i++)
        {
            var item = list[i];

            if(this.itemIsSelected(item['id']))
                continue;

            this.auths['search'].push({source: item['id'], display: item['text']});
        }
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