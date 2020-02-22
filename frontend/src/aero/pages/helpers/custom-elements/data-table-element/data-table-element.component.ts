import { ActivatedRoute} from '@angular/router';
import { Component, EventEmitter, Input, Output } from '@angular/core';

import { DomSanitizer, SafeHtml } from '@angular/platform-browser';

import {CdkDragDrop, moveItemInArray} from '@angular/cdk/drag-drop';

import { BaseHelper } from './../../base';
import { DataHelper } from './../../data';
import { MessageHelper } from './../../message';
import { SessionHelper } from './../../session';
import { GeneralHelper } from './../../general';
import { AeroThemeHelper } from './../../aero.theme';

declare var $: any;

@Component(
{
    selector: 'data-table-element', 
    styleUrls: ['./data-table-element.component.scss'],
    templateUrl: './data-table-element.component.html'
})
export class DataTableElementComponent
{
    @Input() baseUrl: string;
    @Input() tableName: string = "";
    @Input() defaultLimit: number = 10;
    @Input() lightTable: boolean = false;
    @Input() archiveTable: boolean = false;
    
    @Output() dataChanged = new EventEmitter();

    showEditButton = {};
    selectedFilter = {};
    selectedRecord = null;
    selectedRecordList = [];
    //loadDataIntervalId = -1;
    loadDataTimeout = 2000;
    //editData = {};

    inFormColumnName = "";
    inFormTableName = "";
    inFormRecordId = 0;
    inFormElementId = "";

    params = null;

    constructor(
        public route: ActivatedRoute,
        public messageHelper: MessageHelper,
        public sessionHelper: SessionHelper,
        public generalHelper: GeneralHelper,
        public aeroThemeHelper: AeroThemeHelper,
        private sanitizer:DomSanitizer
    ) 
    {
        this.params = this.getDefaultParams();

        var th = this;
        this.route.params.subscribe(val => 
        {
            setTimeout(() => 
            {
                th.fillParamsFromLocal();

                th.loadData();  
                th.addEventForThemeIcons();
            }, 50);
        });
    }



    /****    Operation Functions    *****/

    can(policyType, record)
    {
        var columnName = '';
        switch(policyType)
        {
            case 'edit': columnName = '_is_editable'; break;
            case 'delete': columnName = '_is_deletable'; break;
            case 'archive': columnName = '_is_restorable'; break;
            case 'export': columnName = '_is_exportable'; break;
            case 'show': columnName = '_is_showable'; break;
            case 'create':
                if(typeof BaseHelper.loggedInUserInfo.auths.tables[this.tableName]['creates'] == "undefined") return false;
                return BaseHelper.loggedInUserInfo.auths.tables[this.tableName]['creates'].length > 0
                break;
            case 'deleted':
                if(typeof BaseHelper.loggedInUserInfo.auths.tables[this.tableName]['deleteds'] == "undefined") return false;
                return BaseHelper.loggedInUserInfo.auths.tables[this.tableName]['deleteds'].length > 0
                break;
            case 'clone': columnName = '_is_showable'; break;
            case 'userImitation': return this.canUserImitation(record);
            case 'authWizard': return this.canAuthWizard(record);
            case 'dataEntegrator': return this.canDataEntegrator(record);
            default: alert(policyType + ': not have can function'); return true;
        }

        if(typeof record[columnName] == "undefined" || record[columnName]) return true;
        
        return false;
    }

    canDataEntegrator(record)
    {
        if(this.tableName != 'tables') return false;

        return this.canAdminAuth('dataEntegrator'); 
    }

    canAuthWizard(table)
    {
        if(this.tableName != 'tables') return false;

        return this.canAdminAuth('authWizard'); 
    }

    canUserImitation(user)
    {
        if(this.tableName != 'users') return false;

        if(BaseHelper['loggedInUserInfo']['user']['id'] == user['id']) return false;

        return this.canAdminAuth('userImitation');
    }

    canAdminAuth(auth)
    {
        if(typeof BaseHelper['loggedInUserInfo']['auths']['admin'] == 'undefined') return false;
        if(typeof BaseHelper['loggedInUserInfo']['auths']['admin'][auth] == 'undefined') return false;
        
        return true;
    }

