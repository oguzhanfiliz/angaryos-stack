import { Component, EventEmitter, Input, Output } from '@angular/core';
import { BaseHelper } from './../../../base';
import { DataHelper } from './../../../data';
import { SessionHelper } from './../../../session';
import { MessageHelper } from './../../../message';

declare var $: any;

@Component(
{
    selector: 'multi-select-element',
    styleUrls: ['./multi-select-element.component.scss'],
    templateUrl: './multi-select-element.component.html'
})
export class MultiSelectElementComponent
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
    @Input() showClearDataButton;
    @Input() createForm: boolean = false;
    
    record = null;
    
    baseElementSelector = "";
    val = [];
    selectedVal = [];

    @Output() changed = new EventEmitter();

    constructor(
        private messageHelper: MessageHelper,
        private sessionHelper: SessionHelper
    ) 
    {
        if(this.showClearDataButton == null)
            this.showClearDataButton = false;
    }

    ngAfterViewInit()
    {         
        if(this.upFormId.length > 0)
            this.baseElementSelector = '[ng-reflect-id="'+this.upFormId+'"] ';

        if(this.showClearDataButton == "false")
            this.showClearDataButton = false;

        this.elementOperations();
    }
    
    ngOnChanges()
    {              
        if(typeof this.recordJson != "undefined" && this.recordJson != "")
            this.record = BaseHelper.jsonStrToObject(this.recordJson);
            
        if(this.createForm || this.valueJson.length > 0) 
        {
            var key = "user:"+BaseHelper.loggedInUserInfo.user.id+"."+this.baseUrl+".data";
            
            this.val = [];
            this.selectedVal = []
            var temp = [];
            
            if(this.createForm)
            {
                if(this.defaultData == null || this.defaultData == "") return;

                temp = BaseHelper.jsonStrToObject(this.defaultData);
            }
            else
                temp = BaseHelper.jsonStrToObject(this.valueJson);
            
            if(temp == null) return;
            
            for(var i = 0; i < temp.length; i++)
            {
                this.val.push(temp[i]['source']);
                this.selectedVal.push(temp[i]['source']);

                var tempKey = key + ".selectQueryElementDataCache."+this.name+"."+temp[i]['source'];
                BaseHelper.writeToLocal(tempKey, temp[i]['display']);
            }
            
            setTimeout(() => 
            {
                $(this.baseElementSelector+' [name="'+this.name+'"]').val(this.selectedVal);
                $(this.baseElementSelector+' [name="'+this.name+'"]').trigger('change');
            }, 200)
        }
        else if(this.value.substr(0,1) == '[')
            this.val = BaseHelper.jsonStrToObject(this.value);
        else if(this.value == '')
            this.val = [];
        else
            this.val = this.value.split(","); 
    }

    elementOperations()
    {      
        $.getScript('assets/ext_modules/select2/select2.min.js', () => 
        {
            switch(this.type)
            {
                case 'multiselect:static':
                    this.addSelect2Static();
                    break; 
                default:
                    this.addSelect2();
                    break;
            }
        });        
    }

    handleChange(event)
    {
        this.changed.emit(event);
    }
    
    addSelect2Static()
    {
        var url = this.sessionHelper.getBackendUrlWithToken()+this.baseUrl;
        url += "/getSelectColumnData/"+this.columnName+"?search=***&page=1&limit=500";
        
        if(!this.createForm && this.record != null) url += '&editRecordId='+this.record['id'];
                    
        if(this.upColumnName.length > 0) 
        {
            url += '&upColumnName='+this.upColumnName;
            url += '&upColumnData='+$('#'+this.upColumnName).val();
            
            var temp = BaseHelper.getAllFormsData(this.baseElementSelector);
            url += '&currentFormData='+BaseHelper.objectToJsonStr(temp);
        }
        
        var th = this;
        
        $.ajax(
        {
            url : url,
            type : "GET",
            data : {},
            success : (data) =>
            {
                if(typeof data['results'] == 'undefined')
                {
                    this.messageHelper.sweetAlert("Klonlama yapıldı ama yeni kayıt bilgisi alınırken beklenmedik bir cevap geldi!", "Hata", "warning");
                }
                else
                {
                    var element = $(this.baseElementSelector+' [name="'+this.name+'"]');
                    var key = "user:"+BaseHelper.loggedInUserInfo.user.id+"."+this.baseUrl+".data";
            
                    for(var i = 0; i < data['results'].length; i++)
                    {
                        var item = data['results'][i];
                        
                        if(this.val.includes(item['id'])) continue;
                        
                        var tempKey = key + ".selectQueryElementDataCache."+this.name+"."+item['id'];
                        BaseHelper.writeToLocal(tempKey, item['text']);
                        
                        this.val.push(item['id']);
                    }
                    
                    element.select2(
                    {
                        allowClear: true,
                        placeholder: $(this.baseElementSelector+' [name="'+this.name+'"] span').html(),
                    })
                    .on('select2:select', (event) => th.selected(event))
                    .on('select2:unselect', (event) => th.unselected(event));
                }
            },
            error : (e) =>
            {
                this.messageHelper.toastMessage("Bir hata oluştu", "warning");
            }
        });
        
        $(document).on('click', this.baseElementSelector+' [ng-reflect-name="'+this.name+'"] .select2-selection__choice', function(e) 
        {
            var elementId = 'multi-select-element[ng-reflect-name="'+th.name+'"]';

            $(elementId+' .select2-selection__choice')
            .each((i, opt) => $(opt).removeClass('selected-option'));

            $(e.target).addClass('selected-option');

            $(elementId+" select").select2('close');
        });
    }

    addSelect2()
    {
        $(this.baseElementSelector+' [name="'+this.name+'"]').val(this.val)
        
        var url = BaseHelper.backendUrl + BaseHelper.token;
        url += "/"+this.baseUrl + "/getSelectColumnData/" + this.columnName;
        
        var th = this;
        $(this.baseElementSelector+' [name="'+this.name+'"]').select2(
        {
            ajax: 
            {
                url: url,
                dataType: 'json',
                delay: 1000,
                cache: false,
                data: function (params) 
                {
                    var r = new Object();
                    
                    r['search'] = params['term'];
                    r['page'] = params['page'];

                    if(!th.createForm && th.record != null) r['editRecordId'] = th.record['id'];
                    
                    if(th.upColumnName.length == 0) return r;

                    r['upColumnName'] = th.upColumnName;
                    r['upColumnData'] = $('#'+th.upColumnName).val();
                    
                    var temp = BaseHelper.getAllFormsData(this.baseElementSelector);
                    r['currentFormData'] = BaseHelper.objectToJsonStr(temp);
                    
                    return r;
                }
            },
            
            allowClear: this.showClearDataButton,
            closeOnSelect: false,
            minimumInputLength: 3,
            placeholder: $(this.baseElementSelector+' [name="'+this.name+'"] span').html(),
            sorter: function(data) 
            {
                return data.sort(function(a, b) 
                {
                    return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
                });
            }
        })
        .on('select2:select', (event) => th.selected(event))
        .on('select2:unselect', (event) => th.unselected(event));

        var th = this;
        $(document).on('click', this.baseElementSelector+' [ng-reflect-name="'+this.name+'"] .select2-selection__choice', function(e) 
        {
            var elementId = 'multi-select-element[ng-reflect-name="'+th.name+'"]';

            $(elementId+' .select2-selection__choice')
            .each((i, opt) => $(opt).removeClass('selected-option'));

            $(e.target).addClass('selected-option');

            $(elementId+" select").select2('close');
        });
    }

    selected(event)
    {
        this.clearAndCacheDisplayNameOptions();
        this.changed.emit(event);
    }

    unselected(event)
    {
        this.clearAndCacheDisplayNameOptions();
        this.changed.emit(event);
    }

    clearAndCacheDisplayNameOptions()
    {
        var localKey = "user:"+BaseHelper.loggedInUserInfo.user.id+"."+this.baseUrl+"/form";
        $(this.baseElementSelector+' [name="'+this.name+'"] option[value="-9999"]').remove();
        var data = DataHelper.changeDataForFilterByGuiTypeSelectAndMultiSelect(this.columnName, this.name, localKey);
        return data;
    }

    addStyle()
    {
        $('.select2-results__options').css('font-size', '12px');

        $(".select2").css('font-size', '12px');
        $(".select2").css('margin', '4px');
        $(".select2").css('width', '100%');
        $(".select2 input").css('width', '100%');
        $(".select2-selection").css('border-color', '#ccc');
        $('.select2-selection, select2-selection--multiple').css('min-height', '25px');
        $('.select2 input').css('margin', '3px 0');
        $('.select2-selection__choice').css('padding-left', '2px');
        $('.select2-selection__choice').css('margin', '2px 2px 2px 0');
        $('.select2-selection--multiple').css('width', '100%');
        
        setTimeout(() => $('.select2-selection--multiple').css('display', 'inline-table'), 200);
    }

    getDisplayName(value)
    {
        var key = "user:"+BaseHelper.loggedInUserInfo.user.id+"."+this.baseUrl+".data";
        key += ".selectQueryElementDataCache."+this.columnName+"."+value;
        return BaseHelper.readFromLocal(key); 
    }

    getValue()
    {
        return this.val;
    }
}