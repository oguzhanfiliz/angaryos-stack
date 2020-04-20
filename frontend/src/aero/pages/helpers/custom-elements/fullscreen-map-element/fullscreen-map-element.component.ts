import { Component, EventEmitter, Input, Output } from '@angular/core';

import {CdkDragDrop, moveItemInArray, transferArrayItem} from '@angular/cdk/drag-drop';

import { BaseHelper } from './../../base';
import { AeroThemeHelper } from './../../aero.theme';
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
    toolsBarVisible = true;

    /****    Defaul Functions     ****/

    constructor(
        private messageHelper: MessageHelper,
        private aeroThemeHelper: AeroThemeHelper) 
    {  }

    ngAfterViewInit() 
    {  
        this.aeroThemeHelper.loadPageScriptsLight();
    }

    ngOnChanges()
    {
        this.loggedInUserInfo = BaseHelper.jsonStrToObject(this.loggedInUserInfoJson);
        if(this.loggedInUserInfo == "") return;

        this.createMapElement()
        .then((map) => this.addLayers(map))
        .then((map) => $('.ol-zoom').css('display', 'none'));
    }

    handleChange(event)
    {
        this.changed.emit(event);
    }   



    /****    Gui Operations    ****/

    layers()
    {
        $('#layersModal').modal('show');
    }

    getLayerAuhts(map)
    {
        var layerAuths = this.loggedInUserInfo.map;
        
        var temp = BaseHelper.readFromLocal('map.'+this.loggedInUserInfo.user.id+'.layers');
        if(temp != null) layerAuths = temp;

        return layerAuths;
    }

    setToolsBarVisible(visible)
    {
        this.toolsBarVisible = visible;
    }

    openKmzModal()
    {
        $('#kmzModal').modal('show');
    }



    /****    Map Operations    ****/

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

    zoomOut()
    {
        MapHelper.zoom(this.map, false);
    }

    zoomIn()
    {
        MapHelper.zoom(this.map, true);
    }

    uploadKmz()
    {
        /*var url = this.sessionHelper.getBackendUrlWithToken()+"uploadKmz/"+this.tableName+"/";
        if(this.recordId == 0)
            url += "store";
        else
            url += this.recordId + "/update";

        var params = null;

        if(BaseHelper.formSendMethod == "POST")
            params = this.getElementsDataForUpload(); 
        else
            params = this.getElementsData();
        
        if(this.inFormColumnName.length > 0)
        {
            if(BaseHelper.formSendMethod == "POST")
                params.append('in_form_column_name', this.inFormColumnName);
            else
                params['in_form_column_name'] = this.inFormColumnName;
        }

        if(this.singleColumn)
        {
            if(BaseHelper.formSendMethod == "POST")
                params.append('single_column', this.inFormColumnName);
            else
                params['single_column'] = this.inFormColumnName;
        }

        this.startLoading();
        
        if(BaseHelper.formSendMethod == "POST")
            var request = this.sessionHelper.doHttpRequest("POST", url, params) 
        else
            var request = this.sessionHelper.doHttpRequest("GET", url, params) 
        
        request.then((data) => 
        {
            this.stopLoading();
            
            if(typeof data['message'] == "undefined")
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            else if(data['message'] == 'error')
                this.writeErrors(data['errors']);
            else if(data['message'] == 'success')
                this.saveSuccess(data);
            else
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
        })
        .catch((e) => { this.stopLoading(); });*/
    }

    
}