    userImitation(user)
    {
        this.sessionHelper.userImitation(user);
    }

    authWizard(table)
    {
        this.generalHelper.navigate('authWizard/'+table['name']+"/"+table['id']);
    }

    dataEntegrator(table)
    {
        this.generalHelper.navigate('dataEntegrator/'+table['name']+"/"+table['id']);
    }

    doOperation(policyType, record)
    {
        switch(policyType)
        {
            case 'show': this.show(record); break;
            case 'delete': this.delete(record); break;
            case 'clone': this.clone(record); break;
            case 'edit': this.edit(record); break;
            case 'archive': this.archive(record); break;
            default: alert(policyType + ": " + record.id);
        }
    }

    show(record)
    {
        this.generalHelper.navigate("table/"+this.tableName+"/"+record.id)
    }

    archive(record)
    {
        this.generalHelper.navigate("table/"+this.tableName+"/"+record.id+"/archive")
    }

    delete(record)
    {
        var title = "Kayıt silinecek";
        var message = record.id + " id 'li kaydı simek istediğinize emin misiniz?";

        if(this.selectedRecordList.length > 1)
        {
            title = this.selectedRecordList.length+" kayıt silinecek";
            var message = "";
            for(var i = 0; i < this.selectedRecordList.length; i++)
                message += this.selectedRecordList[i].id + ", ";
            
            message = message.substr(0, message.length -2);
            message += " id 'li kayıtları simek istediğinize emin misiniz?";
        }

        this.messageHelper.swarmConfirm(title, message, "warning")
        .then((r) =>
        {
            if(r != true) return;

            if(this.selectedRecordList.length <= 1)
            {
                this.deleteRecord(record);
                return;
            }

            for(var i = 0; i < this.selectedRecordList.length; i++)
            {
                this.deleteRecord(this.selectedRecordList[i]);
                //BaseHelper.sleep(1000);
            }
            this.selectedRecordList = [];
            this.selectedRecord = null;
        });
    }

    deleteRecord(record)
    {
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+this.tableName+"/"+record.id+"/delete";
        
        this.generalHelper.startLoading();

        this.sessionHelper.doHttpRequest("GET", url)
        .then((data) => 
        {
            this.generalHelper.stopLoading();

            if(typeof data['message'] == "undefined")
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            else if(data['message'] == 'success')
                this.deleteSuccess(record);
            else
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
        })
        .catch((e) => { this.generalHelper.stopLoading(); });
    }

    deleteSuccess(record)
    {
        this.messageHelper.toastMessage("Silme başarılı", 'success');

        var data = BaseHelper.readFromPipe(this.getLocalKey("data"));
        var recs = data[this.params.page].records;

        for(var i = 0; i < recs.length; i++)
            if(recs[i]['id'] == record['id'])
            {
                data[this.params.page].records.splice(i, 1);
                
                var pages = Object.keys(data);
                for(var j = 0; j < pages.length; j++)
                {
                    var p = pages[j];
                    delete data[p].collectiveInfos;
                }

                BaseHelper.writeToPipe(this.getLocalKey("data"), data);
                DataHelper.deleteDataOnPipe('deleted', this.tableName);
                
                return;
            }
    }

    create()
    {
        this.generalHelper.navigate("table/"+this.tableName+"/create")
    }

    deleted()
    {
        this.generalHelper.navigate("table/"+this.tableName+"/deleted")
    }

    edit(record)
    {
        this.generalHelper.navigate("table/"+this.tableName+"/"+record.id+"/edit")
    }

    clone(record)
    {
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+this.tableName+"/"+record.id+"/clone";
        
        this.generalHelper.startLoading();

        this.sessionHelper.doHttpRequest("GET", url)
        .then((data) => 
        {
            this.generalHelper.stopLoading();

            if(typeof data['message'] == "undefined")
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            else if(data['message'] == 'success')
                this.cloneSuccess(data['id']);
            else if(data['message'] == 'error')
            {
                var list = '';
                var keys = Object.keys(data['errors']);
                for(var i = 0; i < keys.length; i++)
                    for(var j = 0; j < data['errors'][keys[i]].length; j++)
                        list += ' - '+data['errors'][keys[i]][j] + '<br>';

                this.messageHelper.sweetAlert("Klon esnasında bazı hatalar oluştu!<br><br>"+(list), "Hata", "warning");
            }
            else
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
        })
        .catch((e) => { this.generalHelper.stopLoading(); });
    }

