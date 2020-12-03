import { environment } from './../../environments/environment';

import { BaseHelper } from './base';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { HTTP} from '@ionic-native/http/ngx';
import { MessageHelper } from './message';
import { GeneralHelper } from './general';

import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

@Injectable()
export class SessionHelper 
{     
    public disableDoHttpRequestErrorControl = false; 
    public doHttpRequestLastTime = 0;
    
    public backendBaseUrl:string = BaseHelper.backendBaseUrl;
    public backendUrl:string = "https://"+environment.host+"/api/v1/";
    
    public noImageUrl = BaseHelper.noImageUrl;

    public tokenTimeOut = 1000 * 60 * 60 * 24 * 5;
    

    public backendServiceIsAvailable = null;

    
    constructor(
      private httpClient: HttpClient,
      private http: HTTP,
      private messageHelper: MessageHelper,
      private generalHelper: GeneralHelper
    ) 
    {
      this.preLoad();      
    }

    public getPublicContents()
    {
      var url = this.backendUrl;
      url += "public/tables/public_contents";

      var data =
      {
        "page":1,
        "limit":5,
        "column_array_id":"0",
        "column_array_id_query":"0",
        "sorts":{},
        "filters":{}
      };

      return this.doHttpRequest("GET", url, {params: this.generalHelper.objectToJsonStr(data)})
      .then((data) => { return data["records"]; });
    }

    private dataInjectionInUrl(url, data)
    {
      if(data == null) return url;

      if(url.indexOf('?') == -1) url += "?";

      var keys = Object.keys(data);
      for(var i = 0; i < keys.length; i++)
        url += keys[i] + "=" + data[keys[i]] + "&";

      return encodeURI(url);
    }

    private getHttpObjectForBrowser(type:string, url:string, data:object)
    {
      if(BaseHelper.debug) alert("SessionHelper.getHttpObjectForBrowser.1");

      switch (type) 
      {
        case "GET": 
          if(BaseHelper.debug) alert("SessionHelper.getHttpObjectForBrowser.2");
          url = this.dataInjectionInUrl(url, data);
          if(BaseHelper.debug) alert("SessionHelper.getHttpObjectForBrowser.3");
          return this.httpClient.get(url);

        case "POST": 
          if(BaseHelper.debug) alert("SessionHelper.getHttpObjectForBrowser.4");
          return this.httpClient.post(url, data);

        case "PUT": 
          if(BaseHelper.debug) alert("SessionHelper.getHttpObjectForBrowser.5");
          return this.httpClient.put(url, data);

        case "DELETE": 
          if(BaseHelper.debug) alert("SessionHelper.getHttpObjectForBrowser.6");
          return this.httpClient.delete(url, data);
      }
    }

    private getHttpObjectForApp(type:string, url:string, data:object, options:object)
    {
      if(BaseHelper.debug) alert("SessionHelper.getHttpObjectForApp.1");

      switch (type) 
      {
        case "GET": 
          if(BaseHelper.debug) alert("SessionHelper.getHttpObjectForApp.2");
          url = this.dataInjectionInUrl(url, data);
          if(BaseHelper.debug) alert("SessionHelper.getHttpObjectForApp.3");
          return this.http.get(url, {}, options);

        case "POST": 
          if(BaseHelper.debug) alert("SessionHelper.getHttpObjectForApp.4");
          var temp = this.http.post(url, data, options);
          if(BaseHelper.debug) alert("SessionHelper.getHttpObjectForApp.4+");
          return temp;

        case "PUT": 
          if(BaseHelper.debug) alert("SessionHelper.getHttpObjectForApp.5");
          return this.http.put(url, data, options);

        case "DELETE": 
          if(BaseHelper.debug) alert("SessionHelper.getHttpObjectForApp.6");
          return this.http.delete(url, data, options);
      }
    }

    private async waitControlForHttpRequest()
    {
      do 
      {
        await this.generalHelper.sleep(50);
        var now = (new Date()).getTime();
      } while ((now - this.doHttpRequestLastTime) < 200);

      this.doHttpRequestLastTime = now;
    }

