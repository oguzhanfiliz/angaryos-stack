import { ActivatedRoute} from '@angular/router';
import { Component } from '@angular/core';
import { SessionHelper } from './../helpers/session';
import { BaseHelper } from './../helpers/base';
import { DataHelper } from './../helpers/data';
import { GeneralHelper } from './../helpers/general';
import { MessageHelper } from './../helpers/message';
import { AeroThemeHelper } from './../helpers/aero.theme'; 

declare var $: any;

@Component(
{
    selector: 'shortcuts',
    styleUrls: ['./shortcuts.component.scss'],
    templateUrl: './shortcuts.component.html',
})
export class ShortcutsComponent
{   
    defaultShortcuts = [];
    userShortcuts = null;
    filteredShortcuts = []; 
    filteredAllShortcuts = [];
    allShortcuts = [];
    
    modal = false;
    eventCreated = false;
    
    constructor(
        private route: ActivatedRoute,
        private sessionHelper: SessionHelper,
        private generalHelper: GeneralHelper,
        private aeroThemeHelper: AeroThemeHelper,
        private messageHelper: MessageHelper
        )
    {   
        if(BaseHelper.loggedInUserInfo == null)
        {
            this.generalHelper.navigate('/login');
            return;
        }
        
        if(BaseHelper.isBrowser && window.location.href.indexOf('/shortcuts') > -1)
        {
            this.generalHelper.navigate('dashboard');
            return;
        }
        
        this.aeroThemeHelper.pageRutine();   
        
        var th = this;   
        setTimeout(() => { th.aeroThemeHelper.pageRutine(); }, 500);
        
        this.fillDefaultShortcuts();
        this.fillUserShortcuts();
        this.fillAllShortcuts();
    }

    fillDefaultShortcuts()
    {
        /*var temp = {
            "id":"tableList101",
            "icon":"zmdi-grid",
            "text":"Yemek Listeleri Listesi",
            "link":"table/yemek_listeleri",
            "type":"navigate",
            "type_display":"Standart Link",
            "selected":true
        };
        this.defaultShortcuts.push(temp);*/
    }

    getTable(tableName)
    {
        var ui = BaseHelper.loggedInUserInfo;
        var tableGroups = ui["menu"]["tables"];

        var keys = Object.keys(tableGroups);
        for(var i = 0; i < keys.length; i++)
        {
            var tableSets = tableGroups[keys[i]];
            for(var j = 0; j < tableSets.length; j++)
            {
                var table = tableSets[j];
                if(table["name"] == tableName) return table;
            }
        }

        return null;
    }
    
    fillAllShortcuts()
    {
        var ui = BaseHelper.loggedInUserInfo;
        
        if(typeof ui["map"] != "undefined")
            this.allShortcuts.push({
                "id": "map",
                "icon": "zmdi-map", 
                "text":"Harita", 
                "link":'/'+BaseHelper.angaryosUrlPath+'/map',
                "type":"navigate",
                "type_display":"Standart Link",
                'selected': false
            });
        
        var menu = ui["menu"];
        if(typeof menu["additionalLinks"] != "undefined")
        {
            for(var i = 0; i < menu["additionalLinks"].length; i++)
            {
                this.allShortcuts.push({
                    "id": 'additionalLink'+menu["additionalLinks"][i]["id"],
                    "icon": "zmdi-link", 
                    "text": menu["additionalLinks"][i]["name_basic"], 
                    "link":'', 
                    "type":"additionalLink",
                    "type_display":"Ek Link",
                    "data": menu["additionalLinks"][i],
                    'selected': false
                });
            }
        }
        
        var auths = ui["auths"];
        if(typeof auths["missions"] != "undefined")
        {
            var missionIds = Object.keys(auths["missions"]);
            for(var i = 0; i < missionIds.length; i++)
            {
                var missionId = missionIds[i];
                 
                this.allShortcuts.push({
                        "id": "mission"+missionId,
                        "icon": "zmdi-grid", 
                        "text": missionId+ ". Görevi Tetikle", 
                        "type":"mission",
                        "type_display":"Görev Tetikleme",
                        'selected': false,
                        "data": missionId
                    });
            }
        }

        if(typeof auths["tables"] != "undefined")
        {
            var tables = Object.keys(auths["tables"]);
            for(var i = 0; i < tables.length; i++)
            {
                var tableName = tables[i];
                var tableAuth = auths["tables"][tableName];
                var table = this.getTable(tableName);
                
                if(table == null) continue;

                if(typeof tableAuth["lists"] != "undefined")
                {
                    this.allShortcuts.push({
                        "id": 'tableList'+table["id"],
                        "icon": "zmdi-grid", 
                        "text": table["display_name"] + " Listesi", 
                        "link": 'table/'+tableName, 
                        "type":"navigate",
                        "type_display":"Standart Link",
                        'selected': false
                    });
                }

                if(typeof tableAuth["creates"] != "undefined")
                {
                    this.allShortcuts.push({
                        "id": 'tableCreate'+table["id"],
                        "icon": "zmdi-grid", 
                        "text": table["display_name"] + " Yeni Kayıt Ekle", 
                        "link": 'table/'+tableName+"/create", 
                        "type":"navigate",
                        "type_display":"Standart Link",
                        'selected': false
                    });
                }
            }
        }
        /*console.log(this.allShortcuts);
        console.log(this.userShortcuts);
        console.log(this.filteredAllShortcuts);*/

        for(var i = 0; i < this.allShortcuts.length; i++)
           for(var j = 0; j < this.userShortcuts.length; j++)
                if(this.allShortcuts[i]["id"] == this.userShortcuts[j]["id"])
                {
                    this.allShortcuts[i]["selected"] = true;
                    break;
                }
        
        this.filteredAllShortcuts = BaseHelper.getCloneFromObject(this.allShortcuts)
    }
    
