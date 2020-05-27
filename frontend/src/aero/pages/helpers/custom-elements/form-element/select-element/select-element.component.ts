import { Component, EventEmitter, Input, Output } from '@angular/core';
import { BaseHelper } from './../../../base';

declare var $: any;

@Component(
{
    selector: 'select-element',
    styleUrls: ['./select-element.component.scss'],
    templateUrl: './select-element.component.html'
})
export class SelectElementComponent
{
    @Input() defaultData: string;
    @Input() recordJson: string; 
    @Input() baseUrl: string;
    @Input() value: string;
    @Input() valueJson: string;
    @Input() class: string;
    @Input() name: string;
    @Input() columnName: string;
    @Input() placeholder: string;
    @Input() showFilterTypesSelect: boolean;
    @Input() filterType: string;
    @Input() upColumnName: string;
    @Input() upFormId: string = "";
    @Input() createForm: boolean = false;
    
    record = null;

    baseElementSelector = "";
    val = [];

    @Output() changed = new EventEmitter();

    ngAfterViewInit()
    {
        if(this.upFormId.length > 0)
            this.baseElementSelector = '[ng-reflect-id="'+this.upFormId+'"] ';

        this.elementOperations(); 
    }

    ngOnChanges()
    {
        if(typeof this.recordJson != "undefined" && this.recordJson != "")
            this.record = BaseHelper.jsonStrToObject(this.recordJson);
            
        if(this.createForm || this.valueJson.substr(0,1) == '[')
        {
            var key = "user:"+BaseHelper.loggedInUserInfo.user.id+"."+this.baseUrl+".data";
            
            this.val = [];
            var temp = [];
            
            if(this.createForm)
            {
                if(this.defaultData == null || this.defaultData == "") return;

                temp = BaseHelper.jsonStrToObject(this.defaultData);
            }
            else
                temp = BaseHelper.jsonStrToObject(this.valueJson);

            for(var i = 0; i < temp.length; i++)
            {
                this.val.push(temp[i]['source']);

                var tempKey = key + ".selectQueryElementDataCache."+this.name+"."+temp[i]['source'];
                BaseHelper.writeToLocal(tempKey, temp[i]['display']);
            }
        }
        else
            this.val = this.value.split(",");
    }

    elementOperations()
    {      
        $.getScript('assets/ext_modules/select2/select2.min.js', () => 
        {
            this.addSelect2()
            this.addStyle(); 
        });        
    }

    handleChange(event)
    {
        this.changed.emit(event);
    }

    addSelect2()
    {
        
        $('[name="'+this.name+'"]').val(this.val)
        
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
                    
                    if(!th.createForm) r['editRecordId'] = th.record['id'];
                    
                    if(th.upColumnName.length == 0) return r;

                    r['upColumnName'] = th.upColumnName;
                    r['upColumnData'] = $('#'+th.upColumnName).val();
                    
                    var temp = BaseHelper.getAllFormsData(this.baseElementSelector);
                    r['currentFormData'] = BaseHelper.objectToJsonStr(temp);
                    
                    return r;
                }
            },
            allowClear: true,
            minimumInputLength: 3,
            placeholder: $('[name="'+this.name+'"] span').html(),
            sorter: function(data) 
            {
                return data.sort(function(a, b) 
                {
                return a.text < b.text ? -1 : a.text > b.text ? 1 : 0;
                });
            }
        })
        .on('select2:select', (event) => 
        {
            if(event.target.value == '-9999')
            {
                $(th.baseElementSelector+' [name="'+th.name+'"]').val("");
                $(th.baseElementSelector+' #select2-'+th.name+'-container').html("");
                return;
            }
            this.changed.emit(event);
        })
        .on('select2:unselect', (event) => this.changed.emit(event));
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
    }

    getDisplayName(value)
    {
        var key = "user:"+BaseHelper.loggedInUserInfo.user.id+"."+this.baseUrl+".data";
        key += ".selectQueryElementDataCache."+this.name+"."+value;
        return BaseHelper.readFromLocal(key); 
    }

    getValue()
    {
        return this.val;
    }
}