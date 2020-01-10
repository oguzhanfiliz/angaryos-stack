import {Component} from '@angular/core';

import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { AeroThemeHelper } from './../helpers/aero.theme';

declare var $: any;

@Component(
{
  selector: 'dashboard',
  styleUrls: ['./dashboard.component.scss'],
  templateUrl: './dashboard.component.html',
})
export class DashboardComponent
{
  constructor(
    private generalHelper: GeneralHelper,
    private aeroThemeHelper: AeroThemeHelper) { }

  ngAfterViewInit() 
  {   
    this.aeroThemeHelper.addEventForFeature("layoutCommonEvents"); 
  }
}
