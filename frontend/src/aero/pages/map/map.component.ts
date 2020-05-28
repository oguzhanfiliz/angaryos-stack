import { Component, TemplateRef } from '@angular/core';
import { Router } from '@angular/router';

import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { SessionHelper } from './../helpers/session';
import { AeroThemeHelper } from './../helpers/aero.theme';

declare var $: any;

@Component(
{
    selector: 'aero-root',
    styleUrls: ['./map.component.scss'],
    templateUrl: './map.component.html',
})
export class MapComponent 
{
    loggedInUserToken = "";
    loggedInUserInfoJson = "";

    constructor(
        private generalHelper: GeneralHelper,
        private sessionHelper: SessionHelper,
        private aeroThemeHelper: AeroThemeHelper
        )
    {
        BaseHelper.preLoad();
        if(BaseHelper.token.length == 0) this.generalHelper.navigate('/login');

        this.loggedInUserToken = BaseHelper.token;

        this.aeroThemeHelper.addEventForFeature('standartElementEvents');

        sessionHelper.getLoggedInUserInfo().then((loggedInUserInfo) =>
        {
            this.loggedInUserInfoJson = BaseHelper.objectToJsonStr(loggedInUserInfo);
        });
        
        this.aeroThemeHelper.pageRutine();
    }
}