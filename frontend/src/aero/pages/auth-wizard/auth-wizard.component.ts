import { ActivatedRoute} from '@angular/router';
import { Component } from '@angular/core';

import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { MessageHelper } from './../helpers/message';
import { AeroThemeHelper } from './../helpers/aero.theme';

import {CdkDragDrop, moveItemInArray, transferArrayItem} from '@angular/cdk/drag-drop';

@Component(
{
    selector: 'auth-wizard',
    styleUrls: ['./auth-wizard.component.scss'],
    templateUrl: './auth-wizard.component.html',
})
export class AuthWizardComponent 
{
    public tableName:string = "";
    public types = [
        {
            source: 'list',
            display: 'Liste',
        },
        {
            source: 'show',
            display: 'Bilgi Kartı',
        },
        {
            source: 'create',
            display: 'Ekleme',
        },
        {
            source: 'update',
            display: 'Güncelleme',
        },
        {
            source: 'querie',
            display: 'Sorgu',
        },
        {
            source: 'deleted',
            display: 'Silinmiş Kayıtlar',
        }
    ]
    public auths = [];

    constructor(
        private route: ActivatedRoute,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper,
        private messageHelper: MessageHelper
        )
    {
        this.auths['search'] = [];
        for(var i = 0; i < this.types.length; i++)
            this.auths[this.types[i]['source']] = [];

        var th = this;
        setTimeout(() => {
            route.params.subscribe(val => 
            {
                th.tableName = val.tableName;
                this.searchWithWord(th.tableName);
            });
        }, 100);
    }

    drop(event: CdkDragDrop<string[]>, target) 
    {
        var source = event.previousContainer.data[event.previousIndex].source;
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