    public async doHttpRequest(type: string, url: string, data: object = {}, options: object = {})
    {
      if(BaseHelper.debug) alert("SessionHelper.doHttpRequest.1");
      await this.waitControlForHttpRequest();
      if(BaseHelper.debug) alert("SessionHelper.doHttpRequest.2");

      var temp = this.generalHelper.clientTypeClassification(this, "doHttpRequest", {type: type, url: url, data: data, options: options});
      if(BaseHelper.debug) alert("SessionHelper.doHttpRequest.3");
      return temp;
    }

    public doHttpRequestApp(params)
    {
      if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.1");
      this.generalHelper.startLoading();

      if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.1+");
      if(typeof params['options'] == "undefined") params['options'] = {};

      if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.1++");
      var temp = this.getHttpObjectForApp(params['type'], params['url'], params['data'], params['options'])

      if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.1+++");
      return temp.then((response) =>
      {
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.2A");
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.2A: "+this.generalHelper.objectToJsonStr(response));

        var data = null;
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.2B");

        try 
        {
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.2C");
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.2C:"+response["data"]);
          data = this.generalHelper.jsonStrToObject(response["data"]); 
          alert(data);
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.2D"); 
        }
        catch (e) 
        {
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.2E");
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.2E:"+response["data"]);
          data = {data: response["data"]};
        }
        
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.2F");
        this.generalHelper.stopLoading();
        
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.2G");
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.2G:"+data["data"]);
        return data["data"];
      })
      .catch((error) =>
      {
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.3A");
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.3A: "+this.generalHelper.objectToJsonStr(error));
        
        this.generalHelper.stopLoading();
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.3B");

        if(error["status"] < 1) 
        {
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.3C");
          this.messageHelper.sweetAlert("Sunucuyla iletişimde bir hata oldu: " + error["error"]);
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.3D");
          throw new Error(error["error"]);
        }
        
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.3E");

        try 
        {
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.3F");
          error["error"] = this.generalHelper.jsonStrToObject(error["error"]);
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.3G"); 
        }  catch (er) { }

        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.3H");
        var temp = this.doHttpRequestError(params['url'], error);
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.3I");
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestApp.3I:"+temp);
        
        throw new Error(temp);
      });
    }

    public doHttpRequestError(url, error)
    {
      if(BaseHelper.debug) alert("SessionHelper.doHttpRequestError.1");

      if(url.indexOf('initialize-db') > -1) return error.message;

      if(BaseHelper.debug) alert("SessionHelper.doHttpRequestError.2");
      if(this.redirectInitializeIfDbNotInitialized(error)) 
      {
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestError.3");
        return error.message;
      }          
      else if(this.redirectLoginPageIfTokenIsFail(error)) 
      {
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestError.4");
        return error.message;
      }           
      else if(this.alertIfErrorHaveServerMessage(error)) 
      {
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestError.5");
        return "***";
      }    

      if(BaseHelper.debug) alert("SessionHelper.doHttpRequestError.6");
      if(!this.disableDoHttpRequestErrorControl)
      {
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestError.7");
        this.messageHelper.sweetAlert("Sunucuyla iletişimde bir hata oldu: " + error.message);
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestError.8");
      } 

      if(BaseHelper.debug) alert("SessionHelper.doHttpRequestError.9");
      return error.message;
    }

    public doHttpRequestBrowser(params)
    {
      this.generalHelper.startLoading();

      return new Promise((resolve, reject) =>
      {
        if(BaseHelper.debug) alert("SessionHelper.doHttpRequestBrowser.1");
        this.getHttpObjectForBrowser(params['type'], params['url'], params['data'])
        .subscribe( 
        response => 
        {
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestBrowser.2A");
          this.generalHelper.stopLoading();

          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestBrowser.2B");
          resolve(response["data"]);

          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestBrowser.2C");
        },
        error =>
        {
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestBrowser.3A");
          this.generalHelper.stopLoading();
          
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestBrowser.3B");

          if(typeof error["error"] != "undefined")
            if(typeof error["error"]["text"] != "undefined")
              error = { message: error["message"]+": "+error["error"]["text"] };
          
          var temp = this.doHttpRequestError(params['url'], error);
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestBrowser.3C");
          if(temp != null) reject(temp);
          if(BaseHelper.debug) alert("SessionHelper.doHttpRequestBrowser.3D");
        });
      });  
    }

