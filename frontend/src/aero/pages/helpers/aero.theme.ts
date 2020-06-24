import { Injectable } from '@angular/core';
import { BaseHelper } from './base';
import { SessionHelper } from './session';

declare var $: any;

@Injectable()
export class AeroThemeHelper 
{     
    public baseMenu = [];
    
    constructor(private sessionHelper: SessionHelper) { }



    /****    GUI Features Functions    ****/
    
    public pageRutine()
    {
        setTimeout(() =>
        {
            $(".page-loader-wrapper").fadeOut();
            $('span.select2-results').parent().remove();

            $('.tooltip-inner').remove();
            $('[data-toggle="tooltip"]').tooltip();
            
            $.getScript('assets/themes/aero/assets/bundles/libscripts.bundle.js');
            
        }, 100);
    }

    public loadPageScripts()
    {
      if(BaseHelper.readFromPipe('loadPageScriptsLoaded'))  return;
      
      BaseHelper.writeToPipe('loadPageScriptsLoaded', true);
      BaseHelper.writeToPipe('loadPageScriptsLightLoaded', true);
      
      setTimeout(async () => 
      {
        await $.getScript('https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js');
        
        $.getScript('assets/themes/aero/assets/bundles/libscripts.bundle.js', function()
        {
          $.getScript('assets/themes/aero/assets/bundles/vendorscripts.bundle.js', function()
          {
            $.getScript('assets/themes/aero/assets/bundles/mainscripts.bundle.js');
            $.getScript('assets/themes/aero/assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js');

            
            $.getScript('assets/themes/aero/assets/bundles/jvectormap.bundle.js');
            $.getScript('assets/themes/aero/assets/bundles/sparkline.bundle.js');
            $.getScript('assets/themes/aero/assets/bundles/c3.bundle.js');

            $.getScript('assets/themes/aero/assets/plugins/jquery-validation/jquery.validate.js');
            $.getScript('assets/themes/aero/assets/plugins/jquery-steps/jquery.steps.js');
          });     

          $.getScript('assets/ext_modules/ace-builds/src-min/ace.js', function()
          {
            $.getScript('assets/ext_modules/ace-builds/src-min/mode-php.js');
            $.getScript('assets/ext_modules/ace-builds/src-min/mode-sql.js');
            $.getScript('assets/ext_modules/ace-builds/src-min/theme-twilight.js');
            $.getScript('assets/ext_modules/ace-builds/src-min/theme-github.js');
          });
        });

        $.getScript('assets/ext_modules/select2/select2.min.js');
        
        $.getScript('assets/ext_modules/jsonTreeViewer/libs/jsonTree/jsonTree.js');
        $.getScript('assets/ext_modules/slimscrool/jquery.slimscroll.min.js');
      }, 10);
    }

    public loadPageScriptsLight()
    {
      if(BaseHelper.readFromPipe('loadPageScriptsLightLoaded'))  return;
      
      BaseHelper.writeToPipe('loadPageScriptsLightLoaded', true);
      
      setTimeout(() => 
      {
        $.getScript('assets/themes/aero/assets/bundles/libscripts.bundle.js', function()
        {
          $.getScript('assets/themes/aero/assets/bundles/vendorscripts.bundle.js');
        });
      }, 10);
    }
    
    public async addEventForFeature(name)
    {
        await BaseHelper.sleep(500);

        switch (name) 
        {
            case 'leftSideBar': return this.addLeftSideBarEvents();
            case 'mobileMenuButton': return this.addMobileMenuButtonEvents();
            case 'rightIconToggleButton': return this.addRightIconToggleButtonEvents();
            case 'standartElementEvents': return this.addStandartElementEvents();

            case 'layoutCommonEvents': 
                this.addMobileMenuButtonEvents();
                this.addRightIconToggleButtonEvents();
                return;

            default: return alert(name + " event bulunamadı");
        }
    }

    public removeThemeClass()
    {
      $('body').removeClass('theme-dark');
      $('body').removeClass('theme-light');
    }

    public addThemeClass()
    {
      $('body').addClass(this.getThemeClass());
    }

    public addStandartElementEvents()
    {
      this.removeThemeClass();
      this.addThemeClass();
    }

    public addMobileMenuButtonEvents()
    {
        $(".mobile_menu").on("click", function() 
        {
            $(".sidebar").toggleClass("open")
        });
    }

    public addRightIconToggleButtonEvents()
    {
        $(".right_icon_toggle_btn").on("click", function() 
        {
            $("body").toggleClass("right_icon_toggle")
        });
    }

    public addLeftSideBarEvents()
    {
      if(typeof BaseHelper.addedScripts['leftSideBar'] != "undefined") return;
      
      $.each($(".menu-toggle.toggled"), function(a, b) 
      {
          $(b).next().slideToggle(0)
      });

      $(".menu-toggle").on("click", function(a) 
      {
          var b = $(this),
              c = b.next();

          if ($(b.parents("ul")[0]).hasClass("list")) 
          {
              var d = $(a.target).hasClass("menu-toggle") ? a.target : $(a.target).parents(".menu-toggle");
              $.each($(".menu-toggle.toggled").not(d).next(), function(a, b) {
                  $(b).is(":visible") && ($(b).prev().toggleClass("toggled"), $(b).slideUp())
              })
          }
          b.toggleClass("toggled"), c.slideToggle(320)
      });

      BaseHelper.addedScripts['leftSideBar'] = true;
    }



