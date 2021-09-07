import { ActivatedRoute} from '@angular/router';
import { Component } from '@angular/core';

import {CdkDragDrop, moveItemInArray} from '@angular/cdk/drag-drop';

import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { MessageHelper } from './../helpers/message';
import { AeroThemeHelper } from './../helpers/aero.theme';

declare var $: any;

@Component(
{
    selector: 'archive',
    styleUrls: ['./archive.component.scss'],
    templateUrl: './archive.component.html'
})
export class ArchiveComponent 
{
    public recordId = 0;
    public tableName = "";
    public defaultLimit = 10;
    
    constructor(
        public route: ActivatedRoute,
        public sessionHelper: SessionHelper,
        public generalHelper: GeneralHelper,
        public aeroThemeHelper: AeroThemeHelper,
        public messageHelper: MessageHelper
        ) 
    {
        var th = this;
        route.params.subscribe(val => 
        {
            th.tableName = val.tableName;
            th.recordId = val.recordId;
        });
    }
}
