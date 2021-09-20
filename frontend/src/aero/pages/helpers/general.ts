import { BaseHelper } from './base';
import { Injectable } from '@angular/core';
import { Location } from '@angular/common'
import { Router } from '@angular/router';
import { Platform } from '@ionic/angular';

import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

declare var $: any;

@Injectable()
export class GeneralHelper 
{     
  constructor(
    public router: Router,
    public platform: Platform,
    public location: Location
    )
  { }
  
  public fillPlatformVariables()
  {
      BaseHelper.isAndroid = this.platform.is("android");
      BaseHelper.isIos = this.platform.is("ios");
      BaseHelper.isBrowser = this.platform.is("desktop");
      
      console.log("a: "+BaseHelper.isAndroid+", i: "+BaseHelper.isIos+", b: "+BaseHelper.isBrowser)
  }
  
  public goBackPage()
  {
    this.location.back()
  }

  public pageNormalizeForNavigate(page)
  {
    if(page.substr(0, 1) != '/' && page.indexOf('://') == -1) 
      page = BaseHelper.angaryosUrlPath+"/"+page;

    return page;
  }

  public navigateNewPage(page)
  {
    if(page.indexOf('://') == -1) page = BaseHelper.backendBaseUrl + "#" + page;        
    if(BaseHelper.isBrowser) window.open(page);
    else window.open(page, '_system', 'location=yes');
  }
  
  public navigateWebUrl(page)
  {
    if(BaseHelper.isBrowser) window.location.href = page;
    else window.open(page, '_system', 'location=yes');
  }

  public navigateAngaryosPage(page)
  {
    this.router.navigateByUrl(page);

    if(!BaseHelper.isAndroid && !BaseHelper.isIos) return;
  
    $('#leftsidebar').removeClass('open');
    $('section').css('margin-right', '0');
    $('.navbar-nav').css('right', '-40');
  }

  public saveLastPage(page)
  {
    if(page.indexOf('/login') > -1) return;
    if(page.indexOf('dashboard') > -1) return;
    if(page.indexOf('/home') > -1) return;
    if(page.indexOf(BaseHelper.angaryosUrlPath+"/") == -1) return;

    page = page.replace(BaseHelper.backendBaseUrl + "#", '');
    if(page.substr(0, BaseHelper.angaryosUrlPath.length+1) == BaseHelper.angaryosUrlPath+"/") page = "/"+page;

    if(BaseHelper.loggedInUserInfo == null) return
    var key = 'user:'+BaseHelper.loggedInUserInfo['user']["id"]+".lastPage"; 
    BaseHelper.writeToLocal(key, page);

    console.log("tut: " + page);
  }

  public navigate(page:string, newPage = false)
  {
    if(BaseHelper.pipe["ctrlKey"]) newPage = true;

    page = this.pageNormalizeForNavigate(page);
    
    this.saveLastPage(page);

    if(newPage) 
      this.navigateNewPage(page);
    else if(page.indexOf('://') > 0)
      this.navigateWebUrl(page);
    else
      this.navigateAngaryosPage(page)
  }

  public getRange(r)
  {
    var rt = [];
    for(var i = 1; i <= parseInt(r); i++)
    {
        rt.push(i);
    }

    return rt;
  }

  public startLoading()
  {
    const Toast = Swal.mixin(
    {
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 1000 * 60 * 15
    })

    Toast.fire(
    {
        icon: "info",
        title: "Bekleyin..." 
    });
  }

  public stopLoading()
  {
    var msg = $('#swal2-title').html();
    if(msg != "Bekleyin...") return;
    
    //Swal.isVisible()
    const Toast = Swal.mixin(
    {
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 100
    })

    Toast.fire(
    {
        icon: "success",
        title: "Tamamlandı..." 
    });
  }
}