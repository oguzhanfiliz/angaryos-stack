import { Component, EventEmitter, Input, Output } from '@angular/core';

import { BaseHelper } from './../../../base';
import { MessageHelper } from './../../../message';
import { DataHelper } from './../../../data';
 
declare var $: any;

@Component(
{
    selector: 'detail-filter-element', 
    styleUrls: ['./detail-filter-element.component.scss'],
    templateUrl: './detail-filter-element.component.html'
})
export class DetailFilterElementComponent
{
    @Input() filterJson: string;
    @Input() baseUrl: string;
    @Input() displayName: string;

    @Output() changed = new EventEmitter();

    constructor(private messageHelper: MessageHelper){}

    getLocalKey(attr)
    {
        return "user:"+BaseHelper.loggedInUserInfo.user.id+"."+this.baseUrl+"."+attr;
    }

    detailFilter()
    {     
        var filter = BaseHelper.jsonStrToObject(this.filterJson);
        var fullnes = $('#data_fullness_state').val();
        if(fullnes.length > 0)
        {
            filter.type = parseInt(fullnes);
            filter.filter = null;
        }
        else
        {
            var id = '#'+filter['columnName']+'_filter_detail';
            filter.type = parseInt($(id+'_filter_type').val());
            filter.filter = $(id).val();
        }

        filter.filter = DataHelper.changeDataForFilterByGuiType(
                                                                    filter.guiType, 
                                                                    filter.filter, 
                                                                    filter['columnName']+'_filter_detail', 
                                                                    filter.columnName,
                                                                    this.getLocalKey("data"));

        this.changed.emit(filter);
        
        $('#detailFilterModal').modal('hide');
    }

    getColumnGuiTypeForQuery(guiType)
    {
        switch (guiType) 
        {
            case 'text': return 'string';
            case 'select': return 'multiselect';
            case 'point': return 'multipolygon';
            default: return guiType;
        }
    }  

    getData(path)
    {
        if(typeof this.filterJson == "undefined") return null;
        if(this.filterJson == "null") return null;
        if(this.filterJson.length == 0) return null;

        var data = BaseHelper.jsonStrToObject(this.filterJson);
        return DataHelper.getData(data, path);
    }
}