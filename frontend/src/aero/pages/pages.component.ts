import { Component } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser'
import { CommonModule } from "@angular/common";

import { environment } from './../../environments/environment';

import { BaseHelper } from './helpers/base';
import { MessageHelper } from './helpers/message';
import { SessionHelper } from './helpers/session';
import { GeneralHelper } from './helpers/general';
import { AeroThemeHelper } from './helpers/aero.theme';

declare var $: any;

@Component({
  selector: 'pages',
  templateUrl: 'pages.component.html'
})
export class PagesComponent 
{
  user = {};
  //searchIntervalId = -1;

  constructor(
        public messageHelper: MessageHelper,
        public sessionHelper: SessionHelper,
        public generalHelper: GeneralHelper,
        public aeroThemeHelper: AeroThemeHelper
        )
  { 
    this.fillVariablesFromLoggedInUserInfo();
    
    this.pageRutine();

    $('body').keydown((event) => this.keyEvent(event));
    $('body').keyup((event) => this.keyEvent(event));
    
    $(".page-loader-wrapper").fadeOut()
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
      this.aeroThemeHelper.updateBaseMenu();
    })
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

  isUserEditOwn()
  {
    if(typeof BaseHelper.loggedInUserInfo['auths'] == "undefined") return false;
    if(typeof BaseHelper.loggedInUserInfo['auths']['tables'] == "undefined") return false;
    if(typeof BaseHelper.loggedInUserInfo['auths']['tables']['users'] == "undefined") return false;
    if(typeof BaseHelper.loggedInUserInfo['auths']['tables']['users']['edits'] == "undefined") return false;
    
    return true;
  }

  getUserEditUrl()
  {
    return BaseHelper.baseUrl+"table/users/"+BaseHelper.loggedInUserInfo.user.id+"/edit";
  }
} 