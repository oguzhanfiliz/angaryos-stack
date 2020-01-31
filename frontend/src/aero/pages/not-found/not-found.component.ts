import { ActivatedRoute} from '@angular/router';
import { Component } from '@angular/core';

import { environment } from './../../../environments/environment';

import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { MessageHelper } from './../helpers/message';
import { AeroThemeHelper } from './../helpers/aero.theme';

@Component(
{
    selector: 'not-found',
    styleUrls: ['./not-found.component.scss'],
    templateUrl: './not-found.component.html',
})
export class NotFoundComponent 
{
    constructor(
        private route: ActivatedRoute,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper,
        private messageHelper: MessageHelper
        )
    {
    }

    ngAfterViewInit() 
    {    
        this.aeroThemeHelper.addEventForFeature("layoutCommonEvents"); 
        this.aeroThemeHelper.addEventForFeature("standartElementEvents");
    }

    get404ImageUrl()
    {
        return 'https://'+environment.host+'/assets/themes/aero/assets/images/404.svg';
    }
}