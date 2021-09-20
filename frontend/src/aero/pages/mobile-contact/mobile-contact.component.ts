import { ActivatedRoute} from '@angular/router';
import { Component } from '@angular/core';
import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { MessageHelper } from './../helpers/message';
import { AeroThemeHelper } from './../helpers/aero.theme'; 

declare var $: any;

@Component(
{
    selector: 'mobile-contact',
    styleUrls: ['./mobile-contact.component.scss'],
    templateUrl: './mobile-contact.component.html',
})
export class MobileContactComponent
{
    baseUrl = '';
    
    constructor(
        private route: ActivatedRoute,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper,
        private messageHelper: MessageHelper
        )
    {
        if(BaseHelper.isBrowser) 
        {
            this.generalHelper.navigate('/login');
            return;
        }
        
        this.baseUrl = BaseHelper.backendBaseUrl; 
        
        this.aeroThemeHelper.pageRutine();
        
        var th = this;
        setTimeout(() =>
        {
            th.aeroThemeHelper.pageRutine();
        }, 500);
    }
    
    call() 
    {
        window.location.href = "tel:+905554443355";
    }
    
    mail()
    {
        window.location.href = "mailto:iletisim@omersavas.com";
    }
    
    navigate(page) 
    { 
        this.generalHelper.navigate(page);
    }
    
    mapNavigate() 
    {
        window.open("maps:?q=39.4191108,29.9621009");
    }
    
    openFeedBackModal()
    {
        $('#feedBackModal').modal('show');
    }
    
    getValidatedFeedbackData()
    {
        var nameSurname = $('#name_surname').val();
        var phone = $('#phone').val();
        var email = $('#email_basic').val();
        var description = $('#description').val();
        
        if(nameSurname == null || nameSurname.length == 0)
        {
            this.messageHelper.sweetAlert("Ad soyad boş geçilemez!", "Hata", "warning");
            return null;
        }
        
        if(phone == null || phone.length < 10)
        {
            this.messageHelper.sweetAlert("Telefon numarası doğru girilmelidir!", "Hata", "warning");
            return null;
        }
        
        if(email == null || email.length < 5 || email.indexOf('.') == -1 || email.indexOf('@') == -1)
        {
            this.messageHelper.sweetAlert("E-mail doğru girilmelidir!", "Hata", "warning");
            return null;
        }
        
        if(description == null || description.length < 10)
        {
            this.messageHelper.sweetAlert("Açıklama en az 10 karakter olmalıdır!", "Hata", "warning");
            return null;
        }
        
        return {
            'name_surname': nameSurname,
            'phone': phone,
            'email_basic': email,
            'description': description,
        }
    }
    
    sendFeedback()
    {
        var colDisplayNames = {
            "name_surname": "Ad Soyad",
            "email_basic": "Email",
            "phone": "Telefon",
            "description": "Açıklama",
        };
        
        var data = this.getValidatedFeedbackData();
        if(data == null) return;
        
        data['column_set_id'] = 0;
        data['state'] = 1;
        data['id'] = 0;
        
        var url = BaseHelper.backendUrl+"public/tables/feedbacks/store";
        this.generalHelper.startLoading();
        $('#sendFeedbackButton').addClass('disabled')
        
        this.sessionHelper.doHttpRequest("POST", url, data) 
        .then((data) => 
        {
            this.generalHelper.stopLoading();
            $('#sendFeedbackButton').removeClass('disabled')
            
            if(typeof data['message'] == "undefined")
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!", "Hata", "warning");
            else if(data['message'] == 'error')
            {
                var keys = Object.keys(data['errors']);
                for(var i = 0; i < keys.length; i++)
                {
                    var colName = keys[i];
                    var err = data['errors'][colName].join(', ');
                    
                    this.messageHelper.sweetAlert("Formda hata var! <br><br> "+colDisplayNames[colName]+": "+err, "Hata", "warning");
                    break;
                }
            }
            else if(data['message'] == 'success')
                this.sendFeedbackSuccess(data);
            else
                this.messageHelper.sweetAlert("Beklenmedik cevap geldi!!", "Hata", "warning");
        })
        .catch((e) => 
        { 
            this.generalHelper.stopLoading();
            $('#sendFeedbackButton').removeClass('disabled');
            
            if(e != '***') this.messageHelper.sweetAlert("Beklenmedik bir hata oluştu! (catch)", "Hata", "warning");
        });
    }
    
    sendFeedbackSuccess(data)
    {
        $('#name_surname').val("");
        $('#phone').val("");
        $('#email_basic').val("");
        $('#description').val("");
        
        $('#feedBackModal').modal('hide');
        
        this.messageHelper.sweetAlert("Geri bildiriminiz başarı ile iletilmiştir", "Başarı", "success");
        
    }
}