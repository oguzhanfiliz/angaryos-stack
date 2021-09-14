import { Component, TemplateRef } from '@angular/core';
import { Router } from '@angular/router';

import { BaseHelper } from './../helpers/base';
import { SessionHelper } from './../helpers/session';
import { MessageHelper } from './../helpers/message';
import { GeneralHelper } from './../helpers/general';
import { AeroThemeHelper } from './../helpers/aero.theme';

import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
 
declare var $: any;

@Component(
{
    selector: 'aero-root',
    styleUrls: ['./login.component.scss'],
    templateUrl: './login.component.html',
})
export class LoginComponent 
{
    public loading = false;
    public baseUrl = "";

    public user = 
    {
        "email": "iletisim@omersavas.com",
        "password": "1234Aa."
    };
    
    public isNative = null;

    constructor(
        private messageHelper: MessageHelper,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper,
        private router: Router
        )
    {
        if(BaseHelper.token.length > 0)
            //window.location.href = BaseHelper.dashboardUrl;
            this.generalHelper.navigate("dashboard");
            
        this.aeroThemeHelper.removeThemeClass();
        
        this.aeroThemeHelper.pageRutine();
        
        this.baseUrl = BaseHelper.backendBaseUrl + "#/";
        
        setTimeout(() => this.cookieControl(), 2000);
        
        if(BaseHelper.isAndroid || BaseHelper.isIos) this.isNative = true;
    }
    
    forgetPassword()
    {
        var th = this;
        
        Swal.fire(
        {
          title: 'Şifre Hatırlatıcı',
          html: `<input type="text" id="mail" class="swal2-input" autocomplete="off" placeholder="E-mail yada TC No">`,
          confirmButtonText: 'Şifremi Sıfırla',
          cancelButtonText: 'İptal',
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
            var mail = Swal.getPopup().querySelector('#mail')['value'];
            if(mail.length == 0) Swal.showValidationMessage(`Mail boş geçilemez`);
          }
        })
        .then( async (result) => 
        {
            if(typeof result["value"] == "undefined" || result["value"] == false) return;
            
            var mail = Swal.getPopup().querySelector('#mail')['value'];
            var url = BaseHelper.backendUrl+'public/missions/5?user='+mail;
            th.sessionHelper.doHttpRequest("GET", url) 
            .then((data) => 
            {
                if(data["message"] == "OK")
                    th.messageHelper.sweetAlert("Mail adresinize gönderilen link ile şifre sıfırlama yapabilirsiniz.", "Şifre Hatırlatma");
                else
                    th.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            })
            .catch((e) => 
            { 
                th.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            });
        });
    }

    cookieControl()
    {
        var c = BaseHelper.readFromLocal('cookieControl');
        if(c == null)
            this.messageHelper.sweetAlert("Bu portaldan en iyi şekilde faydalanabilmeniz için çerezler kullanılmaktadır. Bu portala giriş yaparak çerez kullanımını kabul etmiş sayılıyorsunuz. Daha fazla bilgi için 'Güvenlik politikası' ve 'Aydınlatma Metni' sayfamızı ziyaret edebilirsiniz", "Çerez ve Lokal Data Kullanımı");
        
        BaseHelper.writeToLocal('cookieControl', true);
    }

    validate()
    {
        if(this.user.password.length < 4)
        {
            this.messageHelper.toastMessage("Şifre en az 4 karakter olmalı", "warning");
            return false;
        }
        else if(this.user.email.length < 4)
        {
            this.messageHelper.toastMessage("Mail en az 4 karakter olmalı", "warning");
            return false;
        }
        
        return true;
    }

    doLogin() 
    {
        if(!this.validate()) return;

        this.loading = true;

        this.sessionHelper.login(this.user.email, this.user.password)
        .then((data) => 
        {
            BaseHelper.setToken(data["token"]);
            this.sessionHelper.fillLoggedInUserInfo()
            .then((data) =>
            {
                this.loading = false;
                this.loadScript();
                this.generalHelper.navigate("dashboard");
                //window.location.href = BaseHelper.dashboardUrl;
            })
            .catch((e) =>
            {
                this.loading = false;
            });
        })
        .catch((errorMessage) =>  
        {
            this.loading = false;
            if(errorMessage == "***") return;
            this.messageHelper.toastMessage("Doğrulama Hatası: "+errorMessage);
        });
    }
    
    loadScript()
    {
        BaseHelper.writeToPipe('loadPageScriptsLoaded', false);

        setTimeout(() => 
        {
            BaseHelper.writeToPipe('loadPageScriptsLightLoaded', false);
            this.aeroThemeHelper.loadPageScriptsLight();
        }, 500);
    }
    
    navigate(page, newPage = false)
    {
        this.generalHelper.navigate(page, newPage);
    }
}
