import {Component} from '@angular/core';

import { BaseHelper } from './../helpers/base';
import { GeneralHelper } from './../helpers/general';
import { AeroThemeHelper } from './../helpers/aero.theme';

declare var $: any;

@Component(
{
  selector: 'link-page',
  styleUrls: ['./link-page.component.scss'],
  templateUrl: './link-page.component.html',
})
export class LinkPageComponent
{
  constructor(
    private generalHelper: GeneralHelper,
    private aeroThemeHelper: AeroThemeHelper) { }

  ngAfterViewInit() 
  {   
    this.aeroThemeHelper.addEventForFeature("layoutCommonEvents");
    this.aeroThemeHelper.addEventForFeature("standartElementEvents"); 
  }
}