    private alertIfErrorHaveServerMessage(error)
    {
      if(this.disableDoHttpRequestErrorControl) return false; 
      
      if(typeof error.error != "undefined")
        if(typeof error.error.data != "undefined")
          if(typeof error.error.data.message != "undefined")
          {
            this.messageHelper.sweetAlert(this.convertFromHumanMessage(error.error.data.message), 'Hata', 'warning')
            return true;
          }

      return false;
    }

    private redirectLoginPageIfTokenIsFail(error)
    {
      if(typeof error.error != "undefined")
        if(typeof error.error.data != "undefined")
          if(typeof error.error.data.message != "undefined")
            if(error.error.data.message == "fail.token")
            {
              this.clearUserData();
              //this.generalHelper.navigate("/login");
              console.log("navigate.to.login")
              return true;
            }

      return false;
    }

    private redirectInitializeIfDbNotInitialized(error)
    {
      if(this.disableDoHttpRequestErrorControl) return false;
      
      if(typeof error.error != "undefined")
        if(typeof error.error.data != "undefined")
          if(typeof error.error.data.message != "undefined")
            if(error.error.data.message == "db.is.not.initialized")
            {
              this.messageHelper.toastMessage("Veritabanı Kullanılabilir Değil!");
              return true;
            }

      return false;
    }

    private convertFromHumanMessage(message)
    {
      var messages = 
      {
        "mail.or.password.incorrect": "Mail yada şifre hatalı",
        'no.auth': "Yetkiniz Yok!",
      }

      if(typeof messages[message] == "undefined") return message;

      return messages[message];
    }

    public clearUserData()
    {
      this.clearToken(); 
      this.clearLoggedInUserInfo();
    }

    public clearToken()
    {
      this.generalHelper.removeFromLocal("token");
      BaseHelper.setToken("");
    }

    public clearLoggedInUserInfo()
    {
      this.generalHelper.removeFromLocal("loggedInUserInfo");
      BaseHelper.setLoggedInUserInfo(null);
    } 

    private preLoad()
    {
      this.disableSslCheck();
      if(this.backendServiceIsAvailable == null) this.backendServiceControl();
    }

    private disableSslCheck()
    {
      this.generalHelper.clientTypeClassification(this, "disableSslCheck");
    }

    private disableSslCheckBrowser() 
    {

    }

    public disableSslCheckApp()
    {
      this.http.setServerTrustMode("nocheck");
    }

    private backendServiceControl()
    {
      return;
      setTimeout(() => 
      {
        this.doHttpRequest("GET", this.backendUrl, null)
        .then((data) => this.backendServiceIsAvailable = true)
        .catch((errorMessage) => this.messageHelper.sweetAlert("Sunucu servisleri şuan çalışmıyor olabilir. Sorun yaşarsanız bir süre sonra tekrar deneyin.", "Sunucuya erişilemedi"));
      }, 100);
    }

    public logout()
    {
      var url = this.getBackendUrlWithToken();
      if(url.length == 0) 
      {
        this.clearUserData();
        return;
      }

      this.disableDoHttpRequestErrorControl = true;
      return this.doHttpRequest("GET", url+"logOut", null) 
      .then((data) =>  
      {
        this.disableDoHttpRequestErrorControl = false;
        this.clearUserData();
      })
      .catch((e) =>
      {      
        this.disableDoHttpRequestErrorControl = false;
        this.clearUserData();
      })
    }

    public testInternetConnection()
    {
      return this.generalHelper.clientTypeClassification(this, "testInternetConnection");
    }

