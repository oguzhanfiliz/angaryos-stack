import { Component, EventEmitter, Input, Output } from '@angular/core';
import { BaseHelper } from './../../../base';
import { DataHelper } from './../../../data';

declare var $: any;

@Component(
{
    selector: 'multi-select-element',
    styleUrls: ['./multi-select-element.component.scss'],
    templateUrl: './multi-select-element.component.html'
})
export class MultiSelectElementComponent
{
    @Input() default: string;
    @Input() baseUrl: string;
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
    
    baseElementSelector = "";
    val = [];

    @Output() changed = new EventEmitter();

    constructor()
    {
        if(this.showClearDataButton == null)
            this.showClearDataButton = false;
    }

    ngAfterViewInit()
    {
        //console.log(this.name  + ' ngAfterViewInit')//beki gecikme istiyodur. bazen bozuk çıkıyodu bu element filtre içinde bunu koyunca düzeldi
        setTimeout(() => 
        {            
            if(this.upFormId.length > 0)
                this.baseElementSelector = '[ng-reflect-id="'+this.upFormId+'"] ';

            if(this.showClearDataButton == "false")
                this.showClearDataButton = false;

            this.elementOperations();    
        }, 500);
    }

    ngOnChanges()
    {
        //console.log(this.name  +' ngOnChanges')//beki gecikme istiyodur. bazen bozuk çıkıyodu bu element filtre içinde bunu koyunca düzeldi
        setTimeout(() => 
        {            
            if(this.valueJson.length > 0) 
            {
                var key = "user:"+BaseHelper.loggedInUserInfo.user.id+"."+this.baseUrl+".data";
                
                this.val = [];
                var temp = BaseHelper.jsonStrToObject(this.valueJson);
                if(temp == null) return;
                
                for(var i = 0; i < temp.length; i++)
                {
                    this.val.push(temp[i]['source']);

                    var tempKey = key + ".selectQueryElementDataCache."+this.name+"."+temp[i]['source'];
                    BaseHelper.writeToLocal(tempKey, temp[i]['display']);
                }
            }
            else if(this.value.substr(0,1) == '[')
                this.val = BaseHelper.jsonStrToObject(this.value);
            else
                this.val = this.value.split(",");    
        }, 500);
    }

    async waitForSelect2Library()
    {
        await BaseHelper.waitForOperation(() => $("test").select2('open') );
    }

    async elementOperations()
    {      
        await this.waitForSelect2Library();
        this.addSelect2()
        this.addStyle();
    }

    handleChange(event)
    {
        this.changed.emit(event);
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

                    if(th.upColumnName.length == 0) return r;

                    r['upColumnName'] = th.upColumnName;
                    r['upColumnData'] = $('#'+th.upColumnName).val();

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
        .on('select2:select', (event) => this.selected(event))
        .on('select2:unselect', (event) => this.unselected(event));

        var th = this;
        $(document).on('click', '[ng-reflect-name="'+this.name+'"] .select2-selection__choice', function(e) 
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
        $('.select2-selection--multiple').css('display', 'table');
        $('.select2-selection--multiple').css('width', '100%');
        //$('.select2-selection__rendered').css('display', 'inline-flex');
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