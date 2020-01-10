import Map from 'ol/Map';
import View from 'ol/View';

import { WKT, GeoJSON} from 'ol/format';

import { Tile as TileLayer, Vector as VectorLayer } from 'ol/layer';
import { OSM, Vector as VectorSource } from 'ol/source';

import {Draw, Modify, Snap} from 'ol/interaction';

import { Circle as CircleStyle, Fill, Stroke, Style } from 'ol/style';

import {defaults as defaultControls} from 'ol/control';
import MousePosition from 'ol/control/MousePosition';
import {createStringXY} from 'ol/coordinate';


import { transform as transformProjection } from 'ol/proj';

import {register} from 'ol/proj/proj4';
import proj4 from 'proj4';

import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

import { BaseHelper } from './base';

declare var $: any;

export abstract class MapHelper 
{   
  public static mapProjection = 'EPSG:3857';
  public static userProjection = ""
  public static customProjectionAdded = false;
  


  /****    Map Object Functions     ****/

  public static addCustomProjections()
  {
    if(this.customProjectionAdded) return;

    this.userProjection = 'EPSG:'+BaseHelper.loggedInUserInfo.user.srid;

    proj4.defs('EPSG:7932', '+proj=tmerc +lat_0=0 +lon_0=30 +k=1 +x_0=500000 +y_0=0 +ellps=GRS80 +units=m +no_defs');
    register(proj4);

    this.customProjectionAdded = true;
  }

  public static createFormElementMap(mapId)
  {
    var task = this.mapFactory(mapId);
    return task;
  }

  public static createPreviewMap(mapId)
  {
    var task = this.mapFactory(mapId);
    return task;
  }

  public static mapFactory(mapId)
  {
    var h = $('body').height();
    $('#'+mapId).css('height', (h*0.9)+"px");
    $('#'+mapId).css('min-height', (h*0.9)+"px");

    var task = new Promise((resolve) =>
    {
      $('#'+mapId).html("");
      $('.custom-mouse-position').html("");
      resolve(this.getMap(mapId));
    })
    .then((map: Map) =>
    {
      $('.ol-attribution').remove();
      map.setTarget(map.getTarget());
      map.updateSize(); 

      return map;
    });

    return task;
  }

  public static getMap(mapId, params = {})
  {
    var config = this.getMapConfig(mapId);

    this.addCustomProjections();

    var osmLayer = new TileLayer(
    {
      source: new OSM()
    });

    var vector = new VectorLayer(
    {
      source: new VectorSource(
      {
        features: []
      })
    });

    var layers = [ osmLayer, vector ];

    var view = new View(
    {
      projection: this.mapProjection,
      center: config.center,
      zoom: config.zoom
    });

    var mousePositionControl = new MousePosition(
    {
      coordinateFormat: createStringXY(4),
      projection: 'EPSG:'+BaseHelper.loggedInUserInfo.user.srid,
      className: 'custom-mouse-position',
      target: document.getElementById('mouse-position-'+mapId)
    });

    var map = new Map(
    {
      target: mapId,
      layers: layers,
      view: view,
      controls: defaultControls().extend([mousePositionControl]),
    });

    map.on('moveend', (event) =>
    {
      this.updateMapConfigOnLocal(event);
    });

    return map;
  }

  public static getMapConfig(mapId)
  {
    var config = 
    {
      zoom: 8,
      center: [3315149.9161305767, 4790835.90153615]
    }

    var temp = BaseHelper.readFromLocal('map.'+mapId+'.config');
    if(temp != null)
      config = temp;

    return config;
  }



  /****   Operations    ****/

  public static addDraw(map, type, multiple)
  {
    return new Promise((resolve) =>
    {
      var draw = new Draw(
      {
        source: this.getVectorSource(map),
        type: type
      });
      draw.on('drawend', (e) => this.featuresChanged(map, multiple, e));

      map.addInteraction(draw);

      resolve(draw);
    });     
  }

