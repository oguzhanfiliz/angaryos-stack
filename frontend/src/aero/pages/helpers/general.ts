import { BaseHelper } from './base';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';

import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

declare var $: any;

@Injectable()
export class GeneralHelper 
{     
  constructor(private router: Router) { }

  public navigate(page:string, newPage = false)
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