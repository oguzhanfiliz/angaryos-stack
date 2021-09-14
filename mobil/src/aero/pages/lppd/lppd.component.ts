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
    selector: 'lppd',
    styleUrls: ['./lppd.component.scss'],
    templateUrl: './lppd.component.html',
})
export class LPPDComponent 
{
    constructor(
        private route: ActivatedRoute,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper,
        private messageHelper: MessageHelper
        )
    { }
}