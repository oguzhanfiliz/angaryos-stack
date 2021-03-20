import { Component } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser'
import { CommonModule } from "@angular/common";

import { environment } from './../../environments/environment';

import { BaseHelper } from './helpers/base';
import { DataHelper } from './helpers/data';
import { MessageHelper } from './helpers/message';
import { SessionHelper } from './helpers/session';
import { GeneralHelper } from './helpers/general';
import { AeroThemeHelper } from './helpers/aero.theme';

import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

declare var $: any;

@Component({
  selector: 'pages',
  styleUrls: ['pages.component.scss'],
  templateUrl: 'pages.component.html'
})
export class PagesComponent 
{
  user = {};
  appName = "";
  profilePictureUrl = ""
  userEditUrl = ""
  //searchIntervalId = -1;

  eSigns = [];
  isESignUserTrue = false;
  eSignTimeOut = 1000 * 60 * 5;

  constructor(
        public messageHelper: MessageHelper,
        public sessionHelper: SessionHelper,
        public generalHelper: GeneralHelper,
        public aeroThemeHelper: AeroThemeHelper
        )
  { 
    this.fillVariablesFromLoggedInUserInfo();
    
    this.pageRutine();

    this.appName = environment.appName.charAt(0).toUpperCase() + environment.appName.slice(1);

    $('body').keydown((event) => this.keyEvent(event));
    $('body').keyup((event) => this.keyEvent(event));
    
    $(".page-loader-wrapper").fadeOut();

    this.eSignServerOperations();

    BaseHelper.writeToPipe('basePageComponent', this);
  }
  
  async eSignServerOperations()
  {   
    var socket = this.sessionHelper.getConnectedeSignSocket()
    var th = this;

    socket.onopen = async function() 
    {
      await BaseHelper.sleep(500);
      th.sessionHelper.sendESignMessage(BaseHelper.objectToJsonStr({type: "connectionTest"}));
    };
      
      
    socket.onmessage = async function(e) 
    {
      var delimeter = "@@@";

      console.log("soketten gelen: " + e.data);

      var data = BaseHelper.jsonStrToObject(e.data);

      await BaseHelper.sleep(500);

      switch(data['type'])
      {
        case "connectionSuccess":          
          th.sessionHelper.sendESignMessage(BaseHelper.objectToJsonStr({type: "getUserTc"}));
          break;
        case "doESignSuccess":   
          console.log("ok id:" +data["recordId"]);

          for(var i = 0; i < th.eSigns.length; i++)
            if(th.eSigns[i]['id'] == data["recordId"]) 
            {
              th.eSigns.splice(i, 1);
              th.messageHelper.sweetAlert("İmzalama başarılı", "Tamamlandı", "success");
              break;
            }
          break;
        case "doESignError":   
          console.log("error id:" +data["recordId"]);
          th.messageHelper.sweetAlert("İmzalama esnasında hata oluştu!", "Hata!", "error");
          break;
        case "returnTc":    
          if(BaseHelper.loggedInUserInfo.user.tc == data["tc"]) 
            th.isESignUserTrue = true;          
          else 
            alert("E imza uygulaması çalışıyor ama takılı olan cihaz size ait değil!");
          break;
      }
      
    };
  
    socket.onclose = function() 
    { 
      console.log("soket kapandi"); 
      th.sessionHelper.socket = null;
      th.isESignUserTrue = false;
    };
  }

  async eSignOperations()
  {
    if(typeof this.user['id'] == "undefined") return;

    var key = "user:"+this.user['id']+".eSigns";
    var eSigns = BaseHelper.readFromLocal(key);
    
    if(eSigns == null || eSigns.length == 0) await this.fillESigns();
    else this.eSigns = eSigns;

    this.eSignControl();
  }

