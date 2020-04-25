import { Component, EventEmitter, Input, Output } from '@angular/core';
import { intersect as turfIntersect, polygon as turfPolygon } from '@turf/turf';

import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

import { WKT, GeoJSON} from 'ol/format';

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
    mapClickMode = "getClickedFeatureInfo";
    waitDrawSingleFeature = false;
    drawingInteraction = null;
    ctrlPressed = false;
    altPressed = false;
    selectAreaStartPixel = null;

    /****    Default Functions     ****/

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
        .then((map) => this.addEvents(map))
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
    
    isUpTableRecordSelected()
    {
        var loggedInUserId = BaseHelper.loggedInUserInfo['user']['id'];
        var key = 'user:'+loggedInUserId+'.dataTransport';
        
        var temp = BaseHelper.readFromLocal(key);
        
        return temp != null;
    }
    
    async selectTypeAndDo(func)
    {
        const { value: typeName } = await Swal.fire(
        {
            title: 'Seçmek istediğiniz tip',
            input: 'select',
            inputOptions: 
            {
                point: 'Nokta',
                linestring: 'Çizgi',
                polygon: 'Alan'
            },
            inputPlaceholder: 'Tip seçiniz',
            showCancelButton: false
        });

        if (typeof typeName == "undefined") return;
        if (typeName == "") return;

        func(typeName);
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

    addEvents(map)
    {
        var th = this;
        return new Promise((resolve) =>
        {
            th.addEventForClick(map);
            th.addEventForDataChanged(map);
            th.addEventForSelectArea(map);

            resolve(map);
        }); 
    }

    addEventForClick(map)
    {
        var th = this;
        map.on('click', (event) =>
        {
            if(th.drawingInteraction != null) 
                return;
            else if(th.featuresTreeVisible)
                th.selectClickedFeatureOnVectorSourceTree(event);
            else
                console.log(th.mapClickMode);
        });
    }

    addEventForDataChanged(map)
    {
        var th = this;
        map.on('dataChanged', (event) =>
        {
            switch(event.constructor.name)
            {
                case 'DrawEvent':
                    th.endDrawIsSingleFeatureDrawed(event);
                    th.addFeatureOnVectorFeaturesTree(event.feature);
                    break;
            }
        });
    }

    addEventForSelectArea(map)
    {
        
        var th = this;

        $(document).on('keyup keydown', function(e)
        {
            th.ctrlPressed = e.ctrlKey;
            th.altPressed = e.altKey;
        } );

        map.on('pointerdown', function(e) 
        {        
            if(!th.featuresTreeVisible) return;
            if(!th.ctrlPressed || !th.altPressed) return;

            th.selectAreaStartPixel = e.pixel;
        });

        map.on('pointerup', function(e) 
        {        
            if(th.selectAreaStartPixel == null) return;

            th.areaSelected(th.selectAreaStartPixel, e.pixel);
            th.selectAreaStartPixel = null;
        });

        map.on('pointermove', function(e) 
        {        
            if(th.selectAreaStartPixel == null) return;
            if(!th.featuresTreeVisible) return;
            if(!th.ctrlPressed || !th.altPressed) return;

            th.drawSelectArea(e.pixel);
        });

        $(document).on("mousemove", "#selectArea", function(e) 
        {
            if(th.selectAreaStartPixel == null) return;
            if(!th.featuresTreeVisible) return;
            if(!th.ctrlPressed || !th.altPressed) return;

            th.drawSelectArea([e.clientX, e.clientY]);
        });
    }

    areaSelected(selectAreaStartPixel, selectAreaEndPixel)
    {
        $('#selectArea').remove();

        this.selectTypeAndDo((typeName) =>
        {
            this.selectIntectsFeatureWithArea(typeName, selectAreaStartPixel, selectAreaEndPixel);
        });
    }

    getTurfPolygonFromStartAndEndPixel(selectAreaStartPixel, selectAreaEndPixel)
    {
        var start = this.map.getCoordinateFromPixel(selectAreaStartPixel);
        var end = this.map.getCoordinateFromPixel(selectAreaEndPixel);

        return turfPolygon(
        [
            [
                [start[0], start[1]],
                [end[0], start[1]],
                [end[0], end[1]],
                [start[0], end[1]],
                [start[0], start[1]]
            ]
        ]);
    }

    getTurfPolygonFromExtent(extent)
    {
        return turfPolygon(
        [
            [
                [extent[0], extent[1]],
                [extent[2], extent[1]],
                [extent[2], extent[3]],
                [extent[0], extent[3]],
                [extent[0], extent[1]]
            ]
        ]);
    }

    selectIntectsFeatureWithArea(type, selectAreaStartPixel, selectAreaEndPixel)
    {
        var turfPolygon = this.getTurfPolygonFromStartAndEndPixel(selectAreaStartPixel, selectAreaEndPixel);
        
        var control = false;

        var classNames = this.getClassNames();
        for(var i = 0; i < classNames.length; i++)
        {
            var className = classNames[i];
            if(!this.vectorFeaturesTree[className]['visible']) continue;

            var subClassNames = this.getSubClassNames(className);
            for(var j = 0; j < subClassNames.length; j++)
            {
                var subClassName = subClassNames[j];
                if(!this.vectorFeaturesTree[className]['data'][subClassName]['visible']) continue;

                var subClassData = this.vectorFeaturesTree[className]['data'][subClassName]['data'];

                if(typeof subClassData[type] == "undefined") continue;
                if(!subClassData[type]['visible']) continue;

                var data = subClassData[type]['data'];
                for(var k = 0; k < data.length; k++)
                {
                    var ext = data[k].getGeometry().getExtent();

                    if(ext[0] == ext[2])//Point to poly
                    {
                        ext[2] += 1;
                        ext[3] += 1;
                    }

                    var poly = this.getTurfPolygonFromExtent(ext);

                    if(turfIntersect(turfPolygon, poly))
                    {
                        control = true;
                        data[k].selected = true;
                    } 
                }
            }
        }

        if(control) this.updateFeatureStyles();
    }

    drawSelectArea(selectAreaEndPixel)
    {
        if(this.selectAreaStartPixel == null) return;

        var x1, x2, y1, y2;
        
        if(selectAreaEndPixel[0] > this.selectAreaStartPixel[0])
            x1 = this.selectAreaStartPixel[0];
        else
            x1 = selectAreaEndPixel[0];
        
        if(selectAreaEndPixel[1] > this.selectAreaStartPixel[1])
            y1 = this.selectAreaStartPixel[1];
        else
            y1 = selectAreaEndPixel[1];
        
        var fW = selectAreaEndPixel[0] - this.selectAreaStartPixel[0];
        var fH = selectAreaEndPixel[1] - this.selectAreaStartPixel[1];
        
        if(fW < 0) fW = -1 * fW;
        if(fH < 0) fH = -1 * fH;
        
        var o = $('canvas').offset();
        
        $('#selectArea').remove();
        
        var html = "<div id='selectArea' style='top:"+(y1+o.top)+"px;left:"+(x1+o.left)+"px;";
        html += "position: absolute; z-index: 3999999999;border:3px solid #cf6729;";
        html += "width:"+fW+"px;height:"+fH+"px'></div>";

        $('body').append(html);
    }

    selectClickedFeatureOnVectorSourceTree(event)
    {
        MapHelper.getFeaturesAtPixel(this.map, event.pixel)
        .then((data) =>
        {
            if(typeof data['vector'] == "undefined") return;

            for(var i = 0; i < data['vector'].length; i++)
                if(data['vector'][i].visible)
                    data['vector'][i].selected = true;
            
            this.updateFeatureStyles();
        });
    }

    createIfNotExistClassOnVectorSourceTree(className)
    {
        if(typeof this.vectorFeaturesTree[className] == "undefined")
        {
            this.vectorFeaturesTree[className] = {};
            this.vectorFeaturesTree[className]['visible'] = true;
            this.vectorFeaturesTree[className]['data'] = {};        
        }
    }

    createIfNotExistSubClassOnVectorSourceTree(className, subClassName)
    {
        if(typeof this.vectorFeaturesTree[className]['data'][subClassName] == "undefined")
        {
            this.vectorFeaturesTree[className]['data'][subClassName] = {};
            this.vectorFeaturesTree[className]['data'][subClassName]['visible'] = true;
            this.vectorFeaturesTree[className]['data'][subClassName]['data'] = {};        
        }
    }

    createIfNotExistTypeOnVectorSourceTree(className, subClassName, typeName)
    {
        if(typeof this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName] == "undefined")
        {
            this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName] = {};
            this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['visible'] = true;
            this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['data'] = [];            
        }
    }

    addFeatureOnVectorFeaturesTree(feature)
    {
        var className = "Yerel Nesneler";
        var subClassName = "Çizimler";
        var typeName = feature.getGeometry().getType().toLowerCase();

        feature['featureObject'] = {'type': 'drawed'};
        feature['selected'] = false;
        feature['visible'] = true;
        feature['className'] = className;
        feature['subClassName'] = subClassName;
        feature['typeName'] = typeName;

        this.createIfNotExistClassOnVectorSourceTree(className);
        this.createIfNotExistSubClassOnVectorSourceTree(className, subClassName);
        this.createIfNotExistTypeOnVectorSourceTree(className, subClassName, typeName);

        var i = this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['data'].length;
        feature['index'] = i;

        this.vectorFeaturesTree[className]['data'][subClassName]['data'][typeName]['data'].push(feature);

        this.featuresTreeVisible = true;
    }

    endDrawIsSingleFeatureDrawed(event)
    {
        if(!this.waitDrawSingleFeature) return;
        this.drawEnd();
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

        this.createIfNotExistClassOnVectorSourceTree(name);
        
        var tempFeatures = [];

        var layers = Object.keys(tree);
        for(var i = 0; i < layers.length; i++)
        {
            var layer = layers[i];            
            this.createIfNotExistSubClassOnVectorSourceTree(name, layer);

            var types = Object.keys(tree[layer]);
            for(var j = 0; j < types.length; j++)
            {
                var type = types[j];                   
                this.createIfNotExistTypeOnVectorSourceTree(name, layer, type);

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

    drawEnd()
    {
        MapHelper.removeInteraction(this.map, this.drawingInteraction)
        .then((map) =>
        {
            this.drawingInteraction = null;
            this.waitDrawSingleFeature = false;
        })

    }

    drawStart(featureType, multi = false)
    {
        MapHelper.addDraw(this.map, featureType, true)
        .then((drawingInteraction) =>
        {
            this.waitDrawSingleFeature = !multi;
            this.drawingInteraction = drawingInteraction;        
        })
    }
    
    getSelectedFeatures()
    {
        var selectedFeatures = {};

        var features = MapHelper.getAllFeatures(this.map);
        for(var i = 0; i < features.length; i++)
        {
            if(!features[i].selected) continue;
            
            var typeName = features[i].typeName;
            
            if(typeof selectedFeatures[typeName] == "undefined") 
                selectedFeatures[typeName] = [];
                
            selectedFeatures[typeName].push(features[i]);
        }
        
        return selectedFeatures;
    }
    
    dataTransport()
    {
        if(!this.isUpTableRecordSelected()) 
            return this.messageHelper.toastMessage('Önce bir kaydı veri aktarılacak kayıt olarak belirlemelisiniz!');
        
        var selectedFeatures = this.getSelectedFeatures();
        var types = Object.keys(selectedFeatures);
        if(types.length == 0)
            return this.messageHelper.toastMessage('Aktarmak için seçilmiş nesne yok!');
            
        this.selectTypeAndDo((typeName) =>
        {
            alert('sunucudan '+typeName+' tipinin aktarılabileceği alt tabloları getir. zaten üst tablo belli');
        });
    }
    
    
}