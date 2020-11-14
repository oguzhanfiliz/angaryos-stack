import { environment } from './../../environments/environment';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { Platform } from '@ionic/angular';
import { LoadingController } from '@ionic/angular';
import { Storage } from '@ionic/storage';
import { Geolocation } from "@ionic-native/geolocation/ngx";
import { Camera, CameraOptions } from '@ionic-native/camera/ngx';
import { Device } from '@ionic-native/device/ngx';

import { BaseHelper } from './base';
import { MessageHelper } from './message';

declare var $: any;

@Injectable()
export class GeneralHelper 
{  
  private loading = null;

  public noImageUrl = BaseHelper.noImageUrl;
  public backendBaseUrl = BaseHelper.backendBaseUrl;

  constructor(
    private platform: Platform,
    private router: Router,
    private storage: Storage, 
    private loadingController: LoadingController,
    private geolocation: Geolocation,
    private messageHelper: MessageHelper,
    private camera: Camera,
    private device: Device
  ) 
  { }

  /****    Data Functions    *****/

  public getFileUrl(file, prefix = "")
  {
    if(file == null) return this.noImageUrl;

    var temp = file['destination_path']+prefix+file['file_name'];
    switch(file['disk'])
    {
      case 'fileServer': 
      case 'uploads': 
        temp = "uploads/"+temp;
        break;
    }
    
    return this.backendBaseUrl+temp;
  }

  public objectToJsonStr(obj)
  {    
    if(BaseHelper.debug) alert("GeneralHelper.objectToJsonStr.1");    
    if(BaseHelper.debug) alert("GeneralHelper.objectToJsonStr.2:"+obj);
    if(BaseHelper.debug) alert("GeneralHelper.objectToJsonStr.2+:"+(typeof obj));
    if(typeof obj == "string") return obj;
    
    if(BaseHelper.debug) alert("GeneralHelper.objectToJsonStr.3");    
    var temp = JSON.stringify(obj);

    if(BaseHelper.debug) alert("GeneralHelper.objectToJsonStr.4");
    if(BaseHelper.debug) alert("GeneralHelper.objectToJsonStr.5:"+temp);
    return temp;
  }

  public jsonStrToObject(jsonStr)
  {
    if(BaseHelper.debug) alert("GeneralHelper.jsonStrToObject.1");
    if(BaseHelper.debug) alert("GeneralHelper.jsonStrToObject.2:"+jsonStr);
    if(BaseHelper.debug) alert("GeneralHelper.jsonStrToObject.2+:"+(typeof jsonStr));

    if(typeof jsonStr != "string") return jsonStr;
    
    if(BaseHelper.debug) alert("GeneralHelper.jsonStrToObject.3");

    if(jsonStr == "") return "";

    if(BaseHelper.debug) alert("GeneralHelper.jsonStrToObject.4");
    var temp = JSON.parse(jsonStr);   
    
    if(BaseHelper.debug) alert("GeneralHelper.jsonStrToObject.5"); 
    if(BaseHelper.debug) alert("GeneralHelper.jsonStrToObject.6:"+temp); 
    return temp;
  }

  public writeToLocal(key, value, timeOut = -1)
  {
    if(timeOut == 0) return;

    var obj = 
    {
      "data": value,
      "timeOut": timeOut
    };

    if(timeOut > 0) obj["startTime"] = new Date().toString();

    var jsonStr = this.objectToJsonStr(obj);
    jsonStr = this.encode(jsonStr);

    this.clientTypeClassification(this, "writeToLocal", {key: key, jsonStr: jsonStr});
  }

  public writeToLocalApp(params)
  {
    this.storage.set(params['key'], params['jsonStr']);
  }

  public writeToLocalBrowser(params)
  {
    localStorage.setItem(params['key'], params['jsonStr']);
  }