    cloneSuccess(id)
    {
        this.messageHelper.toastMessage("Klonlama başarılı", 'success');
        
        this.params.filters =
        {
            'id': 
            {
                'type': 1,
                'guiType': 'numeric',
                'filter': id
            }
        };
        
        this.loadDataInterval(100, true);
    }

    restore(record)
    {
        this.messageHelper.swarmConfirm("Kayıt geri yüklenecek", record.id + " id 'li kaydı geri yüklemek istediğinize emin misiniz?", "warning")
        .then((r) =>
        {
            if(r != true) return;
            this.restoreRecord(record);
        })
    }

    restoreRecord(record)
    {
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+this.tableName+"/"+record.id+"/restore";
        
        this.generalHelper.startLoading();

        this.sessionHelper.doHttpRequest("GET", url)
        .then((data) => 
        {
            this.generalHelper.stopLoading();

            if(typeof data['message'] == "undefined")
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            else if(data['message'] == 'success')
                this.restoreSuccess();
            else
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
        })
        .catch((e) => { this.generalHelper.stopLoading(); });
    }

    restoreSuccess()
    {
        DataHelper.deleteDataOnPipe('list', this.tableName);

        var id = this.baseUrl.split("/")[2];
        DataHelper.deleteDataOnPipe('archive', this.tableName, parseInt(id));
        DataHelper.deleteDataOnPipe('show', this.tableName, parseInt(id));        
        DataHelper.deleteDataOnPipe('deleted', this.tableName);
        
        this.messageHelper.toastMessage("Geri yükleme başarılı", 'success');
        this.generalHelper.navigate('table/'+this.tableName);
    }



    /****    Data Functions     *****/

    getLocalKey(attr)
    {
        return "user:"+BaseHelper.loggedInUserInfo.user.id+"."+this.baseUrl+"."+attr;
    }

    getData(path = '')
    {
         var data = BaseHelper.readFromPipe(this.getLocalKey("data"));

        if(data == null) return null;
        
        data = data[this.params.page];
        return DataHelper.getData(data, path);
    }

    getParam(path = '')
    {
        return DataHelper.getData(this.params, path);
    }

    getCollectiveInfo(columnName)
    {
        var nameMap = 
        {
            'sum': 'Toplam',
            'avg': 'Ortalama',
            'min': 'En az',
            'max': 'En çok',
            'count': 'Adet'
        };

        var info = this.getData('collectiveInfos.'+columnName);
        
        if(info == null) return "";
        if(info == "") return "";

        return nameMap[info['type']] + ': ' + info['data'];
    }

    loadDataInterval(timeout = null, del = false)
    {
        if(timeout == null) timeout = this.loadDataTimeout;
        
        var params =
        {
            del: del,
            th: this
        };

        function func(params)
        {
            if(params.del) BaseHelper.deleteFromPipe(params.th.getLocalKey("data"));
            params.th.loadData(); 
        }

        return BaseHelper.doInterval('dataTableLoadData', func, params, timeout);
    }

    loadData()
    {
        var temp = this.getData();
        if(temp != null) 
            return this.dataChanged.emit(temp);

        var url = this.sessionHelper.getBackendUrlWithToken()+this.baseUrl;

        this.generalHelper.startLoading();

        this.sessionHelper.doHttpRequest("GET", url, {'params': BaseHelper.objectToJsonStr(this.params)})
        .then((data) => 
        {
            var temp = BaseHelper.readFromPipe(this.getLocalKey("data"));
            if(temp == null) temp = [];

            temp[this.params.page] = data;
            BaseHelper.writeToPipe(this.getLocalKey("data"), temp); 
            
            if(this.params.columns == null || this.params.columns.length == 0) 
                this.params.columns = this.getObjectKeys(data['columns']);
            
            this.generalHelper.stopLoading();
            this.addEventForFeatures();

            this.dataChanged.emit(data);
        })
        .catch((e) => { this.generalHelper.stopLoading(); });
    }