    additionalLinkClicked(additionalLink)
    {
      DataHelper.loadAdditionalLinkPayload(this, additionalLink);

      var url = DataHelper.getUrlFromAdditionalLink(additionalLink);
      if(url == null || url.length == 0) return;

      this.generalHelper.navigate(url, additionalLink['open_new_window']);
    }
    
    fillUserShortcuts()
    {
        var key = 'user:'+BaseHelper.loggedInUserInfo['user']["id"]+".shortcuts";    
        var temp = BaseHelper.readFromLocal(key);        
        if(!temp)
        {
            this.userShortcuts = this.defaultShortcuts;
            this.saveUserShortcutsToLocal();
            this.search("");
            
            return;
        };
        
        this.userShortcuts = temp;
        this.filteredShortcuts = BaseHelper.getCloneFromObject(this.userShortcuts);
    }
    
    openEditShortcutsModal()
    {
        this.modal = true;
        
        var th = this;
        setTimeout(() => 
        {
            $('#editShortcutsModal').modal('show');
            th.setEventListener();
        }, 200);
    }
    
    setEventListener()
    {
        if(this.eventCreated) return;
            
        this.eventCreated = true;

        var th = this;
        setTimeout(() =>
        {
            $("#editShortcutsModal").on("hidden.bs.modal", function () 
            {
                th.modal = false;
                th.filteredShortcuts = BaseHelper.getCloneFromObject(th.userShortcuts);
            });
        }, 300);
            
    }
    
    shortcutSelected(shortcut)
    {
        var state = $('#shortcut-'+shortcut["id"]).prop('checked');
        
        if(state)
        {
            var finded = false;
            for(var i = 0; i < this.userShortcuts.length; i++)
            {
                var temp = this.userShortcuts[i];
                if(shortcut["id"] == temp["id"]) 
                {
                    finded = true;
                    break;
                }
            }
            
            if(!finded) this.userShortcuts.push(shortcut);
        }
        else
        {
            for(var i = 0; i < this.userShortcuts.length; i++)
            {
                var temp = this.userShortcuts[i];
                if(shortcut["id"] == temp["id"])
                {
                    this.userShortcuts.splice(i, 1);
                    break;
                }
            }
        }
                
        for(var i = 0; i < this.allShortcuts.length; i++)
        {
            var temp = this.allShortcuts[i];
            if(shortcut["id"] == temp["id"])
            {
                this.allShortcuts[i]["selected"] = state;
                break;
            }
        }
        
        this.saveUserShortcutsToLocal();
        
    }
    
    saveUserShortcutsToLocal()
    {
        var key = 'user:'+BaseHelper.loggedInUserInfo['user']["id"]+".shortcuts";   
        BaseHelper.writeToLocal(key, this.userShortcuts);
    }
        
    search(text)
    {
        this.filteredShortcuts = this.searchInArray(text, this.userShortcuts);
    }
    
    searchAllShurtcut(text)
    {
        this.filteredAllShortcuts = this.searchInArray(text, this.allShortcuts);
    }
    
    searchInArray(text, fullArray)
    {
        if(text.length == 0) return fullArray;
        
        text = text.toLocaleLowerCase();        
        var tempArray = [];        
        for(var i = 0; i < fullArray.length; i++)
        {
            var item = fullArray[i];
            
            if(item["text"].toLocaleLowerCase().indexOf(text) > -1)
            {
                tempArray.push(item);
                continue;
            }
            
            if(typeof item["link"] != "undefined" && item["link"].toLocaleLowerCase().indexOf(text) > -1)
            {
                tempArray.push(item);
                continue;
            }
            
            if(item["type_display"].toLocaleLowerCase().indexOf(text) > -1)
            {
                tempArray.push(item);
                continue;
            }
        }
        
        return tempArray;
    }
    
    async missionTrigger(id)
    {
        var url = this.sessionHelper.getBackendUrlWithToken();
        if(url.length == 0) return;
        
        url += "missions/"+id;
        
        var th = this;
        await this.sessionHelper.doHttpRequest("GET", url) 
        .then(async (data) => 
        {
            this.messageHelper.sweetAlert("Tetikleme başarılı: "+data["message"], "Başarı", "success");
        })
        .catch((e) =>  
        {
            console.log(e);
            this.messageHelper.sweetAlert("Beklenmedik bir hata oluştu!", "Hata", "warning");
        });
    }
    
    shortcutClicked(shortcut)
    {
        BaseHelper.closeModal('browserShortcutsModal');
        
        switch(shortcut["type"])
        {
            case "navigate":
                this.generalHelper.navigate(shortcut["link"]);
                break;
            case "additionalLink":
                this.additionalLinkClicked(shortcut["data"])
                break;
            case "mission":
                this.missionTrigger(shortcut["data"]);
                break;
            default: console.log(shortcut);
        }
    }
}