  public readFromLocal(key)
  {
    var temp = this.clientTypeClassification(this, "readFromLocal", key);
    if(temp == null) return new Promise(resolve => resolve(null));

    return temp.then((jsonStr) => 
    { 
      if(jsonStr == null) return null;

      jsonStr = this.decode(jsonStr);

      var obj = this.jsonStrToObject(jsonStr);

      if(this.getLocalDataExpiration(obj))
        return obj.data;
      else
      {
        this.removeFromLocal(key);
        return null;
      }
    });
  }

  public readFromLocalBrowser(key)
  {
    return new Promise(resolve =>
    {
      var temp = localStorage.getItem(key);
      resolve(temp);
    });
  }

  public readFromLocalApp(key)
  {
    return this.storage.get(key);
  }

  public removeFromLocal(key)
  {
    this.writeToLocal(key, null);
  }

  private getLocalDataExpiration(obj)
  {
    if(obj.timeOut < 0) return true;

    var startTime = new Date(obj.startTime);
    var now = new Date();

    var interval = now.getTime() - startTime.getTime();

    return interval < obj.timeOut;
  }



  /****    Cryption Functions    ****/

  public encode(str)
  {
    return str;
  }

  public decode(str)
  {
    return str; 
  }



  /****    Gui Functions    ****/

  public getDeviceInfo()
  {
    if(BaseHelper.debug) alert("GeneralHelper.getDeviceInfo.1");

    var info = 
    {
      "cordova": this.device.cordova,
      "model": this.device.model,
      "platform": this.device.platform,
      "uuid": this.device.uuid,
      "version": this.device.version,
      "manufacturer": this.device.manufacturer,
      "isVirtual": this.device.isVirtual,
      "serial": this.device.serial
    };

    if(BaseHelper.debug) alert("GeneralHelper.getDeviceInfo.2");
    if(this.platform.is('ios')) 
      info['clientOs'] = "ios";
    else if(this.platform.is('android')) 
      info['clientOs'] = "android";
    else
      info['clientOs'] = "other";

    if(BaseHelper.debug) alert("GeneralHelper.getDeviceInfo.3");
    if(this.platform.is('desktop') || this.platform.is('mobileweb')) 
      info['clientType'] = "browser";
    else
      info['clientType'] = "app";
    
    if(BaseHelper.debug) alert("GeneralHelper.getDeviceInfo.4");
    return info;
  }

  public async startLoading(message = "Bekleyin...")
  {
    if(this.loading == null) this.loading = await this.loadingController.create({message: message}); 

    await this.loading.present();
  }

  public async stopLoading()
  {
    if(this.loading == null) setTimeout(() => $('ion-loading').remove(), 200);
    else await this.loading.dismiss();
  }



  /****    Media Functions    ****/

  public takePhoto(returnType = "imageUrl")
  {
    if(BaseHelper.debug) alert("GeneralHelper.takePhoto.1");

    var type = this.camera.DestinationType.FILE_URI;
    if(returnType == "base64Image") type = this.camera.DestinationType.DATA_URL;

    if(BaseHelper.debug) alert("GeneralHelper.takePhoto.1+");

    const options: CameraOptions = 
    {
      quality:50,
      targetHeight:600,
      targetWidth:600,
      destinationType: type,
      encodingType: this.camera.EncodingType.JPEG,
      mediaType: this.camera.MediaType.PICTURE,
      saveToPhotoAlbum: false
    }

    if(BaseHelper.debug) alert("GeneralHelper.takePhoto.2");

    var th = this;
    var temp = new Promise((resolve, reject) => 
    {
      if(BaseHelper.debug) alert("GeneralHelper.takePhoto.3A");
      
      this.camera.getPicture(options).then(image => 
      {
        if(BaseHelper.debug) alert("GeneralHelper.takePhoto.3A1");
        if(returnType == "base64Image") image = 'data:image/jpeg;base64,'+image;

        if(BaseHelper.debug) alert("GeneralHelper.takePhoto.3A2");
        resolve(image);        
        if(BaseHelper.debug) alert("GeneralHelper.takePhoto.3A3");
      },  
      err => 
      {
        if(BaseHelper.debug) alert("GeneralHelper.takePhoto.3B1");
        reject(err);
        if(BaseHelper.debug) alert("GeneralHelper.takePhoto.43B2");
      });

      if(BaseHelper.debug) alert("GeneralHelper.takePhoto.3C");
    });

    if(BaseHelper.debug) alert("GeneralHelper.takePhoto.4");

    return temp;
  }

