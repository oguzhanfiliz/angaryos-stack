import { Component, EventEmitter, Input, Output } from '@angular/core';

import {CdkDragDrop, moveItemInArray, transferArrayItem} from '@angular/cdk/drag-drop';

import { BaseHelper } from './../../base';
import { AeroThemeHelper } from './../../aero.theme';
import { MapHelper } from './../../map';
import { MessageHelper } from './../../message';
import { SessionHelper } from './../../session';
import { GeneralHelper } from './../../general';

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
    featuresTreeVisible = false;
    vectorFeaturesTree = {};

    /****    Defaul Functions     ****/

    constructor(
        private messageHelper: MessageHelper,
        private aeroThemeHelper: AeroThemeHelper,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper) 
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

    setFeaturesTreeVisible(visible)
    {
        this.featuresTreeVisible = visible;
    }

    isVectorFeaturesTreeNull()
    {
        var keys = Object.keys(this.vectorFeaturesTree);
        return keys.length == 0;
    }

    selectKmzFile()
    {
        $('#kmzFile').click();

        var th = this;
        $('#kmzFile').change(function ()
        {
            var exts = ['kml', 'kmz'];

            var path = $('#kmzFile').val();
            var arr = path.split('.');
            var ext = arr[arr.length-1];

            if(exts.includes(ext))
                th.uploadKmz();
            else
                th.messageHelper.sweetAlert("Geçersiz doya tipi!", "Hata", "warning");
        });
    }

    kmzAuthControl()
    {
        return this.sessionHelper.kmzAuthControl();
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

    uploadKmzValidation()
    {
        if($('#kmzFile').val() == "")
        {
            this.messageHelper.sweetAlert("Dosya boş geçilemez!", "Hata", "warning");
            return false;
        }

        return true;
    }

    deleteKmlOrKmzFileFeatures(name)
    {
        console.log("delete: " + name);
    }

    addFeaturesFromKmzOrKmlFile(tree)
    {
        var name = $('#kmzFile')[0].files[0].name;

        if(typeof this.vectorFeaturesTree[name] != "undefined")
            this.deleteKmlOrKmzFileFeatures(name);

        this.vectorFeaturesTree[name] = {};
        this.vectorFeaturesTree[name]['visible'] = true;
        this.vectorFeaturesTree[name]['data'] = {};
        
        var tempFeatures = [];

        var layers = Object.keys(tree);
        for(var i = 0; i < layers.length; i++)
        {
            var layer = layers[i];
            this.vectorFeaturesTree[name]['data'][layer] = {};
            this.vectorFeaturesTree[name]['data'][layer]['visible'] = true;
            this.vectorFeaturesTree[name]['data'][layer]['data'] = {};

            var types = Object.keys(tree[layer]);
            for(var j = 0; j < types.length; j++)
            {
                var type = types[j];
                this.vectorFeaturesTree[name]['data'][layer]['data'][type] = {};
                this.vectorFeaturesTree[name]['data'][layer]['data'][type]['visible'] = true;
                this.vectorFeaturesTree[name]['data'][layer]['data'][type]['data'] = [];

                var features = tree[layer][type];
                for(var k = 0; k < features.length; k++)
                {
                    var featureObject = tree[layer][type][k];

                    var feature = MapHelper.getFeatureFromWkt(featureObject['wkt']);
                    feature['featureObject'] = featureObject;
                    feature['selected'] = false;
                    feature['visible'] = true;
                    feature['className'] = name;
                    feature['subClassName'] = layer;
                    feature['typeName'] = type;
                    feature['index'] = k;

                    this.vectorFeaturesTree[name]['data'][layer]['data'][type]['data'].push(feature);
                    tempFeatures.push(feature);
                }
            }
        }

        MapHelper.addFeatures(this.map, tempFeatures)
        .then((features) => MapHelper.zoomToFeatures(this.map, features));
    }

    uploadKmz()
    {
        if($('#kmzFile').val() == "") return;

        var url = this.sessionHelper.getBackendUrlWithToken()+"translateKmzOrKmlToJson";
        
        if(!this.uploadKmzValidation()) return;

        var params = new FormData();
        params.append("file", $('#kmzFile')[0].files[0]);

        this.generalHelper.startLoading();
        
        this.sessionHelper.doHttpRequest("POST", url, params) 
        .then((data) => 
        {
            this.generalHelper.stopLoading();
            
            if(data == null)
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            else 
            {
                this.addFeaturesFromKmzOrKmlFile(data);
                
                $('#kmzFile').val("");

                MapHelper.addModify(this.map, true);
                MapHelper.addSnap(this.map, true);

                this.setFeaturesTreeVisible(true);
            }
                
        })
        .catch((e) => { this.generalHelper.stopLoading(); });
    }

    getClassNames()
    {
        return Object.keys(this.vectorFeaturesTree);
    }

    toogleClassVisible(className)
    {
        var temp = this.vectorFeaturesTree[className]['visible'];
        this.vectorFeaturesTree[className]['visible'] = !temp;

        var data = this.vectorFeaturesTree[className]['data'];
        var subClassNames = this.getSubClassNames(className);
        for(var i = 0; i < subClassNames.length; i++)
        {
            var subClassName = subClassNames[i];
            this.vectorFeaturesTree[className]['data'][subClassName]['visible'] = temp;
            this.toogleSubClassVisible(className, subClassName);
        }
    }

    selectAllFeatureInClass(className)
    {
        var temp = null;

        var subClassNames = this.getSubClassNames(className);
        for(var i = 0; i < subClassNames.length; i++)
        {
            var subClassName = subClassNames[i];

            if(temp == null)
            {
                var types = this.getTypeNames(className, subClassName);
                var typeName = types[0];
                temp = this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['data'][0].selected;

            }
                
            var types = this.getTypeNames(className, subClassName);
            var typeName = types[0];
            this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['data'][0].selected = temp;
            this.selectAllFeatureInSubClass(className, subClassName);
        }
    }

    getSubClassNames(className)
    {
        return Object.keys(this.vectorFeaturesTree[className]['data']);
    }

    toogleSubClassVisible(className, subClassName)
    {
        var temp = !this.vectorFeaturesTree[className]['data'][subClassName]['visible'];
        this.vectorFeaturesTree[className]['data'][subClassName]['visible'] = temp;

        var data = this.vectorFeaturesTree[className]['data'][subClassName]['data'];
        var types = this.getTypeNames(className, subClassName);
        for(var i = 0; i < types.length; i++)
        {
            var typeName = types[i];
            this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['visible'] = !temp;
            this.toogleTypeVisible(className, subClassName, typeName);
        }
    }

    selectAllFeatureInSubClass(className, subClassName)
    {
        var temp = null;

        var types = this.getTypeNames(className, subClassName);
        for(var i = 0; i < types.length; i++)
        {
            var typeName = types[i];

            if(temp == null)
                temp = this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['data'][0].selected;

            this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['data'][0].selected = temp;
            this.selectAllFeatureInType(className, subClassName, typeName)
        }
    }

    getTypeNames(className, subClassName)
    {
        return Object.keys(this.vectorFeaturesTree[className]['data'][subClassName]['data']);
    }

    toogleTypeVisible(className, subClassName, typeName)
    {
        var temp = !this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['visible'];
        this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['visible'] = temp;

        var data = this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['data'];
        for(var i = 0; i < data.length; i++)
            data[i].visible = temp;
        
        this.updateFeatureStyles();
    }

    selectAllFeatureInType(className, subClassName, typeName)
    {
        var data = this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['data'];
        var temp = !data[0].selected;
        for(var i = 0; i < data.length; i++)
            data[i].selected = temp;
        
        this.updateFeatureStyles();
    }

    getFeatures(className, subClassName, typeName)
    {
        return this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['data'];
    }

    toggleFeatureSelected(className, subClassName, typeName, i)
    {
        var temp = !this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['data'][i]['selected'];
        this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['data'][i]['selected'] = temp;

        this.updateFeatureStyles();
    }

    updateFeatureStyles()
    {
        var tempFeatures = [];

        var features = MapHelper.getAllFeatures(this.map);
        for(var i = 0; i < features.length; i++)
        {
            var className = features[i].className;
            var subClassName = features[i].subClassName;
            var typeName = features[i].typeName;
            var index = features[i].index;
            
            var temp = this.vectorFeaturesTree[className]["data"][subClassName]["data"][typeName]["data"][index];

            var type = BaseHelper.ucfirst(typeName);

            var style = null;
            if(!temp.visible)
                style = MapHelper.getInvisibleStyle(type);
            else
            {
                if(temp.selected)
                    style = MapHelper.getSelectedStyle(type);
                else
                    style = MapHelper.getDefaultStyle(type);
            }

            features[i].setStyle(style);
        }
    }
}