    getObjectKeys(obj)
    {
        return BaseHelper.getObjectKeys(obj)
    }



    /****    Gui Helper Functions     ****/

    getEditTdClass(record, columnName)
    {
        var notEditableColumns = ['id', 'created_at', 'updated_at', 'own_id', 'user_id'];
        if(notEditableColumns.includes(columnName)) return "";

        if(!this.can('edit', record)) return "";
        
        return 'edit-td';
    }

    isGeoColumn(columnName)
    {
        var geoColumns = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon'];
        var type = this.getColumnGuiTypeForQuery(this.getData('columns.'+columnName+'.gui_type_name'));
        return geoColumns.includes(type);
    }

    getRecordOperations()
    {
        return DataHelper.recordOperations;
    }

    convertDataForGui(record, columnName)
    {
        var type = this.getData('columns.'+columnName+".gui_type_name");
        var data = DataHelper.convertDataForGui(record, columnName, type);
        return this.sanitizer.bypassSecurityTrustHtml(data);
    }

    getColumnVisibility(columnName)
    {
        if(this.params.columns == null) return false;

        return this.params.columns.includes(columnName);
    }

    getSortPriorityForColumn(columnName)
    {
        var columns = Object.keys(this.params.sorts);
        for(var i = 0; i < columns.length; i++)
            if(columnName == columns[i])
                return i+1;
        
        return "";
    }

    getSortStateForColumn(columnName)
    {
        if(typeof this.params.sorts[columnName] == "undefined")
            return 0;
        else if(this.params.sorts[columnName]) 
            return 1;
        else
            return 2;
    }

    getFilterDescription(columnName)
    {
        if(typeof this.params.filters[columnName] == "undefined") return "";

        var displayName = this.getData('columns.'+columnName+'.display_name');
        if(displayName == null) return "";
        
        var guiType = this.getData('columns.'+columnName+'.gui_type_name');
        guiType = this.getColumnGuiTypeForQuery(guiType);

        switch (this.params.filters[columnName].type) 
        {
            case 100: return displayName + ": <b>Boş Olanlar</b>";
            case 101: return displayName + ": <b>Boş Olmayanlar</b>";
            default: return displayName + ": " + DataHelper.getFilterDescriptionByColumnGuiType(
                                                        columnName,
                                                        guiType,
                                                        this.params.filters[columnName].type,
                                                        this.params.filters[columnName].filter,
                                                        this.getLocalKey("data"));
        }
    }

    getColumnGuiTypeForQuery(guiType)
    {
        if(typeof guiType == "undefined") return "";
        if(guiType == null) return "";
        
        switch (guiType.split(':')[0]) 
        {
            case 'text': 
            case 'codeeditor': 
            case 'files': 
            case 'password': 
                return 'string';
            case 'select':
            case 'multiselectdragdrop':
                return 'multiselect';
            case 'point': 
            case 'multipoint': 
            case 'linestring': 
            case 'multilinestring':
            case 'polygon': 
                return 'multipolygon';
            default: return guiType;
        }
    }  

    getFilterJson(selectedFilter)
    {
        return BaseHelper.objectToJsonStr(this.selectedFilter);
    } 

    getBasicFilterObject(columnName)
    {
        return {
            type: 1,
            guiType: this.getData('columns.'+columnName+'.gui_type_name'),
            filter: ""
        };
    }

    getRecordRowClass(index)
    {
        for(var i = 0; i < this.selectedRecordList.length; i++)
            if(index == this.selectedRecordList[i].index)
                return "selected-row";

        return "";
    }

    /****   Gui Action Functions   ****/

    downloadStandartReport()
    {
        var types = ['excel', 'pdf', 'csv'];
        var format = prompt("Hangi formatta indirmek istersiniz? (excel, csv yada pdf)", "excel");
        if(!types.includes(format)) format = 'excel';
        
        var url = this.sessionHelper.getBackendUrlWithToken()+this.baseUrl;
        var temp = BaseHelper.getCloneFromObject(this.params);
        temp['report_type'] = format;
        url += "/report?params="+BaseHelper.objectToJsonStr(temp);

        window.open(url, "_blank");
    }

