import { Component, EventEmitter, Input, Output } from '@angular/core';

import { MapHelper } from './../../map';
 
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
}