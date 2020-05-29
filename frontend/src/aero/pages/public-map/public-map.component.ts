import { Component, TemplateRef } from '@angular/core';
import { Router } from '@angular/router';

import { BaseHelper } from './../helpers/base';
import { MessageHelper } from './../helpers/message';
import { SessionHelper } from './../helpers/session';
import { AeroThemeHelper } from './../helpers/aero.theme';

declare var $: any;

@Component(
{
    selector: 'aero-root',
    styleUrls: ['./public-map.component.scss'],
    templateUrl: './public-map.component.html',
})
export class PublicMapComponent 
{
    loggedInUserToken = "public";
    loggedInUserInfoJson = "";

    constructor(
        private messageHelper: MessageHelper,
        private sessionHelper: SessionHelper,
        private aeroThemeHelper: AeroThemeHelper
        )
    {
        sessionHelper.doHttpRequest("GET", BaseHelper.backendUrl+"public/getLoggedInUserInfo")
        .then((loggedInUserInfo) => 
        {
           this.loggedInUserInfoJson = BaseHelper.objectToJsonStr(loggedInUserInfo)
        })
        .catch((e) => { this.messageHelper.toastMessage("Bir hata oluştu. Sonra tekrar deneyin"); });
        
        this.aeroThemeHelper.pageRutine();
    }
}
