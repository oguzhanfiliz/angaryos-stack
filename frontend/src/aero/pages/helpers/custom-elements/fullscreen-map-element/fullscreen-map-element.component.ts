import { Component, EventEmitter, Input, Output } from '@angular/core';

import { BaseHelper } from './../../base';
import { MapHelper } from './../../map';
import { MessageHelper } from './../../message';

declare var $: any;

@Component(
{
    selector: 'fullscreen-map-element',
    styleUrls: ['./fullscreen-map-element.component.scss'],
    templateUrl: './fullscreen-map-element.component.html'
})
export class FullScreenMapElementComponent
{
    @Input() token: string = "public";
    @Input() loggedInUserInfoJson: string = "";
    
    @Output() changed = new EventEmitter();

    map = null;
    loggedInUserInfo = null;

    constructor(private messageHelper: MessageHelper) 
    { 
        
    }

    ngOnChanges()
    {
        this.loggedInUserInfo = BaseHelper.jsonStrToObject(this.loggedInUserInfoJson);
        if(this.loggedInUserInfo == "") return;

        this.createMapElement()
        .then((map) => this.addLayers(map))
    }

    handleChange(event)
    {
        this.changed.emit(event);
    }   

    createMapElement()
    {
        return new Promise((resolve) =>
        {
            this.map = MapHelper.createFullScreenMap('fullScreenMap');
            BaseHelper["pipe"]["geoserverBaseUrl"] = BaseHelper.backendUrl+this.token+"/getMapTile";

            resolve(this.map);
        }); 
    }

    getLayerAuhts(map)
    {
        var layerAuths = this.loggedInUserInfo.map;
        
        var temp = BaseHelper.readFromLocal('map.'+this.loggedInUserInfo.user.id+'.layers');
        if(temp != null) layerAuths = temp;

        return layerAuths;
    }

    addLayers(map)
    {
        var layerAuths = this.getLayerAuhts(map);
        return MapHelper.addLayersFromMapAuth(map, layerAuths);
    }
}