  eSignControl()
  {
    if(this.eSigns.length == 0) return;

    var rememberKey = "user:"+this.user['id']+".eSignRemember";

    if(!this.isESignUserTrue)
    {
      this.messageHelper.sweetAlert("E-imza programına erişim yok! Programı başka bir uygulama yada sekme kullanıyor olabilir. Tüm sekmeleri ve uygulamarı kapatıp bu pencereyi yenilemeyi deneyin.", "Hata", "error");
      return
    }

    var sign = this.eSigns[0];

    var remembered = BaseHelper.readFromLocal(rememberKey);
    if(remembered == null) remembered = "";

    var checked = "";
    if(remembered.length > 0) checked = 'checked';

    Swal.fire(
    {
      title: 'Elektronik İmza',
      html: `<br><p style="text-align: justify;"> `+sign['signed_text']+` </p><br>
      <input value="`+remembered+`" type="password" id="ePassword" class="swal2-input" autocomplete="off" placeholder="E-imza şifreniz"><br>
      <input `+checked+` type="checkbox" name="eRemember" id="eRemember"> Hatırla`,
      confirmButtonText: 'İmzala',
      cancelButtonText: 'İmzalamayı Reddet',
      customClass: 
      {
        confirmButton: 'btn btn-success',
        cancelButton: 'btn btn-danger'
      },
      buttonsStyling: false,
      showCloseButton: true,
      showCancelButton: true,
      focusConfirm: true,
      preConfirm: () => 
      {
        var password = Swal.getPopup().querySelector('#ePassword')['value'];
        if(password.length == 0) Swal.showValidationMessage(`E-imza boş geçilemez`);
      }
    })
    .then((result) => 
    {
      if(typeof result["value"] == "undefined" || !result["value"]) 
      {
        if(result['dismiss'].toString() != 'cancel') return;

        this.sessionHelper.eSignCancel(sign)
        .then((control) => 
        {
          if(control) this.eSigns.splice(0,1);
          var key = "user:"+this.user['id']+".eSigns";
          BaseHelper.writeToLocal(key, this.eSigns, this.eSignTimeOut);
        });
        return;
      }

      var password = Swal.getPopup().querySelector('#ePassword')['value'];
      var remember = Swal.getPopup().querySelector('#eRemember');
      
      if(remember['checked']) BaseHelper.writeToLocal(rememberKey, password);
      else BaseHelper.removeFromLocal(rememberKey);

      this.sessionHelper.doESign(sign, password); 
    });

    if(remembered.length == 0) 
      setTimeout(() => {
        $('#ePassword').val("")
      }, 750);
  }

  async fillESigns()
  {
    var url = this.sessionHelper.getBackendUrlWithToken()+"tables/e_signs";

    var params = 
    {
      "page":1,
      "limit":10,
      "sorts":{ "id":true },
      "filters":
      {
        "state":
        {
          "type":1,
          "guiType":"boolean",
          "filter":true
        },
        "signed_at":
        {
          "type":100,
          "guiType":"datetime",
          "filter":null
        },
        "own_id":
        {
          "type":1,
          "guiType":"multiselect",
          "filter":[ BaseHelper.loggedInUserInfo.user['id'].toString() ]
        }
      }
    }

    var auth = BaseHelper.loggedInUserInfo.auths['tables']['e_signs'];
           
    var listId = auth["lists"][0];
    var queryId = listId;
    if(typeof auth['queries'] != "undefined" && typeof auth['queries'][0] != "undefined")
        queryId = auth['queries'][0]
        
    params['column_array_id'] = listId;
    params['column_array_id_query'] = queryId;

    var th = this;
    await this.sessionHelper.doHttpRequest("POST", url, {"params": BaseHelper.objectToJsonStr(params)}) 
    .then((data) => 
    {
      this.eSigns = data['records'];

      var key = "user:"+this.user['id']+".eSigns";
      BaseHelper.writeToLocal(key, this.eSigns, this.eSignTimeOut);  

      if(this.eSigns.length > 0) th.eSignControl() 
    })
    .catch((e) => {  });
  }
  
  private pageRutine() 
  {        
      setTimeout(() =>
      {
        $('#shortcuts').removeClass('show');
        $('#shortcuts ul').removeClass('show');
        
        var th = this;
        $('#importRecordFile').change(() => th.importRecodrFileChanged());
        
      }, 1000);
  }
  
  private importRecodrFileChanged()
  {
    var path = $('#importRecordFile').val();
    if(path == "") return;

    var arr = path.split('.');
    var ext = arr[arr.length-1];

    if(ext == 'json')
      this.importRecord();
    else
      this.messageHelper.sweetAlert("Geçersiz doya tipi!", "Hata", "warning");
  }

  private keyEvent(event)
  {
    var keys = ['altKey', 'ctrlKey', 'shiftKey'];

    for(var i = 0; i < keys.length; i++)
      BaseHelper['pipe'][keys[i]] = event[keys[i]];
  }

  private fillVariablesFromLoggedInUserInfo()
  {
    this.sessionHelper.getLoggedInUserInfo()
    .then((data) =>
    {
      this.user = data['user'];
      if(this.user['email'] == null || this.user['email'].length == 0) this.user['email'] = this.user['tc'];
      
      this.fillProfilePictureUrl();
      this.fillUserEditUrl();
      
      this.aeroThemeHelper.updateBaseMenu();
      
      setTimeout(() => 
      {
        this.eSignOperations();
      }, 3000);
    })
  }

  fillProfilePictureUrl()
  {
    if(this.user['profile_picture'] == null) 
    {
      this.profilePictureUrl = BaseHelper.noImageUrl;
      return;
    }

    var temp = BaseHelper.jsonStrToObject(this.user['profile_picture']);
    this.profilePictureUrl = BaseHelper.getFileUrl(temp[0], '');
  }

  searchInMenuInputChanged(event) 
  {
    var params =
    {
        event: event,
        th: this
    };

    function func(params)
    {
        params.th.aeroThemeHelper.updateBaseMenu(params.event.target.value);
    }

    return BaseHelper.doInterval('searchInBaseMenu', func, params, 500);
  }
  