    testInternetConnectionApp()
    {
      var jsonUrl = "https://angular-http-guide.firebaseio.com/courses.json?orderBy=%22$key%22&limitToFirst=1";
      return this.http.get(jsonUrl, {}, {});
    }

    testInternetConnectionBrowser()
    {
      var jsonUrl = "https://angular-http-guide.firebaseio.com/courses.json?orderBy=%22$key%22&limitToFirst=1";
      
      return new Promise((resolve, reject) => 
      {
        this.httpClient.get(jsonUrl)
        .subscribe( 
        response => 
        {
          resolve(response);
        },
        error =>
        {
          reject(error.message);
        });
      });
    }

    public login(email:string, password:string)
    {
      if(BaseHelper.debug) alert("SessionHelper.login.1");
      var info = this.generalHelper.getDeviceInfo();
      
      if(BaseHelper.debug) alert("SessionHelper.login.2");
      var temp = this.doHttpRequest("POST", this.backendUrl+"login", 
      {
        email: email, 
        password: password,
        clientInfo: 
        {
          type: 'mobil',
          agent: info
        }
      });

      if(BaseHelper.debug) alert("SessionHelper.login.3");

      return temp;
    }

    public fillLoggedInUserInfo()
    {
      var url = this.getBackendUrlWithToken() + "getLoggedInUserInfo";

      return this.doHttpRequest("GET", url, null) 
      .then((data) =>  
      {
        this.setLoggedInUserInfo(data);
        return data;
      });
    }

    public setLoggedInUserInfo(info)
    {
      this.generalHelper.writeToLocal("loggedInUserInfo", info, this.tokenTimeOut)
      BaseHelper.setLoggedInUserInfo(info);
    }

    public getBackendUrlWithToken()
    {
      var token = BaseHelper.getToken();

      if(token.length == 0)
      {
        this.messageHelper.toastMessage("Giriş Yap!");
        return "";
      }

      return this.backendUrl + token + "/";
    }

    public setToken(token)
    {
      this.generalHelper.writeToLocal("token", token, this.tokenTimeOut)
      BaseHelper.setToken(token);
    }