    closeModal(id)
    {
        BaseHelper.closeModal(id);
    }

    editRecodData(record, columnName)
    {
        this.inFormTableName = this.tableName;
        this.inFormColumnName = columnName;
        
        this.inFormRecordId = record.id;
        if(this.inFormRecordId < 1) return;

        var rand = Math.floor(Math.random() * 10000) + 1;
        this.inFormElementId = "ife-"+rand;
        
        setTimeout(() => 
        {
            $('#'+this.inFormElementId+'inFormModal').modal('show');
        }, 100);
    }

    clearSelectionText()
    {
        if (window.getSelection) {window.getSelection().removeAllRanges();}
        else if (document['selection']) {document['selection'].empty();}
    }

    selectRecord(event, record, i)
    {
        if(BaseHelper["pipe"]["altKey"]) return true;

        event.preventDefault();
        record.index = i;

        if(this.selectedRecord == null)
        {
            this.selectedRecord = record;
            this.selectedRecordList = [record];
        }
        else if(this.selectedRecord.index == record.index)
        {
            this.selectedRecord = null;
            this.selectedRecordList = [];
        }
        else if(BaseHelper['pipe']['shiftKey'])
        {
            var records = this.getData('records');
            this.selectedRecordList = [];

            var start = this.selectedRecord.index;
            var end = i;

            if(start > i)
            {
                var temp = end;
                end = start;
                start = temp;
            }

            for(var j = 0; j < records.length; j++)
                if(j >= start && j <= end)
                {
                    records[j].index = j;
                    this.selectedRecordList.push(records[j]);
                }
        }
        else if(BaseHelper['pipe']['ctrlKey'])
        {
            this.selectedRecordList.push(record);   
        }
        else
        {
            this.selectedRecord = record;
            this.selectedRecordList = [record];
        }

        this.clearSelectionText()
    }

    detailFilterChanged(filter)
    {
        if(filter.filter != null && filter.filter.length == 0)
            delete this.params.filters[filter.columnName];
        else
            this.params.filters[filter.columnName] = filter;
        
        this.saveParamsToLocal()
        this.loadDataInterval(this.loadDataTimeout, true);
    }

    limitUpdated(limit)
    {
        this.params.page = 1;
        this.params.limit = parseInt(limit);

        this.saveParamsToLocal()
        this.loadDataInterval(100, true);
    }

    pageUpdated(page)
    {
        this.params.page = parseInt(page);

        this.saveParamsToLocal()
        this.loadDataInterval(100);
    }

    prevPage()
    {
        this.pageUpdated(this.params.page-1);
    }

    nextPage()
    {
        this.pageUpdated(this.params.page+1);
    }

    getDefaultParams()
    {
        return {
            page: 1,
            limit: 10,
            column_array_id: 0,
            column_array_id_query: 0,
            sorts: {},
            filters: {},
            edit: true,
            columns: null
        };
    }
    
    fillParamsFromLocal()
    {  
        this.params = this.getDefaultParams();
        this.params.limit = this.defaultLimit;

        var temp = BaseHelper.readFromLocal(this.getLocalKey("params"));
        if(temp != null) this.params = temp;

        if(this.tableName.indexOf('tree:') == -1)
        {
            var auth = BaseHelper.loggedInUserInfo.auths.tables[this.tableName];
            this.params.column_array_id = auth['lists'][0];
            this.params.column_array_id_query = auth['queries'][0];
        }
        else
        {
            var temp:any = this.tableName.replace('tree:', '').split(':');
            this.params.column_array_id = temp[1];
            this.params.column_array_id_query = temp[1];
        }

        if(this.params.columns == null || this.params.columns.length == 0)
            this.params.columns = this.getObjectKeys(this.getData('columns'));
    }

    saveParamsToLocal()
    {
        BaseHelper.writeToLocal(this.getLocalKey("params"), this.params);
    }

    filterChanged(columnName, event)
    {
        if(!this.addFilterFromEvent(columnName, event)) return;

        this.params.page = 1;
        this.saveParamsToLocal();
        this.loadDataInterval(this.loadDataTimeout, true);
    }