    /****    Current Theme Functions    ****/
    
    public getThemeClass()
    {
      var theme = 'theme-light';

      var name = this.getTheme();
      if(typeof name != "undefined" && name != null && name.length > 0)
        theme = 'theme-'+name;
      
      return theme
    }

    public setTheme(name)
    {
      BaseHelper.writeToLocal('theme', name);
    }

    public getTheme()
    {
      return BaseHelper.readFromLocal('theme');
    }



    /****   Base Menu Functions    ****/
    
    private controlSearchForTableItem(item, search)
    {
      if(search.length == 0) return true;

      search = search.toLocaleLowerCase();

      if(item.display_name.toLocaleLowerCase().indexOf(search) > -1) return true;
      if(item.name.toLocaleLowerCase().indexOf(search) > -1) return true;

      return false;
    }

    private getTableGroupdById(id)
    {
      var tableGroups = BaseHelper.loggedInUserInfo.menu.tableGroups;
      for (let i = 0; i < tableGroups.length; i++) 
        if(tableGroups[i]['id'] == id)
          return tableGroups[i];
    }

    private getTableGroupdByIds()
    {
      var tableGroups = BaseHelper.loggedInUserInfo.menu.tableGroups;
      
      var tableGroupIds = []
      for(var i = 0; i < tableGroups.length; i++)
        tableGroupIds.push(tableGroups[i]['id']);

      return tableGroupIds;
    }

    private getTableGroupsForMenuItem(search = "")
    {
      var menu = [];

      var tables = BaseHelper.loggedInUserInfo.menu.tables;      
      var tableGroupIds = this.getTableGroupdByIds();//Object.keys(tables);
     
      for (var i = 0; i < tableGroupIds.length; i++) 
      {
        var tableGroup = this.getTableGroupdById(tableGroupIds[i]);
        
        var tempMenuItem =
        {
          title: tableGroup['name_basic'],
          icon: tableGroup['icon'],
          toggled: search.length > 0,
          children: []
        }; 
 
        if(typeof tables[tableGroupIds[i]] == "undefined")
          continue;

        var children = []; 
        var tempTables = tables[tableGroupIds[i]];
        for(var j = 0; j < tempTables.length; j++)
        {
          if(!this.controlSearchForTableItem(tempTables[j], search)) continue;

          var temp = 
          {
            title: tempTables[j].display_name,
            link: '/'+BaseHelper.angaryosUrlPath+'/table/'+tempTables[j].name
          };

          children.push(temp);
        }

        if(children.length == 0) continue;

        tempMenuItem.children = children.sort(function(a, b) 
        { 
          if(a.title == null) a.title = a.link;
          if(b.title == null) b.title = b.link;

          return a.title.localeCompare(b.title);
        });
        
        menu.push(tempMenuItem);
      }

      return menu;
    }

    public getHomePageMenuItem()
    {
      var homePage =
      {
        title: 'Anasayfa',
        icon: 'zmdi-home',
        link: '/',
      };
      return homePage;
    }

    public getDashboardPageMenuItem()
    {
      var page =
      {
        title: 'Göstergeler',
        icon: 'zmdi-copy',
        link: '/'+BaseHelper.angaryosUrlPath+'/dashboard',
      };
      return page;
    }

    public getMapPageMenuItem()
    {
      var mapPage =
      {
        title: 'Harita',
        icon: 'zmdi-map',
        link: '/'+BaseHelper.angaryosUrlPath+'/map',
      };
      return mapPage;
    }
    
    public getRecordImportPageMenuItem()
    {
      var mapPage =
      {
        title: 'İçe Aktar',
        icon: 'zmdi-sign-in',
        link: '',
        func: 'importRecord',
      };
      return mapPage;
    }
    
    public getLogsPageMenuItem()
    {
      var mapPage =
      {
        title: 'Sunucu Logları',
        icon: 'zmdi-code-setting',
        link: '',
        func: 'openBackendLogs',
      };
      return mapPage;
    }
    
    public updateBaseMenu(search = "")
    {
      this.baseMenu =  [ ];

      if(search == "")
      {
        this.baseMenu.push(this.getHomePageMenuItem());
        
        if(this.sessionHelper.mapAuthControl())
          this.baseMenu.push(this.getMapPageMenuItem());
          
        if(this.sessionHelper.recordImportAuthControl())
          this.baseMenu.push(this.getRecordImportPageMenuItem());
          
        if(this.sessionHelper.debugUserAuthControl())
          this.baseMenu.push(this.getLogsPageMenuItem());
        
        this.baseMenu.push(this.getDashboardPageMenuItem());
      }

      var tableGroups = this.getTableGroupsForMenuItem(search);
      for (let i = 0; i < tableGroups.length; i++) 
        this.baseMenu.push(tableGroups[i]);
      
      setTimeout(() => 
      {
        delete BaseHelper.addedScripts['leftSideBar'];
        this.addEventForFeature("leftSideBar");
      }, 100);
    }
}