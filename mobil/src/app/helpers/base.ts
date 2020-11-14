import { environment } from './../../environments/environment';

export abstract class BaseHelper 
{     
  /****    Variables    ****/

  public static debug = false;

  public static noImageUrl = 'assets/img/404.png';
  public static backendBaseUrl:string = "https://"+environment.host+"/";

  private static token:string = "";
  private static loggedInUserInfo = null;

  public static loggedUserInfoChangedDelegate = [];

  public static pipe = {};

  public static mobilAuths = {};



  /****    Functions    ****/

  public static setLoggedInUserInfo(info)
  {
    this.loggedInUserInfo = info;
    this.loggedUserInfoChanged();
  }

  public static setToken(token)
  {
    this.token = token;
    this.loggedUserInfoChanged();
  }

  public static getToken()
  {
    return this.token;
  }

  public static getLoggedInUserInfo()
  {
    return this.loggedInUserInfo;
  }



  /****    Events    ****/

  private static loggedUserInfoChanged()
  {
    this.updateMobilAuths();

    for(var i = 0; i < this.loggedUserInfoChangedDelegate.length; i++)
    {
      var o = this.loggedUserInfoChangedDelegate[i];
      o.loggedInUserInfoChanged(this.token, this.loggedInUserInfo);
    }
  }

  private static updateMobilAuths()
  {
    this.mobilAuths = {};
    
    if(this.loggedInUserInfo == null) return;
    if(typeof this.loggedInUserInfo["auths"] == "undefined") return;
    if(typeof this.loggedInUserInfo["auths"]["mobil"] == "undefined") return;
    
    var keys = Object.keys(this.loggedInUserInfo["auths"]["mobil"]);
    for(var i = 0; i < keys.length; i++) this.mobilAuths[keys[i]] = true;
  }

  public static ucfirst(s)
  {
    if (typeof s !== 'string') return ''
    return s.charAt(0).toUpperCase() + s.slice(1)
  }


  //public pipe = {};

  //public _keyStr = environment.encryptKey;
  
  //public static isMobileDevice = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));

  /*public static preLoad()
  {
    this.fillUserData();
  }*/



  /****    General Function    ****/

  /*

  public static getObjectKeys(obj)
  {
    if(obj == null) return [];
    return Object.keys(obj);
  }

  public static htmlStripTags(html)
  {
    return html.replace(/&/g, "&amp;").replace(/>/g, "&gt;").replace(/</g, "&lt;").replace(/"/g, "&quot;");
  }

  public static replaceAll(str, oldStr, newStr)
  {
    return str.split(oldStr).join(newStr);
  }

  
*/


  

  /*public static doInterval(id, func, params, duration = 1000)
  {
    return new Promise(resolve => 
    {
      id = "intervalId"+id;

      if(typeof this.pipe[id] != "undefined") 
        clearInterval(this.pipe[id]);

      this.pipe[id] = setInterval(() =>
      {
        clearInterval(this.pipe[id]);
        delete this.pipe[id];

        resolve(func(params));
      }, duration);
    });
  }

  public static async waitForOperationTest(resolve, func)
  {
    var count = 20;
    var control = true;
    
    while(control) 
    {
        try 
        {
            func(); 
            control = false;
        } 
        catch (error) 
        {
            await BaseHelper.sleep(100); 
        } 

        if(--count == 0) control = false;
      }
            
      resolve();
  }

  public static async waitForOperation(func)
  {
    return new Promise((resolve, error) => this.waitForOperationTest(resolve, func));
  } 
  
  public static formatMoney(number, decPlaces, decSep, thouSep) 
  {
    decPlaces = isNaN(decPlaces = Math.abs(decPlaces)) ? 2 : decPlaces,
    decSep = typeof decSep === "undefined" ? "." : decSep;
    thouSep = typeof thouSep === "undefined" ? "," : thouSep;
    var sign = number < 0 ? "-" : "";
    var i = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decPlaces)));
    var j = (j = i.length) > 3 ? j % 3 : 0;

    return sign +
        (j ? i.substr(0, j) + thouSep : "") +
        i.substr(j).replace(/(\decSep{3})(?=\decSep)/g, "$1" + thouSep) +
        (decPlaces ? decSep + Math.abs(number - parseInt(i)).toFixed(decPlaces).slice(2) : "");
  }*/