    /*

    public userImitation(user)
    {
        var url = this.getBackendUrlWithToken()+"getUserToken/"+user.id;
        
        this.generalHelper.startLoading();

        this.doHttpRequest("GET", url)
        .then((data) => 
        {
            this.generalHelper.stopLoading();

            if(typeof data['token'] == "undefined")
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            else
                this.loginWithImitationUser(data['token']);
        })
        .catch((e) => { this.generalHelper.stopLoading(); });
    }

    private navigateToPage(page)
    {
      if(window.location.href.indexOf(page) == -1)
        this.generalHelper.navigate(page);

      setTimeout(() => {
        window.location.reload();
      }, 100);
    }

    public loginWithImitationUser(token)
    {
      BaseHelper.writeToLocal("realUserToken", BaseHelper.token);

      BaseHelper.setToken(token);
      this.fillLoggedInUserInfo()
      .then((data) =>
      {
        this.messageHelper.toastMessage("Kullanıcı taklit ediliyor");
        this.navigateToPage('dashboard');
      });
    }

    public logoutForImitationUser()
    {
      var url = this.getBackendUrlWithToken() + "logOut";

      return this.doHttpRequest("GET", url, null) 
      .then((data) =>  
      {
        BaseHelper.clearUserData();
        
        var token = BaseHelper.readFromLocal("realUserToken");
        BaseHelper.setToken(token);
        
        this.fillLoggedInUserInfo()
        .then((data) =>
        {
          BaseHelper.removeFromLocal("realUserToken");

          this.messageHelper.toastMessage("Gerçek kullanıcıya dönüldü");

          this.navigateToPage('table/users');
        });
        
      });
    }

    

    

    public tokenControl()
    {
      return this.doHttpRequest("GET", this.getBackendUrlWithToken(), null);  
    }

    public mapAuthControl()
    {
      if(BaseHelper.loggedInUserInfo == null) return false;
      if(typeof BaseHelper.loggedInUserInfo['auths'] == "undefined") return false;
      if(typeof BaseHelper.loggedInUserInfo['auths']['map'] == "undefined") return false;
      if(typeof BaseHelper.loggedInUserInfo['auths']['map'][0] == "undefined") return false;

      return true;
    }
    
    public recordImportAuthControl()
    {
      if(BaseHelper.loggedInUserInfo == null) return false;
      if(typeof BaseHelper.loggedInUserInfo['auths'] == "undefined") return false;
      if(typeof BaseHelper.loggedInUserInfo['auths']['admin'] == "undefined") return false;
      if(typeof BaseHelper.loggedInUserInfo['auths']['admin']['recordImport'] == "undefined") return false;

      return true;
    }
    
    public debugUserAuthControl()
    {
      if(BaseHelper.loggedInUserInfo == null) return false;
      if(typeof BaseHelper.loggedInUserInfo['debug_user'] == "undefined") return false;
      
      return BaseHelper.loggedInUserInfo['debug_user'];
    }

    public kmzAuthControl()
    {
      if(!this.mapAuthControl()) return false;
      if(typeof BaseHelper.loggedInUserInfo['auths']['map']['kmz'] == "undefined") return false;

      return true;
    }

    public getLoggedInUserInfo()
    {
      if(BaseHelper.loggedInUserInfo == null) 
        return this.fillLoggedInUserInfo();

      return this.tokenControl()
      .then((data) =>
      {
        return BaseHelper.loggedInUserInfo;
      });
    }

    public toSeo(str) 
    {
      str = str.replace(/ /g, "_");
      str = str.replace(/</g, "");
      str = str.replace(/>/g, "");
      str = str.replace(/"/g, "");
      str = str.replace(/é/g, "");
      str = str.replace(/!/g, "");
      str = str.replace(/’/, "");
      str = str.replace(/£/, "");
      str = str.replace(/^/, "");
      str = str.replace(/#/, "");
      str = str.replace(/$/, "");
      str = str.replace(/\+/g, "");
      str = str.replace(/%/g, "");
      str = str.replace(/½/g, "");
      str = str.replace(/&/g, "");
      str = str.replace(/\//g, "");
      str = str.replace(/{/g, "");
      str = str.replace(/\(/g, "");
      str = str.replace(/\[/g, "");
      str = str.replace(/\)/g, "");
      str = str.replace(/]/g, "");
      str = str.replace(/=/g, "");
      str = str.replace(/}/g, "");
      str = str.replace(/\?/g, "");*/
      //str = str.replace(/\*/g, "");
      /*str = str.replace(/@/g, "");
      str = str.replace(/€/g, "");
      str = str.replace(/~/g, "");
      str = str.replace(/æ/g, "");
      str = str.replace(/ß/g, "");
      str = str.replace(/;/g, "");
      str = str.replace(/,/g, "");
      str = str.replace(/`/g, "");
      str = str.replace(/|/g, "");
      str = str.replace(/\./g, "");
      str = str.replace(/:/g, "");
      str = str.replace(/İ/g, "i");
      str = str.replace(/I/g, "i");
      str = str.replace(/ı/g, "i");
      str = str.replace(/ğ/g, "g");
      str = str.replace(/Ğ/g, "g");
      str = str.replace(/ü/g, "u");
      str = str.replace(/Ü/g, "u");
      str = str.replace(/ş/g, "s");
      str = str.replace(/Ş/g, "s");
      str = str.replace(/ö/g, "o");
      str = str.replace(/Ö/g, "o");
      str = str.replace(/ç/g, "c");
      str = str.replace(/Ç/g, "c");
      str = str.replace(/–/g, "_");
      str = str.replace(/—/g, "_");
      str = str.replace(/—-/g, "_");
      str = str.replace(/—-/g, "_");

      return str.toLowerCase();
    }*/ 
}