    addFilterFromEvent(columnName, event, filterType = -1)
    {
        if(filterType == -1)
        {
            if(typeof this.params.filters[columnName] != "undefined")
                filterType = this.params.filters[columnName].type;
            else
                filterType = 1;
        }

        var guiType = this.getData('columns.'+columnName+'.gui_type_name')      

        return this.addFilterFromEventForBasicType(columnName, event, filterType);
    }

    addFilterFromEventForBasicType(columnName, event, filterType)
    {
        var guiType = this.getData('columns.'+columnName+'.gui_type_name');
        guiType = this.getColumnGuiTypeForQuery(guiType);
        
        var filter = event.target.value;
        filter = DataHelper.changeDataForFilterByGuiType(guiType, filter, event.target.name, columnName, this.getLocalKey("data"));
    
        if(filter.toString().length == 0) 
        {
            delete this.params.filters[columnName];
            return true;
        }

        this.params.filters[columnName] = 
        {
            type: filterType,
            guiType: guiType,
            filter: filter
        };

        return true;
    }

    

    sortByColumn(columnName)
    {
        if(typeof this.params.sorts[columnName] == "undefined")
            this.params.sorts[columnName] = true;
        else
        {
            if(this.params.sorts[columnName]) 
                this.params.sorts[columnName] = false;
            else
                delete this.params.sorts[columnName];
        }
        
        this.saveParamsToLocal()
        this.loadDataInterval(this.loadDataTimeout, true);
    }

    clearColumnFilter(columnName)
    {
        if(typeof this.params.filters[columnName] == "undefined") return;

        delete this.params.filters[columnName];

        this.saveParamsToLocal()
        this.loadDataInterval(this.loadDataTimeout, true);
    }

    dropColumn(event: CdkDragDrop<string[]>) 
    {
        moveItemInArray(this.params.columns, event.previousIndex, event.currentIndex);
        this.saveParamsToLocal();
    }

    showAllColumns()
    {
        this.params.columns = this.getObjectKeys(this.getData('columns'));
        this.saveParamsToLocal();
    }

    hideAllColumns()
    {
        this.params.columns = [];
        this.saveParamsToLocal();
    }

    toggleColumnVisibility(columnName)
    {
        if(!this.params.columns.includes(columnName))
        {
            this.params.columns.push(columnName);
        }
        else
        {
            var len = this.params.columns.length;
            for(var i = 0; i < len; i++)
                if(columnName == this.params.columns[i])
                    this.params.columns.splice(i, 1);
        }
        
        this.saveParamsToLocal();
    }

    toggleEditMode()
    {
        this.params.editMode = !this.params.editMode;
        this.saveParamsToLocal();

        this.messageHelper.toastMessage("Düzenleme modu " + (this.params.editMode ? "aktif" : "pasif"));
    }

    getEditMode()
    {
        return this.params.editMode;
    }

    openDetailFilterModal(columnName)
    {
        if(typeof this.params.filters[columnName] == "undefined")
            this.selectedFilter = this.getBasicFilterObject(columnName);
        else
            this.selectedFilter = this.params.filters[columnName];
            
        this.selectedFilter['columnName'] = columnName;
        
        $('#detailFilterModal').modal('show');
    }



    /****    Events Functions    ****/

    inFormSavedSuccess(event)
    {
        var temp = BaseHelper.readFromPipe(this.getLocalKey("data"));

        var len = temp[this.params.page]['records'].length;
        for(var i = 0; i < len; i++)
            if(this.inFormRecordId == temp[this.params.page]['records'][i]['id'])
            {
                temp[this.params.page]['records'][i][this.inFormColumnName] = event.in_form_data.display;
                break;
            }
        
        BaseHelper.writeToPipe(this.getLocalKey("data"), temp); 
        this.closeModal(this.inFormElementId+'inFormModal');
    }

    addEventForFeatures()
    {
        this.aeroThemeHelper.addEventForFeature("standartElementEvents");
    }

    addEventForThemeIcons()
    {
        this.aeroThemeHelper.addEventForFeature("mobileMenuButton");
        this.aeroThemeHelper.addEventForFeature("rightIconToggleButton");
    }
}