  public static addModify(map, multiple)
  {
    return new Promise((resolve) =>
    {
      var modify = new Modify(
      {
        source: this.getVectorSource(map)
      });
      modify.on('modifyend',(e) => this.featuresChanged(map, multiple, e));

      map.addInteraction(modify);

      resolve(modify);
    });     
  }

  public static addSnap(map)
  {
    return new Promise((resolve) =>
    {
      var snap = new Snap(
      {
        source: this.getVectorSource(map)
      });
      map.addInteraction(snap);

      resolve(snap);
    });    
  }



  /****    Events     ****/

  public static emitDataChangedEvent(map, e)
  {
    e.type = 'dataChanged';
    map.dispatchEvent(e);
  }

  public static updateMapConfigOnLocal(event) 
  {
    var map = event.map;

    var name = map.getTarget();
    var center = map.getView().getCenter();
    var zoom = map.getView().getZoom();

    var config = 
    {
      zoom: zoom,
      center: center
    }

    BaseHelper.writeToLocal('map.'+name+'.config', config);
  }



  /****    Features Functions    ****/

  public static featuresChanged(map, multiple, e)
  {
    setTimeout(() => 
    {
      if(this.getAllFeatures(map).length < 2) 
      {
        this.emitDataChangedEvent(map, e);
        return;
      }

      if(!multiple)
        this.showNoMultipleConfirm(map, e);
      else
        this.emitDataChangedEvent(map, e);
        
    }, 100);

    return true;
  }

  public static getFeatureFromWkt(wkt, dataProjection = 'EPSG:4326', featureProjection = null)
  {
    if(featureProjection == null) 
      featureProjection = this.mapProjection;

    var format = new WKT();

    var feature = format.readFeature(wkt, 
    {
      dataProjection: dataProjection,
      featureProjection: featureProjection
    });

    feature.setStyle(this.getDefaultStyle('Point'));

    return feature;
  }

  public static getVectorLayer(map)
  {
    var layers = map.getLayers().array_;
    for(var i = 0; i < layers.length; i++)
        if(map.getLayers().array_[i] instanceof VectorLayer)
          return map.getLayers().array_[i];
  }

  public static getVectorSource(map)
  {
    var vectorLayer = this.getVectorLayer(map);
    return vectorLayer.getSource();
  }

  public static clearAllFeatures(map)
  {
    this.getVectorSource(map).clear();
  }

  public static deleteFeature(map, index)
  {
    var source = this.getVectorSource(map);
    var feature = source.getFeatures()[index];
    source.removeFeature(feature);
  }

  public static selectFeature(map, index, onlyOneFeature = true)
  {
    var source = this.getVectorSource(map);
    var features = source.getFeatures();

    if(onlyOneFeature)
      for(var i = 0; i < features.length; i++)
      {
        var type = features[i].getGeometry().getType();
        var style = this.getDefaultStyle(type);
        features[i].setStyle(style);
      }
    
    var type = features[index].getGeometry().getType();
    var style = this.getSelectedStyle(type);
    features[index].setStyle(style);
  }

  public static getAllFeaturesAsGeoJsonObject(map)
  {
    var writer = new GeoJSON();
    var source = this.getVectorSource(map);
    var json = writer.writeFeatures(source.getFeatures());

    return BaseHelper.jsonStrToObject(json);
  }

  public static getAllFeaturesAsWkt(map, targetProjection = null)
  {
    var sourceProjection = this.mapProjection;
    if(targetProjection == null) targetProjection = this.userProjection;

    var format = new WKT();

    var source = this.getVectorSource(map);
    var features = source.getFeatures();
    
    var wkts = [];
    for(var i = 0; i < features.length; i++)
    {
      features[i].getGeometry().transform(sourceProjection, targetProjection);
      wkts.push(format.writeGeometry(features[i].getGeometry()));
      features[i].getGeometry().transform(targetProjection, sourceProjection);
    }

    return wkts;
  }

