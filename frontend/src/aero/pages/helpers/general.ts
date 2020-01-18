import { BaseHelper } from './base';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';

declare var $: any;

@Injectable()
export class GeneralHelper 
{     
  constructor(private router: Router) { }

  public navigate(page:string)
  {
    if(page.substr(0, 1) != '/') 
      page = BaseHelper.angaryosUrlPath+"/"+page;
  console.log(page)
    this.router.navigate([page]);
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
    $('#loading').html("Yükleniyor...");
    $('#loading').fadeIn(500);
  }

  public stopLoading()
  {
    $('#loading').html("Yüklendi!");
    $('#loading').fadeOut(500);
  }
}