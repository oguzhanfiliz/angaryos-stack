import { Component, EventEmitter, Input, Output } from '@angular/core';

import { MapHelper } from './../../map';
import { BaseHelper } from './../../base';
 
declare var $: any;

@Component(
{
    selector: 'geo-preview-element', 
    styleUrls: ['./geo-preview-element.component.scss'],
    templateUrl: './geo-preview-element.component.html'
})
export class GeoPreviewElementComponent
{
    @Input() name: string;
    @Input() wkt: string;
    @Input() srid: string;
    @Input() type: string;

    getDisplayName(type)
    {
        var displayNames = 
        {
            'point': "Nokta",
            'multipoint': "Nokta(lar)",
            'linestring': "Çizgi",
            'multilinestring': "Çizgi(ler)",
            'polygon': "Alan",
            'multipolygon': "Alan(lar)",
        };

        return displayNames[type];
    }

    showPreviewModal()
    {
        return new Promise((resolve) =>
        {
            $('#mapPreviewModal').modal('show').on('shown.bs.modal', () => 
            {
                MapHelper.createPreviewMap('mapPreview').then((map) =>
                {
                    var layers = MapHelper.getBaseLayersFromMap(map);
                    for(var i = 0; i < layers.length; i++)
                        layers[i].setVisible(layers[i]['name'] == 'bing_aerialwithlabelsondemand');

                    resolve(map);
                });
            });
        });
    }

    preview()
    {
        this.showPreviewModal().then((map) => 
        {
            MapHelper.clearAllFeatures(map);
            MapHelper.addFeatureByWkt(map, this.wkt, "EPSG:"+this.srid);
        });
    }

    isElementNull()
    {
        if(this.wkt == null) return true;
        if(this.wkt.length == 0) return true;

        return false;
    }
    
    mapAuthControl()
    {
        if(typeof BaseHelper.loggedInUserInfo['auths']['map'] == "undefined")
            return false;
            
        return true;
    }
    
    getBaseUrl()
    {
        return BaseHelper.baseUrl;
    }
    
    getZoomToJson()
    {
        var params = 
        {
            wkt: this.wkt,
            srid: "EPSG:"+this.srid
        };
        
        return BaseHelper.objectToJsonStr(params);
    }
}