  public static getAllFeatures(map)
  {
    return this.getVectorSource(map).getFeatures();
  }

  public static addFeatureByWkt(map, wkt, projection = null)
  {
    if(projection == null) projection = this.userProjection;
    
    return new Promise((resolve) =>
    {
      var feature = this.getFeatureFromWkt(wkt, projection);
      this.getVectorSource(map).addFeature(feature);

      var extent = feature.getGeometry().getExtent();
      map.getView().animate(
      {
        center: this.getCenterOfExtent(extent),
        duration: 500
      });

      resolve(feature);
    }); 
  }




  /****    Common Functions    ****/

  public static showNoMultipleConfirm(map, e)
  {
      Swal.fire(
      {
          title: 'Emin misiniz?',
          text: "Bu nesneyi ekleyebilmek için eski nesneler silinecek!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Evet, sil",
          cancelButtonText: "Hayır"
      })
      .then((r) =>
      {
          var features = this.getAllFeatures(map);
          var lastFeature =  features[features.length -1];

          if(r.value)
          {
              this.clearAllFeatures(map);
              this.getVectorSource(map).addFeature(lastFeature);
              this.emitDataChangedEvent(map, e);
          }
          else
              MapHelper.getVectorSource(map).removeFeature(lastFeature);
      });
  }

  /*public static getTreeFromFeature(id, feature)
  {
    switch(feature.getGeometry().getType())
    {
      case 'Polygon': return this.getTreeFromFeaturePolygon(id, feature);
      default: alert(feature.getGeometry().getType() + " yok"); return "";
    }
  }

  public static getTreeFromFeaturePolygon(id, feature)
  {
    var coords = feature.getGeometry().getCoordinates()[0];

    var html = "<ul>";

    for(var i = 0; i < coords.length; i++)
      html += "<li id='coords-"+id+"-"+i+"'>"+coords[i][0]+" "+coords[i][1]+"</li>"

    html += "</ul>";

    return html;
  }*/

  public static getDefaultStyle(type)
  {
    switch(type)
    {
      case 'Polygon': return null;
      case 'Linestring': return null;
      case 'Point': return null;
    }
  }

  public static getSelectedStyle(type)
  {
    switch(type)
    {
      case 'Polygon': return this.getSelectedStylePolygon();
      case 'Linestring': return this.getSelectedStyleLinestring();
      case 'Point': return this.getSelectedStylePoint();
    }
  }

  public static getSelectedStyleLinestring()
  {
    return new Style(
    {
      stroke: new Stroke(
      {
        color: 'red',
        width: 3
      })
    });
  }

  public static getSelectedStylePolygon()
  {
    return new Style(
    {
      stroke: new Stroke(
      {
        color: 'red',
        width: 3
      }),
      fill: new Fill(
      {
        color: 'rgba(0, 0, 255, 0.9)'
      })
    });
  }

  public static getSelectedStylePoint()
  {
    return new Style(
    {
      image: new CircleStyle(
      {
        radius: 8,
        fill: new Fill(
        {
          color: 'red'
        })
      })
    });
  }

  public static getCenterOfExtent(extent)
  {
    var X = extent[0] + (extent[2]-extent[0])/2;
    var Y = extent[1] + (extent[3]-extent[1])/2;
    return [X, Y];
  }

  public static transformCoorditanes(coords, sourceProjection = null, targetProjection = null)
  {
    if(sourceProjection == null) sourceProjection = this.mapProjection;
    if(targetProjection == null) targetProjection = this.userProjection;
    if(sourceProjection == targetProjection) return coords;

    return transformProjection(coords, sourceProjection, targetProjection);
  }

  public static transformFeatureCoorditanes(feature, sourceProjection = null, targetProjection = null)
  {
    if(sourceProjection == null) sourceProjection = this.mapProjection;
    if(targetProjection == null) targetProjection = this.userProjection;
    if(sourceProjection == targetProjection) return feature;

    return feature.getGeometry().transform(sourceProjection, targetProjection);
  }
}