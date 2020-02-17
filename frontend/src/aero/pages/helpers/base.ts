import { environment } from './../../../environments/environment';

declare var $: any;
document.title = environment.title;

export abstract class BaseHelper 
{     
  public static angaryosUrlPath:string = environment.urlPath;
  
  public static backendUrl:string = "https://"+environment.host+"/api/v1/";
  public static baseUrl:string = "https://"+environment.host+"/";
  public static _keyStr = environment.encryptKey;

  public static tokenTimeOut = 1000 * 60 * 60 * 24 * 5;
  public static token:string = "";
  public static debug:boolean = true;
  public static loggedInUserInfo = null;

  public static backendServiceControl = null;

  public static addedScripts = {};
  public static pipe = {};

  public static preLoad()
  {
    this.fillUserData();
  }



  /****    General Function    ****/

  public static sleep(ms) 
  {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

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

  public static doInterval(id, func, params, duration = 1000)
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
      var control = true;
      while (control) 
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
      }

      resolve();
  }

  public static async waitForOperation(func)
  {
      return new Promise(resolve => this.waitForOperationTest(resolve, func));
  } 



  /****    User Operation Functions    ****/

  private static fillUserData()
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

  public static setToken(token)
  {
    this.writeToLocal("token", token, this.tokenTimeOut)
    this.token = token;
  }

  public static setLoggedInUserInfo(info)
  {
    this.writeToLocal("loggedInUserInfo", info, this.tokenTimeOut)
    this.loggedInUserInfo = info;
  }

  public static clearUserData()
  {
    this.clearToken();
    this.clearLoggedInUserInfo();
  }

  public static clearToken()
  {
    this.removeFromLocal("token");
    this.token = "";
  }

  public static clearLoggedInUserInfo()
  {
    this.removeFromLocal("loggedInUserInfo");
    this.loggedInUserInfo = "";
  } 

  public static closeModal(id)
  {
    $('#'+id).modal('hide');
  }
 


  /***   Data Functions    ****/

  public static ucfirst(s)
  {
    if (typeof s !== 'string') return ''
    return s.charAt(0).toUpperCase() + s.slice(1)
  }
  
  public static writeToLocal(key, value, timeOut = -1)
  {
    if(timeOut == 0) return;

    var obj = 
    {
      "data": value,
      "timeOut": timeOut
    };

    if(timeOut > 0) obj["startTime"] = new Date().toString();

    var jsonStr = this.objectToJsonStr(obj);
    jsonStr = this.encode(jsonStr);

    localStorage.setItem(key, jsonStr);
  }

  private static getLocalDataExpiration(obj)
  {
    if(obj.timeOut < 0) return true;

    var startTime = new Date(obj.startTime);
    var now = new Date();

    var interval = now.getTime() - startTime.getTime();

    return interval < obj.timeOut;
  }

  public static readFromLocal(key)
  {
    var jsonStr = localStorage.getItem(key);
    if(jsonStr == null) return null;

    jsonStr = this.decode(jsonStr);

    var obj = this.jsonStrToObject(jsonStr);

    if(this.getLocalDataExpiration(obj))
      return obj.data;
    else
    {
      this.removeFromLocal(key);
      return null;
    }
  }

  public static removeFromLocal(key)
  {
    localStorage.removeItem(key);
  }

  public static objectToJsonStr(obj)
  {
    return JSON.stringify(obj);
  }

  public static jsonStrToObject(jsonStr)
  {
    if(jsonStr == "") return "";
    
    return JSON.parse(jsonStr);
  }

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
    var arr = dateString.split(' ');
    var date = arr[0].split('/');
    return date[2]+"-"+date[1]+"-"+date[0]+" "+arr[1];
  }

  public static humanDateStringToDBDateString(str)
  {
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
  }



  /****    Cryption Functions    ****/

  public static encode(str)
  {
    return str;
  }

  public static decode(str)
  {
    return str;
  }
}