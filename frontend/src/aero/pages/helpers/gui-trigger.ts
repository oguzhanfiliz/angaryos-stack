import { Injectable } from '@angular/core';
import { MessageHelper } from './message';
import { SessionHelper } from './session';
import { BaseHelper } from './base';


declare var $: any;

@Injectable()
export class GuiTriggerHelper 
{   
    intervalIds = {};

    constructor(
      public messageHelper: MessageHelper,
      public sessionHelper: SessionHelper
    ) {} 
    


    /****   Triggers  *****/

    public autoFillNameColumnFromDisplayNameColumn(tableName, columnName, elementId, data) 
    {
      var params =
      {
        elementId: elementId,
        columnName: columnName,
        data: data,
        this: this
      };

      function func(params)
      {
        var targetElementId = params.elementId.replace(params.columnName, 'name');
          
        var val = $(targetElementId).val();

        if(typeof val != "undefined" && val != "") return;

        var name = params.this.sessionHelper.toSeo(params.data.display_name);
        $(targetElementId).val(name);

        //return {danger: "test error"+params.elementId, success: "test success"};
      }

      return BaseHelper.doInterval('autoFillNameColumnFromDisplayNameColumn', func, params);
    }



    /****    Functions    ****/

    
}