  public getBlobFromBase64Image(base64: string) 
  {
    const info = this.getInfoFromBase64(base64);
    const sliceSize = 512;
    const byteCharacters = window.atob(info.rawBase64);
    const byteArrays = [];

    for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) 
    {
      const slice = byteCharacters.slice(offset, offset + sliceSize);
      const byteNumbers = new Array(slice.length);

      for (let i = 0; i < slice.length; i++)
        byteNumbers[i] = slice.charCodeAt(i);
      
      byteArrays.push(new Uint8Array(byteNumbers));
    }

    return new Blob(byteArrays, { type: info.mime });
  }

  public getInfoFromBase64(base64: string) 
  {
    const meta = base64.split(',')[0];
    const rawBase64 = base64.split(',')[1].replace(/\s/g, '');
    const mime = /:([^;]+);/.exec(meta)[1];
    const extension = /\/([^;]+);/.exec(meta)[1];

    return {
      mime,
      extension,
      meta,
      rawBase64
    };
  }


  /****    Location Function    ****/

  public getCurrentLocation()
  {
    return this.geolocation.getCurrentPosition()
    .catch((err) =>
    {
      alert(99556677);
      alert("getCurrentLocation.error.catch:"+this.objectToJsonStr(err));
      this.messageHelper.toastMessage("Konum alınırken hata oluştu: "+err.message, 10000);
    });
  }

  public trackCurrentLocation(callbackFunction)
  {
    let watch = this.geolocation.watchPosition();
    watch.subscribe(callbackFunction);
  }



  /****    General Functions ****/

  public sleep(ms)
  {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

  public clientTypeClassification(obj, functionName, params = null)
  {
    if(BaseHelper.debug) alert("GeneralHelper.clientTypeClassification.1");
    var info = this.getDeviceInfo();

    if(BaseHelper.debug) alert("GeneralHelper.clientTypeClassification.2");
    switch(info['clientType'])
    {
      case "app":
      case "browser":
        if(BaseHelper.debug) alert("GeneralHelper.clientTypeClassification.3");
        functionName += BaseHelper.ucfirst(info['clientType']);
        if(BaseHelper.debug) alert("GeneralHelper.clientTypeClassification.4:"+functionName);
        break;
      default:
        if(BaseHelper.debug) alert("GeneralHelper.clientTypeClassification.5");
        alert("undefined.clientType:"+info['clientType']+","+functionName+".clientTypeClassification");
        if(BaseHelper.debug) alert("GeneralHelper.clientTypeClassification.6");
        return;
    }

    if(BaseHelper.debug) alert("GeneralHelper.clientTypeClassification.7");
    if(params == null)
    {
      if(BaseHelper.debug) alert("GeneralHelper.clientTypeClassification.8");
      if(BaseHelper.debug) alert("GeneralHelper.clientTypeClassification.9"+(typeof obj[functionName]));
      return obj[functionName]();
    }
    else
    {
      if(BaseHelper.debug) alert("GeneralHelper.clientTypeClassification.10");      
      if(BaseHelper.debug) alert("GeneralHelper.clientTypeClassification.11"+(typeof obj[functionName]));
      return obj[functionName](params);
    }
  }




  /*public navigate(page:string, newPage = false)
  {
    if(page.substr(0, 1) != '/') 
      page = BaseHelper.angaryosUrlPath+"/"+page;

    if(newPage) 
    {
        page = BaseHelper.backendBaseUrl + "#" + page
        window.open(page);
    }
    else this.router.navigate([page]);
  }

  public getRange(r)
  {
    var rt = [];
    for(var i = 1; i <= parseInt(r); i++)
    {
        rt.push(i);
    }

    return rt;
  }*/
}