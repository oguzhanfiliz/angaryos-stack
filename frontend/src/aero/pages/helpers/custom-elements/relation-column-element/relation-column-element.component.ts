import { Component, EventEmitter, Input, Output } from '@angular/core';
import { BaseHelper } from './../../base';
import { GeneralHelper } from './../../general';
import { SessionHelper } from './../../session';
import { MessageHelper } from './../../message';


declare var $: any;

@Component(
{
    selector: 'relation-column-element',
    styleUrls: ['./relation-column-element.component.scss'],
    templateUrl: './relation-column-element.component.html'
})
export class RelationColumnElementComponent
{
    @Input() type: string = "";
    @Input() name: string = "";
    @Input() relationJson: string = "";
    @Input() recordJson: string = "";
    
    typeBase = "";
    record = null;
    relation = null;
    items = [];
    
    constructor (
        private generalHelper: GeneralHelper,
        private sessionHelper: SessionHelper,
        private messageHelper: MessageHelper
    ) { }

    ngAfterViewInit()
    { 
        
    }
    
    ngOnChanges()
    {              
        this.typeBase = this.type.split(':')[0];
        
        if(this.recordJson != "") 
        {
            this.record = BaseHelper.jsonStrToObject(this.recordJson);
            this.fillMultiItems();
        }
        
        if(this.relationJson != "") this.relation = BaseHelper.jsonStrToObject(this.relationJson);
    }
    
    fillMultiItems()
    {
        if(this.typeBase == 'select') return;
        
        if(this.record[this.name] == null)
        {
            this.items = [];
            return;
        }
        
        if(this.record[this.name] == "")
        {
            this.items = [];
            return;
        }
        
        var temp = BaseHelper.jsonStrToObject(this.record[this.name]);
        if(typeof temp != "object") 
        {
            this.items = temp;
            return;
        }
        
        var arr = [];
        var keys = Object.keys(temp);
        for(var i = 0; i < keys.length; i++)
            arr.push(temp[keys[i]]); 
        
        this.items = arr;
    }
    
    getRelationDataUrl()
    {
        if(this.relation == null) return "";
        
        var temp = window.location.href;
        temp = temp.replace(BaseHelper.baseUrl, "");
        var segments = temp.split('/');
        
        var url = BaseHelper.backendUrl + BaseHelper.token;
        url += "/tables/"+segments[1]+"/"+this.record['id']+"/getRelationDataInfo/"+this.name;        
        
        return url;
    }
    
    openRelationDataInfoPage(event, item = null)
    {        
        var url = this.getRelationDataUrl();
        if(item != null) url += "?source="+item['source'];
        
        this.generalHelper.startLoading();
        
        this.sessionHelper.doHttpRequest("GET", url) 
        .then((data) => 
        {
            this.generalHelper.stopLoading();
            
            if(typeof data['recordId'] == "undefined") 
            {
                this.messageHelper.toastMessage("Bu kaydın bilgi kartı gösterilemez");  
                return;
            }
            
            if(!this.authControlForRelationDataInfoPage(data)) 
            {
                this.messageHelper.toastMessage("Bilgi kartı için yetkiniz yok");  
                return;
            }
            
            this.generalHelper.navigate('table/'+data['tableName']+"/"+data['recordId'], event.ctrlKey);
        })
        .catch((e) => { this.generalHelper.stopLoading(); });
    }
    
    authControlForRelationDataInfoPage(data)
    {
        var auth = BaseHelper.loggedInUserInfo['auths']['tables'];
        if(typeof auth[data['tableName']] == "undefined") return false;
        if(typeof auth[data['tableName']]['shows'] == "undefined") return false;
        
        return true;
    }
}