  menuItemClick(func)
  {
    switch(func)
    {
      case 'importRecord':
        this.importRecordLinkClicked();
        break;
      case 'openBackendLogs':
        this.openBackendLogs();
        break;
      default:
        console.log(func);
    }
  }
  
  additionalLinkClicked(additionalLink)
  {
    DataHelper.loadAdditionalLinkPayload(this, additionalLink);
    
    var url = DataHelper.getUrlFromAdditionalLink(additionalLink);
    
    if(additionalLink['open_new_window']) window.open(url);
    else window.location.href = url;
  }
  
  openBackendLogs()
  {
    window.open(this.sessionHelper.getBackendUrlWithToken()+"logs");
  }
  
  importRecordLinkClicked()
  {
    $('#importRecordFile').click();
  }
  
  
  importRecord()
  {
    var url = this.sessionHelper.getBackendUrlWithToken()+"importRecord";

    var params = new FormData();
    var files = $('#importRecordFile')[0].files;
    for(var l = 0; l < files.length; l++)
        params.append("files[]", files[l]);

    this.generalHelper.startLoading();

    this.sessionHelper.doHttpRequest("POST", url, params) 
    .then((data) => 
    {
        $('#importRecordFile').val("");
        this.generalHelper.stopLoading();

        if(data == null)
            this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
        else 
        {
            console.log(data);
            if(typeof data['data'] == "undefined")
                this.messageHelper.sweetAlert('Beklenmedik bir hata oluştu!', 'İçe Aktarma', 'error');
            else if(typeof data['error'] == "undefined")
                this.messageHelper.sweetAlert('Beklenmedik bir hata oluştu!', 'İçe Aktarma', 'error');
            else if(Object.keys(data['error']).length > 0)
                this.messageHelper.sweetAlert('İçe aktarma tamamlandı ama bazı hatalar oluştu!', 'İçe Aktarma', 'error');
            else 
                this.messageHelper.sweetAlert('İşlem başarı ile gerçekleştirildi', 'İçe Aktarma', 'success');
        }
    })
    .catch((e) => 
    { 
        $('#importRecordFile').val("");
        this.generalHelper.stopLoading(); 
    });
  }

  clearMenuFilter()
  {
    $('#menuFilter').val("");
    this.aeroThemeHelper.updateBaseMenu("");
  }

  logout()
  {
    var token = BaseHelper.readFromLocal('realUserToken');
    if(token != null) this.sessionHelper.logoutForImitationUser();
    else this.sessionHelper.logout();
  }

  getLogoutButtonClass ()
  {
    var cls = 'mega-menu';
    var token = BaseHelper.readFromLocal('realUserToken');
    if(token != null) cls += " imitation-user-logout-button";

    return cls;
  }

  ngAfterViewInit() 
  {    
    this.aeroThemeHelper.loadPageScripts(); 
  }

  search()
  {
    var words = $('#searchWords').val();
    if(words == null || words.length == 0)
    {
      this.messageHelper.toastMessage("Aramak için birşeyler yazmalısınız!");
      return;
    }

    window.location.href = BaseHelper.baseUrl+"search/"+words;
    window.location.reload();
  }

  changeTheme(name)
  {
    this.aeroThemeHelper.setTheme(name);
  }

  isCurrentTheme(name)
  {
    var theme = this.aeroThemeHelper.getThemeClass();
    return theme == ('theme-'+name)
  }

  getUserEditUrl()
  {
    return BaseHelper.baseUrl+"table/users/"+BaseHelper.loggedInUserInfo.user.id+"/edit";
  }

  getAppName()
  {
    return environment.appName.charAt(0).toUpperCase() + environment.appName.slice(1);
  }

  getProfilePictureUrl()
  {
    if(this.user['profile_picture'] == null) return BaseHelper.noImageUrl;

    var temp = BaseHelper.jsonStrToObject(this.user['profile_picture']);
    return BaseHelper.getFileUrl(temp[0], '');
  }

  isUserEditOwn()
  {
    if(typeof BaseHelper.loggedInUserInfo == "undefined") return false;
    if(typeof BaseHelper.loggedInUserInfo['auths'] == "undefined") return false;
    if(typeof BaseHelper.loggedInUserInfo['auths']['tables'] == "undefined") return false;
    if(typeof BaseHelper.loggedInUserInfo['auths']['tables']['users'] == "undefined") return false;
    if(typeof BaseHelper.loggedInUserInfo['auths']['tables']['users']['edits'] == "undefined") return false;
    
    return true;
  }

  fillUserEditUrl()
  {
    this.userEditUrl = BaseHelper.baseUrl+"table/users/"+BaseHelper.loggedInUserInfo.user.id+"/edit";
  }

  additionalLinkClicked(additionalLink)
  {
    DataHelper.loadAdditionalLinkPayload(this, additionalLink);
    
    var url = DataHelper.getUrlFromAdditionalLink(additionalLink);
    if(url == null || url.length == 0) return;
    
    if(additionalLink['open_new_window']) window.open(url);
    else window.location.href = url;
  }
} 