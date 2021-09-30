import { ActivatedRoute} from '@angular/router';
import { Component } from '@angular/core';

import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { MessageHelper } from './../helpers/message';
import { AeroThemeHelper } from './../helpers/aero.theme';

import {CdkDragDrop, moveItemInArray, transferArrayItem} from '@angular/cdk/drag-drop';

declare var $: any; 

@Component(
{
    selector: 'data-entegrator',
    styleUrls: ['./data-entegrator.component.scss'],
    templateUrl: './data-entegrator.component.html',
})
export class DataEntegratorComponent 
{
    private tableName:string = "";
    private tableId:number = 0;
    private dataSourceId:number = 0;

    public remoteColumns = [];
    public columnChanges = {};
    public columns = [];

    private loading = false;
    
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
        BaseHelper.getScript('assets/ext_modules/select2/select2.min.js');

        var th = this;
        setTimeout(() => {
            route.params.subscribe(val => 
            {
                th.tableName = val.tableName;
                th.tableId = val.tableId;

                th.fillColumns();
                
                this.aeroThemeHelper.pageRutine();
            });
        }, 100);
    }

    remoteTableChanged(event)
    {
        this.fillRemoteColumns(event.target.value);
    }

    isElementSelected(elementName)
    {
        var val = $("[name='"+elementName+"']").val();
        
        if(typeof val == "undefined") return false;
        if(val == "") return false;

        return true;
    }

    isDataEntegratable()
    {
        if(this.loading) return false;

        if(!this.isElementSelected('data_source_id')) return false;
        if(!this.isElementSelected('data_source_direction_id')) return false;
        if(!this.isElementSelected('data_source_rmt_table_id')) return false;
        
        return true;
    }

    cloneColumn(column)
    {
        for(var i = 0; i < this.columns.length; i++)
            if(column['source'] == this.columns[i]['source'])
            {
                var clone = BaseHelper.getCloneFromObject(column);
                clone['source'] = clone['source'] + '_klon';

                var temp = this.columns.splice(i+1);
                this.columns = this.columns.concat(clone);
                this.columns = this.columns.concat(temp);

                setTimeout(() => {
                    this.addSelect2();
                }, 100);

                return;
            }
    }

    fillColumns()
    {
        var url = this.sessionHelper.getBackendUrlWithToken();
        if(url.length == 0) return;
        
        url += "tables/"+this.tableName+"/create";

        this.sessionHelper.doHttpRequest("POST", url, {'params': BaseHelper.objectToJsonStr({column_set_id: 0})})
        .then((data) => 
        {
            var keys = Object.keys(data['column_set']['column_arrays'][0]['columns']);

            // Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
            this.columns.push({
                id: 6,
                source: 'id',
                display: 'Kayıt No',
                gui_type_name: 'numeric'
            });

            this.columns.push({
                id: 14,
                source: 'updated_at',
                display: 'Güncellenme Zamanı (Zorunlu)',
                gui_type_name: 'datetime'
            });

            for (var i = 0; i < keys.length; i++)
                this.columns.push({
                    id: data['column_set']['column_arrays'][0]['columns'][keys[i]]['id'],
                    source: keys[i],
                    display: data['column_set']['column_arrays'][0]['columns'][keys[i]]['display_name'],
                    gui_type_name: data['column_set']['column_arrays'][0]['columns'][keys[i]]['gui_type_name']
                });
            
            // Tablo yada kolon eklenme sırası değişirse güncellenmesi gerekir
            this.columns.push({
                id: 11,
                source: 'own_id',
                display: 'Kaydın Sahibi',
                gui_type_name: 'select'
            });

            this.columns.push({
                id: 12,
                source: 'user_id',
                display: 'Kaydı Güncelleyen',
                gui_type_name: 'select'
            });

            this.columns.push({
                id: 13,
                source: 'created_at',
                display: 'Oluşturulma Zamanı',
                gui_type_name: 'datatime'
            });

            this.addSelect2()
        });
    }

    fillRemoteColumns(remoteTableId)
    {
        var params = 
        {
            page: 1, 
            limit: 500, 
            column_array_id: "0", 
            column_array_id_query: "0", 
            sorts: {},
            filters: 
            {
                data_source_rmt_table_id: 
                {
                    type: 1,
                    guiType: "multiselect",
                    filter: [remoteTableId]
                }
            }
        };

        var url = this.sessionHelper.getBackendUrlWithToken();
        if(url.length == 0) return;
        
        url += "tables/data_source_remote_columns";

        this.sessionHelper.doHttpRequest("POST", url, {'params': BaseHelper.objectToJsonStr(params)})
        .then((data) => 
        {
            for(var i = 0; i < data['records'].length; i++)
                this.remoteColumns.push({
                    id: data['records'][i]['id'],
                    name: data['records'][i]['name_basic'],
                    type: data['records'][i]['db_type_name'],
                });

            this.addSelect2();
        });
    }
    
    addSelect2()
    {
        setTimeout(async () => {
            await BaseHelper.waitForOperation(() => $('test').select2() );
            $('.remoteColumns').select2();
        }, 100);
    }

    autoSelectColumns()
    {
        for(var i = 0; i < this.columns.length; i++)
            for(var j = 0; j < this.remoteColumns.length; j++)
                if(this.columns[i]['source'] == this.remoteColumns[j]['name'])
                {
                    if(this.columns[i]['source'] == 'id') continue;

                    $('#remote_'+this.columns[i]['source']).val(this.remoteColumns[j]['id']);

                    var display = this.remoteColumns[j]['name']+" "+this.remoteColumns[j]['type'];
                    $('#select2-remote_'+this.columns[i]['source']+'-container').html(display);
                }
    }

    removeRelation(column)
    {
        if(this.columnHasChanger(column))
            delete this.columnChanges[column['source']];

        $('#remote_'+column['source']).val("");
        $('#select2-remote_'+column['source']+'-container').html("İlişki yok");
    }

    getColumnTrStyle(column)
    {
        var style = {};

        if(this.columnHasChanger(column)) style['height'] = '140px';

        if(!this.columnHasValue(column)) return style;

        style['background-color'] = 'rgba(84, 208, 137, 0.3)';
        return style;
    }

    columnHasValue(column)
    {
        if(typeof column['source'] == "undefined") return false;

        var val = $('#remote_'+column['source']).val();
        
        if(typeof val == "undefined") return false;

        return val != "";
    }

    columnHasChanger(column)
    {
        if(typeof column['source'] == "undefined") return false;

        return (typeof this.columnChanges[column['source']] != "undefined");
    }

    addDataChanger(column)
    {
        this.columnChanges[column['source']] = true;
    }

    async addRemoteColumn(columnFormData)
    {
        var url = this.sessionHelper.getBackendUrlWithToken();
        if(url.length == 0) return;
        
        url += "tables/data_source_col_relations/store";
        
        var id = 0;
        await this.sessionHelper.doHttpRequest("POST", url, columnFormData) 
        .then(async (data) => 
        {            
            if(typeof data['message'] == "undefined")
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            else if(data['message'] == 'error')
                this.messageHelper.sweetAlert("Bir hata oluştu:" + BaseHelper.objectToJsonStr(data['errors']), "Hata", "warning");
            else if(data['message'] == 'success')
                id = data['in_form_data']['source'];
            else
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
                
            await BaseHelper.sleep(500);
        })
        
        return id;
    }

    formValidation()
    {
        var control = this.columnHasValue({source: 'updated_at'});
        if(!control)
        {
            this.messageHelper.toastMessage("Güncellenme Zamanı kolonu boş geçilemez!");
            return false;
        }

        return true;
    }

    async addRemoteColumnRelations()
    {
        if(!this.formValidation()) return [];

        var columns = [];
        for(var i = 0; i < this.columns.length; i++)
        {
            if(!this.columnHasValue(this.columns[i])) continue;

            var php_code = $('#'+this.columns[i]['source']+'_changer').val();
            if(typeof php_code == "undefined") php_code = "";
            else if(php_code == "null") php_code = "";
            else php_code = BaseHelper.replaceAll(php_code, '+', '%2B');

            var col = 
            {
                column_id: this.columns[i]['id'],
                data_source_remote_column_id: $('#remote_'+this.columns[i]['source']).val(),
                php_code: php_code,
                state: 1,
                column_set_id: 0,
                in_form_column_name: "data_source_col_relation_ids",
            };

            col['id'] = await this.addRemoteColumn(col);

            if(col['id'] == 0)
            {
                this.messageHelper.toastMessage("Bir sorun oluştu", "danger");
                return [];
            }

            columns.push(col);
        }

        return columns;
    }

    addRemoteTableRelation(columns)
    {
        var columnIds = [];
        for(var i = 0; i < columns.length; i++)
            columnIds.push(columns[i]['id']);

        var table = 
        {
            data_source_id: $('#data_source_id').val(),
            table_id: this.tableId,
            data_source_rmt_table_id: $('#data_source_rmt_table_id').val(),
            data_source_direction_id: $('#data_source_direction_id').val(),
            cron: $('#cron').val(),
            data_source_col_relation_ids: BaseHelper.objectToJsonStr(columnIds),
            state: 1,            
            column_set_id: 0
        }

        var url = this.sessionHelper.getBackendUrlWithToken();
        if(url.length == 0) return;
        
        url += "tables/data_source_tbl_relations/store";
        
        this.sessionHelper.doHttpRequest("POST", url, table) 
        .then((data) => 
        {         
            if(typeof data['message'] == "undefined")
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            else if(data['message'] == 'error')
                this.messageHelper.sweetAlert("Bir hata oluştu:" + BaseHelper.objectToJsonStr(data['errors']), "Hata", "warning");
            else if(data['message'] == 'success')
                this.messageHelper.sweetAlert("Veri aktarma görevi başarı ile oluşturuldu!", "Başarı", "success");
            else
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
        });
    }

    async save()
    {
        if(!this.isDataEntegratable()) return;

        this.startLoading();

        var columns = await this.addRemoteColumnRelations()
        if(columns.length == 0) 
        {
            this.stopLoading();
            return;
        }

        this.addRemoteTableRelation(columns);
        this.stopLoading();
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
}