  /****    User Operation Functions    ****/

  /*private static fillUserData()
  {
    this.fillTokenIfExist();
    this.fillLoggedInUserInfoIfExist();
  }

  private static fillTokenIfExist()
  {
    var temp = this.readFromLocal("token");
    if(temp != null) this.token = temp;
  }

  private static fillLoggedInUserInfoIfExist()
  {
    var temp = this.readFromLocal("loggedInUserInfo");
    if(temp != null) this.loggedInUserInfo = temp;
  }

  

  

  

  public static closeModal(id)
  {
    $('#'+id).click();
  }*/
 


  /***   Data Functions    ****/
  
  /*public static getAllFormsData(baseElementSelector)
  {
    var data = {};
    
    var temp = $(baseElementSelector+' input');
    for(var i = 0; i < temp.length; i++)
    {
        var element = $(temp[i]);
        data[element.attr('name')] = element.val();
    }
    
    var temp = $(baseElementSelector+' select');
    for(var i = 0; i < temp.length; i++)
    {
        var element = $(temp[i]);
        data[element.attr('name')] = element.val();
    }  
   
    return data;   
  }

  public static getElementTitle(title, defaultTitle = "")
  {
    if(title == null) return defaultTitle;
    if(title == "") return defaultTitle;
    if(title.substr(0, 1) == "*") return defaultTitle;
    
    return title;
  }

  */
  
  

  /**/

  /*

  

  public static getCloneFromObject(obj)
  {
    var str = this.objectToJsonStr(obj);
    return this.jsonStrToObject(str);
  }

  public static dateToDBString(date) 
  {
    function zeroPad(d) 
    {
      return ("0" + d).slice(-2)
    }

    var rt = [date.getUTCFullYear(), zeroPad(date.getMonth() + 1), zeroPad(date.getDate())].join("-");
    rt += " ";
    rt += [zeroPad(date.getHours()), zeroPad(date.getMinutes()), zeroPad(date.getSeconds())].join(":");
    return rt;
  }

  public static dBDateTimeStringToHumanDateTimeString(dateString) 
  {
    if(dateString == null || dateString.length == 0) return dateString;
    
    var arr = dateString.split(' ');
    var date = arr[0].split('-');
    return date[2]+"/"+date[1]+"/"+date[0]+" "+arr[1];
  }

  public static dBDateStringToHumanDateString(dateString) 
  {
    if(dateString == null || dateString.length == 0) return dateString;
    
    var date = dateString.split('-');
    return date[2]+"/"+date[1]+"/"+date[0];
  }

  public static humanDateTimeStringToDBDateTimeString(dateString) 
  {
    if(dateString == null || dateString.length == 0) return dateString;

    var arr = dateString.split(' ');
    var date = arr[0].split('/');
    return date[2]+"-"+date[1]+"-"+date[0]+" "+arr[1];
  }

  public static humanDateStringToDBDateString(str)
  {
    if(str == null || str.length == 0) return str;

    var date = str.split('/');
    return date[2]+"-"+date[1]+"-"+date[0];
  }

  public static writeToPipe(key, data, debug = false)
  {
      if(debug) console.log('Write To Pipe: ' + key);
      this.pipe[key] = data;
  }

  public static readFromPipe(key, debug = false)
  {
      if(debug) console.log('Read From Pipe: ' + key);

      if(typeof this.pipe[key] == "undefined") return null;

      return this.pipe[key];
  }

  public static deleteFromPipe(key, debug = false)
  {
      if(debug) console.log('Delete From Pipe: ' + key);

      if(typeof this.pipe[key] == "undefined") return;

      delete this.pipe[key];
  }*/



  
}