import { ActivatedRoute} from '@angular/router';
import { Component } from '@angular/core';

import { types as typeList } from './types';

import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { MessageHelper } from './../helpers/message';
import { AeroThemeHelper } from './../helpers/aero.theme';

import {CdkDragDrop, moveItemInArray, transferArrayItem} from '@angular/cdk/drag-drop';

declare var $: any; 

@Component(
{
    selector: 'data-entegrator',
    styleUrls: ['./data-entegrator.component.scss'],
    templateUrl: './data-entegrator.component.html',
})
export class DataEntegratorComponent 
{
    public tableName:string = "";
    public tableId:number = 0;
    
    constructor(
        private route: ActivatedRoute,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper,
        private messageHelper: MessageHelper
        )
    {
        this.aeroThemeHelper.addEventForFeature("standartElementEvents"); 
        this.aeroThemeHelper.addEventForFeature("layoutCommonEvents"); 

        setTimeout(() => {
            route.params.subscribe(val => 
            {
                th.tableName = val.tableName;
                th.tableId = val.tableId;
            });
        }, 100);
    }
}