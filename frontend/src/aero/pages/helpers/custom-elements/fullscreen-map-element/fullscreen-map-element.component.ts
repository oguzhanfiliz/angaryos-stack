import { Component, EventEmitter, Input, Output } from '@angular/core';

import {CdkDragDrop, moveItemInArray, transferArrayItem} from '@angular/cdk/drag-drop';

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
    layerList = [];

    constructor(private messageHelper: MessageHelper) 
    {  }

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



    menu()
    {
        $('#layersModal').modal('show');
    }

    createMapElement()
    {
        return new Promise((resolve) =>
        {
            BaseHelper["pipe"]["geoserverBaseUrl"] = BaseHelper.backendUrl+this.token+"/getMapData";

            var task = MapHelper.createFullScreenMap('fullScreenMap')
            .then((map) => this.map = map);

            resolve(task);
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

    getBaseLayers()
    {
        return MapHelper.getBaseLayersFromMap(this.map);
    }

    getLayers()
    {
        var temp = MapHelper.getLayersFromMapWithoutBaseLayers(this.map);
        this.layerList = temp.reverse();

        return this.layerList;
    }
    
    changeBaseLayer(e)
    {
        var val = parseInt(e.target.value);
        MapHelper.changeBaseLayer(this.map, val);
    }

    getSelectedBaseLayerIndex()
    {
        var baseLayers = MapHelper.getBaseLayersFromMap(this.map);
        for(var i = 0; i < baseLayers.length; i++)
            if(baseLayers[i].getVisible())
                return i;

        return -1;
    }

    layerChanged(event: CdkDragDrop<string[]>) 
    {
        var len = this.layerList.length - 1;
        var prev = len - event.previousIndex;
        var curr = len - event.currentIndex;
        
        var diff = curr - prev;
        if(diff == 0) return;

        MapHelper.moveLayer(this.map, this.layerList[event.previousIndex], diff);
    }

    changeLayerVisibility(layer)
    {
        MapHelper.changeLayerVisibility(this.map, layer);
    }
}