import { Component, EventEmitter, Input, Output } from '@angular/core';
import { BaseHelper } from './../../base';
import { MessageHelper } from './../../message';
import { SessionHelper } from './../../session';
import { GeneralHelper } from './../../general';


declare var $: any;

@Component(
{
    selector: 'boolean-fastchange-element',
    styleUrls: ['./boolean-fastchange-element.component.scss'],
    templateUrl: './boolean-fastchange-element.component.html'
})
export class BooleanFastChangeElementComponent
{
    @Input() value: string = "";
    @Input() name: string;
    @Input() recordJson: string;

    @Output() changed = new EventEmitter();
    
    record = null;
    
    constructor(
        private messageHelper: MessageHelper,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
    ) {  }
    
    ngOnChanges()
    {              
        if(this.recordJson != "")  this.record = BaseHelper.jsonStrToObject(this.recordJson);
    }
    
    onChanged(event)
    {
        var temp = window.location.href;
        temp = temp.replace(BaseHelper.baseUrl, "");
        var segments = temp.split('/');
        var tableName = segments[1];
        
        var params =
        {
            column_set_id: BaseHelper.loggedInUserInfo['auths']['tables'][tableName]['edits'][0],
            in_form_column_name: this.name,
            single_column: this.name
        }
        
        var val = $('#'+this.name+'-'+this.record['id']).prop("checked");
        params[this.name] = val ? 1 : 0;
        
        var url = this.sessionHelper.getBackendUrlWithToken()+"tables/"+tableName+"/"+this.record['id']+"/update";
        
        this.generalHelper.startLoading();
        
        this.sessionHelper.doHttpRequest("POST", url, params) 
        .then((data) => 
        {
            this.generalHelper.stopLoading();
            
            if(typeof data['message'] == "undefined")
            {
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
                this.rollback();
            }
            else if(data['message'] == 'error')
            {
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
                this.rollback();
            }
            else if(data['message'] == 'success')
                this.changed.emit(event);
            else
            {
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
                this.rollback();
            }
        })
        .catch((e) => 
        { 
            this.generalHelper.stopLoading(); 
            this.rollback();
        });
    }

    rollback()
    {
        var temp = !$('#'+this.name+'-'+this.record['id']).prop("checked");
        $('#'+this.name+'-'+this.record['id']).prop("checked", temp);
        this.value = temp.toString();
    }
}