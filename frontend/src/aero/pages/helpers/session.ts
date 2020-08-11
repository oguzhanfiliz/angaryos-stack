import { BaseHelper } from './base';
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

import { MessageHelper } from './message';
import { GeneralHelper } from './general';

import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

@Injectable()
export class SessionHelper 
{     
    disableDoHttpRequestErrorControl = false; 
    
    constructor(
      private httpClient: HttpClient,
      private messageHelper: MessageHelper,
      private generalHelper: GeneralHelper) 
    {
      this.preLoad();
    }

    private preLoad()
    {
      if(BaseHelper.backendServiceControl == null)
        this.backendServiceControl();
    }

    private backendServiceControl()
    {
      setTimeout(() => 
      {
        this.doHttpRequest("GET", BaseHelper.backendUrl, null)
        .then((data) => BaseHelper.backendServiceControl = true)
        .catch((errorMessage) => this.messageHelper.sweetAlert("Sunucu servisleri şuan çalışmıyor olabilir. Sorun yaşarsanız bir süre sonra tekrar deneyin.", "Sunucuya erişilemedi"));
      }, 100);
    }

    public getBackendUrlWithToken()
    {
      if(BaseHelper.token.length == 0) this.generalHelper.navigate("/login");

      return BaseHelper.backendUrl + BaseHelper.token + "/";
    }

    private getHttpObject(type:string, url:string, data:object)
    {
      switch (type) 
      {
        case "GET": 
          url = this.dataInjectionInUrl(url, data);
          return this.httpClient.get(url);
        case "POST": return this.httpClient.post(url, data);
        case "PUT": return this.httpClient.put(url, data);
        case "DELETE": return this.httpClient.delete(url, data);
      }
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

    private redirectLoginPageIfTokenIsFail(error)
    {
      if(typeof error.error != "undefined")
        if(typeof error.error.data != "undefined")
          if(typeof error.error.data.message != "undefined")
            if(error.error.data.message == "fail.token")
            {
              BaseHelper.clearUserData();
              this.generalHelper.navigate("/login");
              return true;
            }

      return false;
    }

    private initializeDBConfirmation()
    {
      Swal.fire(
      {
        title: 'İlk kurulum',
        text: "Veritabanı daha önce kurulmamış. Şimdi ilk kurulumu yapmak ister misiniz? Dikkat edin! bu işlem tüm veritabanını siler yeniden oluşturur!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet şimdi yapacağım!'
      })
      .then((result) => 
      {
        if (result.value) 
        {
          this.messageHelper.toastMessage('Bu işlem zaman alabilir tamamlandığında size bildirilecek...');

          this.doHttpRequest("GET", BaseHelper.backendUrl + "initializeDb")
          .then((data) =>
          {
            var message = "Tebrikler kurulum başarılı!"
              
            Swal.fire("Başarılı!", message, "success");
          })
          .catch((errorMessage) =>
          {
            var message = "Malesef veritabanı başlatılamadı! Tarayıcınızın geliştirici araçlarından ";
            message += " network geçmişinize bakabilir yada destek sayfamızı ziyaret edebilirsiniz.";
              
            Swal.fire("Yapılamadı!", message, "warning");
          });
        }
      });
    }

    private redirectInitializeIfDbNotInitialized(error)
    {
      if(this.disableDoHttpRequestErrorControl) return false;
      
      if(typeof error.error != "undefined")
        if(typeof error.error.data != "undefined")
          if(typeof error.error.data.message != "undefined")
            if(error.error.data.message == "db.is.not.initialized")
            {
              this.initializeDBConfirmation();
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

    public doHttpRequest(type: string, url: string, data: object = {})
    {
      this.generalHelper.startLoading();
      
      return new Promise((resolve, reject) =>
      {
        this.getHttpObject(type, url, data)
        .subscribe( 
        response => 
        {
          this.generalHelper.stopLoading();
          resolve(response["data"]);
        },
        error =>
        {
          this.generalHelper.stopLoading();
          
          if(url.indexOf('initialize-db') > -1) reject(error.message);

          if(this.redirectInitializeIfDbNotInitialized(error)) 
          {
            reject(error.message);
            return;
          }          
          else if(this.redirectLoginPageIfTokenIsFail(error)) 
          {
            reject(error.message);
            return;
          }           
          else if(this.alertIfErrorHaveServerMessage(error)) 
          {
            reject("***");
            return;
          }    

          if(!this.disableDoHttpRequestErrorControl)
            this.messageHelper.sweetAlert("Sunucuyla iletişimde bir hata oldu: " + error.message);
            
          reject(error.message);
        });
      });
    }

    public login(email:string, password:string)
    {
      return this.doHttpRequest("POST", BaseHelper.backendUrl+"login", 
      {
        email: email, 
        password: password,
        clientInfo: 
        {
          type: 'browser',
          agent: navigator.userAgent
        }
      });
    }

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

    public logout()
    {
      var url = this.getBackendUrlWithToken() + "logOut";

      return this.doHttpRequest("GET", url, null) 
      .then((data) =>  
      {
        BaseHelper.clearUserData()
        this.generalHelper.navigate('/login');
      })
    }

    public fillLoggedInUserInfo()
    {
      var url = this.getBackendUrlWithToken() + "getLoggedInUserInfo";

      return this.doHttpRequest("GET", url, null) 
      .then((data) =>  
      {
        BaseHelper.setLoggedInUserInfo(data);
        return data;
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
      str = str.replace(/\?/g, "");
      str = str.replace(/\*/g, "");
      str = str.replace(/@/g, "");
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
    }
}