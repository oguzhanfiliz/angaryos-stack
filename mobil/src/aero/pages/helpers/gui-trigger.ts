import { Injectable } from '@angular/core';
import { MessageHelper } from './message';
import { SessionHelper } from './session';
import { BaseHelper } from './base';
import { columnVisibilityRules } from './column.visibility.rules';

declare var $: any;

@Injectable()
export class GuiTriggerHelper 
{   
    constructor(
      public messageHelper: MessageHelper,
      public sessionHelper: SessionHelper
    ) {} 
    


    /****    Common Functions    ****/

    public getFormGroupElement(elementId)
    {
      var temp = $(elementId);

      for(var i = 0; i < 10; i++)
      {
        temp = temp.parent();

        var clss = temp.attr('class');
        if(typeof clss == "undefined") continue;
        if(clss == "") continue;

        clss = clss.split(" ");
        for(var j = 0; j < clss.length; j++)
          if(clss[j] == 'form-group') return (temp);
      }
    }

    public changeColumnVisibility(tableName, columnName, elementId, data)
    {
      if(typeof columnVisibilityRules[columnName] != "undefined")
        columnVisibilityRules[columnName](tableName, columnName, elementId, data);
    }



    /****   Triggers  *****/

    public autoFillNameColumnFromDisplayNameColumn(tableName, columnName, elementId, data) 
    {
      var group = this.getFormGroupElement(elementId);

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
}