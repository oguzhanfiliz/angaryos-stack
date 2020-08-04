import { Component, EventEmitter, Input, Output } from '@angular/core';

import { BaseHelper } from './../../../base';
import { MapHelper } from './../../../map';
import { MessageHelper } from './../../../message';

declare var $: any;

@Component(
{
    selector: 'map-element',
    styleUrls: ['./map-element.component.scss'],
    templateUrl: './map-element.component.html'
})
export class MapElementComponent
{
    @Input() defaultData: string;
    @Input() value: string;
    @Input() type: string;
    @Input() name: string;
    @Input() columnName: string;
    @Input() placeholder: string;
    @Input() showFilterTypesSelect: boolean;
    @Input() multiple: boolean;
    @Input() filterType: string;  
    @Input() srid: string = "";
    @Input() upFormId: string = "";
    @Input() createForm: boolean = false;

    map = null;
    geoJsonObject = null;
    baseElementSelector = "";
    geoColumns = 
    {
        'point': 'Nokta',
        'linestring': 'Çizgi',
        'polygon': 'Alan',
        'multipoint': 'Nokta(lar)',
        'multilinestring': 'Çizgi(ler)',
        'multipolygon': 'Alan(lar)',
    }

    @Output() changed = new EventEmitter();
    @Output() dataChanged = new EventEmitter();

    constructor(private messageHelper: MessageHelper) { }

    

    /****    Map Functions    ****/

    ngAfterViewInit()
    {
        if(this.upFormId.length > 0)
            this.baseElementSelector = '[ng-reflect-id="'+this.upFormId+'"] ';
    }

    openMapElement()
    {
        this.showModal()
        .then(() => this.createMapElement())
        .then((map) => this.addDrawing(map))
        .then((map) => this.addFeaturesFromValue(map))
        .then((map) => this.emitDataChangedEvent())
    }

    showModal()
    {
        return new Promise((resolve) =>
        {
            $(this.baseElementSelector+' #'+this.name+'ElementModal').modal('show').on('shown.bs.modal', () => 
            {
                resolve(true);                
            });
        });
    }

    createMapElement()
    {
        return MapHelper.createFormElementMap(this.name+'MapElement').then((map) =>
        {
            this.map = map;
            return map;
        });
    }

    addDrawing(map)
    {
        var type = BaseHelper.ucfirst(this.type.replace("multi", ""));
        if(type == "Linestring") type = "LineString";
        
        MapHelper.addDraw(map, type, this.multiple)
        MapHelper.addModify(map, this.multiple)
        MapHelper.addSnap(map);
        
        map.addEventListener(   'dataChanged', 
                                (e) => this.emitDataChangedEvent(), 
                                true);

        return map;
    }

    addFeaturesFromValue(map)
    {
        if(this.value == "") return map;

        if(this.value.substr(0, 2) != '["') 
            this.value = '["' + this.value + '"]';

        var srid = null;
        if(this.srid.length > 0)
            srid = "EPSG:"+this.srid;

        var wkts = BaseHelper.jsonStrToObject(this.value);
        for(var i = 0; i < wkts.length; i++)
            MapHelper.addFeatureByWkt(map, wkts[i], srid);

        return map;
    }

    selectFeature(i)
    {
        MapHelper.selectFeature(this.map, i)
    }

    deleteFeature(i)
    {
        MapHelper.deleteFeature(this.map, i);
        this.emitDataChangedEvent();
    }
    
    addNetcadFeatures()
    {   
        setTimeout(() => $(document).off('focusin.modal'), 500);
             
        this.messageHelper.swalPrompt("Netcad nokta dizisi:", "Tamam", "İptal", "textarea")
        .then((data) =>
        {
            if(typeof data["value"] == "undefined") return;
                
            var wkt = MapHelper.getWktFromNetcad(data['value'], this.type.toLowerCase().replace('multi', ''));   

            var feature = MapHelper.getFeatureFromWkt(wkt, MapHelper.userProjection);
            MapHelper.addFeatures(this.map, [feature]);
            MapHelper.zoomToFeature(this.map, feature);
            
            if(!this.multiple) 
            {
                MapHelper.showNoMultipleConfirm(this.map)
                .then(() => this.emitDataChangedEvent());
            }
        });
    }



    /****    Gui Functions    ****/

    getDescription()
    {
        if(this.value == null) return this.placeholder;
        if(this.value.length == 0) return this.placeholder;

        return this.geoColumns[this.type];
    }   

    getStyle()
    {
        if(this.value == null) return {color: '#999'};
        if(this.value.length == 0) return {color: '#999'};

        return  {color: '#495057'};
    }

    getTransformedCoords(coords)
    {
        return MapHelper.transformCoorditanes(coords);
    }



    /****    Event Functions    ****/

    emitDataChangedEvent()
    {
        this.geoJsonObject = MapHelper.getAllFeaturesAsGeoJsonObject(this.map).features;
        this.dataChanged.emit(this.geoJsonObject);
    }

    emitChangedEvent()
    {
        var srid = null;
        if(this.srid.length > 0)
            srid = "EPSG:"+this.srid;

        var wkt = MapHelper.getAllFeaturesAsWkt(this.map, srid);
        var json = BaseHelper.objectToJsonStr(wkt);
        this.value = json
        $(this.baseElementSelector+' #'+this.name).val(json);

        const changeEvent = document.createEvent('Event');  
        changeEvent.initEvent('change', true, true);
        $(this.baseElementSelector+' #'+this.name)[0].dispatchEvent(changeEvent);
        
        $(this.baseElementSelector+' #'+this.name+'ElementModal').modal('hide');
    }

    handleChange(event)
    {
        this.changed.emit(event);
    }   
}