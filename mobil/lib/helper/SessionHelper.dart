import 'dart:io';
import 'package:angaryos/helper/LogHelper.dart';
import 'package:angaryos/helper/BaseHelper.dart';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:path/path.dart';
import 'package:dio/dio.dart';

class SessionHelper {
  //****    Variables    ****//

  static String publicToken = "public";
  static String token = "";
  static bool disableSslCheck = true;
  static bool showAngaryosErrorMessage = true;
  static dynamic user = null;

  static dynamic defaultListParams = {
    "page": 1,
    "limit": "10",
    "column_array_id": "0",
    "column_array_id_query": "0",
    "sorts": {},
    "filters": {}
  };

  //****    General    ****//

  static Future<Map<String, Object>> prepareDataForPost(
      Map<String, dynamic> data) async {
    Map<String, Object> temp = Map<String, Object>();

    for (String key in data.keys) {
      dynamic item = data[key];

      switch (item["type"]) {
        case "string":
          temp[key] = item["data"];
          break;
        case "fileBytes":
          List<MultipartFile> files = <MultipartFile>[];
          var fileBytes = item["data"];
          for (Map<String, dynamic> fData in fileBytes) {
            MultipartFile singleFile = await MultipartFile.fromBytes(
                fData["bytes"],
                filename: fData["name"]);
            files.add(singleFile);
          }
          temp[key] = files;
          break;
        case "filePaths":
          List<MultipartFile> files = <MultipartFile>[];
          var filePaths = item["data"];
          for (String filePath in filePaths) {
            MultipartFile singleFile = await MultipartFile.fromFile(filePath,
                filename: basename(filePath));
            files.add(singleFile);
          }

          temp[key] = files;
          break;
        default:
          LogHelper.error(
              tr("httpPost data içinde geçersiz kolon tipi"), [key, data]);
      }
    }

    return temp;
  }

  static Future<String?> httpPost(String url, Map<String, dynamic>? data,
      [bool? forceReturn]) async {
    String errorHtml = "";
    forceReturn ??= false;
    try {
      FormData? formData = null;
      if (data != null)
        formData = FormData.fromMap(await prepareDataForPost(data));
      var response =
          await Dio().post(url, data: formData).catchError((error) async {
        try {
          LogHelper.error(
              tr("Http post içinde hata"), [error.toString(), url, data]);
          if (forceReturn!) errorHtml = error.response.toString();
        } catch (e) {
          LogHelper.error(
              tr("Http post içinde hata") + ":", [e.toString(), url, data]);
        }
      });

      if (forceReturn) return errorHtml == "" ? response.toString() : errorHtml;
      if (response.statusCode == 200) return response.toString();
      return null;
    } catch (e) {
      LogHelper.error(
          tr("Http post içinde hata") + "::", [e.toString(), url, data]);
      if (errorHtml != "" && forceReturn) return errorHtml;
      return null;
    }
  }

  static Future<dynamic> httpPostJson(String url, dynamic data,
      [bool? forceReturn]) async {
    String? jsonStr = await httpPost(url, data, forceReturn);
    if (jsonStr == null) return null;

    try {
      return BaseHelper.jsonStrToObject(jsonStr);
    } catch (e) {
      LogHelper.error(tr("Http post içinde json parse edilemedi"),
          [e.toString(), url, data]);
      return forceReturn! ? jsonStr : null;
    }
  }

  static Future<dynamic> httpPostJsonA(String url, dynamic data,
      [bool? forceReturn]) async {
    dynamic obj = await httpPostJson(url, data, forceReturn);
    if (obj == null) return null;

    try {
      if (obj["status"] == "success") return obj["data"];

      if (obj["status"] == "error") {
        if (!await _angaryosResponseJsonCallback(obj) && forceReturn!)
          return obj["data"];
      }

      LogHelper.error(tr("httpPostJsonA için geçersiz cevap"), [obj, data]);
      return forceReturn! ? obj : null;
    } catch (e) {}

    LogHelper.error(tr("httpPostJsonA için geçersiz cevap") + ":", [obj, data]);
    return forceReturn! ? obj : null;
  }

  static Future<String?> httpGet(String url, [bool? forceReturn]) async {
    String errorHtml = "";
    forceReturn ??= false;

    try {
      final response = await http.get(Uri.parse(url), headers: {
        "Access-Control_Allow_Origin": "*"
      }).catchError((error) async {
        try {
          LogHelper.error(
              tr("Http get içinde hata"), [error.toString(), url, forceReturn]);
          if (forceReturn!) errorHtml = error.response.toString();
        } catch (e) {
          LogHelper.error(tr("Http get içinde hata") + ":",
              [e.toString(), url, forceReturn]);
        }
      });

      if (forceReturn) return errorHtml == "" ? response.body : errorHtml;

      if (response.statusCode == 200) {
        return response.body;
      } else {
        return null;
      }
    } catch (e) {
      LogHelper.error(tr("Http get içinde hata"), [e.toString(), url]);
      if (errorHtml != "" && forceReturn) return errorHtml;
      return null;
    }
  }

  static Future<dynamic> httpGetJson(String url, [bool? forceReturn]) async {
    String? jsonStr = await httpGet(url, forceReturn);
    if (jsonStr == null) return null;

    try {
      return BaseHelper.jsonStrToObject(jsonStr);
    } catch (e) {
      LogHelper.error(
          tr("Http get içinde json parse edilemedi"), [e.toString(), url]);
      return forceReturn! ? jsonStr : null;
    }
  }

  static Future<dynamic> httpGetJsonA(String url, [bool? forceReturn]) async {
    dynamic obj = await httpGetJson(url, forceReturn);
    if (obj == null) return null;

    try {
      if (obj["status"] == "success") return obj["data"];

      if (obj["status"] == "error") {
        if (!await _angaryosResponseJsonCallback(obj) && forceReturn!)
          return obj["data"];
      }

      LogHelper.error(tr("httpGetJsonA için geçersiz cevap"), obj);
      return forceReturn! ? obj : null;
    } catch (e) {}

    LogHelper.error(tr("httpGetJsonA için geçersiz cevap"), obj);
    return forceReturn! ? obj : null;
  }

  static Future<bool> _angaryosResponseJsonCallback(dynamic obj) async {
    switch (obj["data"]["message"]) {
      case "fail.token":
        BaseHelper.redirectToLoginUrl();
        return false;
      default:
        if (showAngaryosErrorMessage == false)
          LogHelper.error(tr("Sunucudan bir hata mesajı döndü"), obj);
        else
          BaseHelper.toastMessage(tr("Sunucudan bir hata mesajı döndü") +
              ": " +
              obj["data"]["message"]);
        return false;
    }
  }

  static String getBaseUrlWithToken() {
    return BaseHelper.backendBaseUrl + token + "/";
  }

  static String getListPageUrl(String tableName,
      [bool? publicUser, bool? defaultParams]) {
    publicUser ??= false;
    defaultParams ??= false;

    var token = publicUser ? SessionHelper.publicToken : SessionHelper.token;

    String url = BaseHelper.backendBaseUrl + token + "/tables/" + tableName;

    if (defaultParams == false) return url;

    var params = SessionHelper.defaultListParams;
    String paramsString = BaseHelper.objectToJsonStr(params);
    url += "?params=" + paramsString;
    return url;
  }

  static Future<String?> loginAndGetToken(String email, String password) async {
    String url = BaseHelper.backendBaseUrl + "login";

    dynamic clientInfo = {};
    clientInfo["agent"] = await BaseHelper.getDeviceInfo();
    clientInfo["firebaseToken"] = BaseHelper.firebaseCMToken;
    clientInfo["type"] = "mobile";

    Map<String, dynamic> data = Map<String, dynamic>();
    data["email"] = {"type": "string", "data": email};
    data["password"] = {"type": "string", "data": password};
    data["clientInfo"] = {
      "type": "string",
      "data": BaseHelper.objectToJsonStr(clientInfo)
    };

    dynamic obj = await SessionHelper.httpPostJsonA(url, data, true);
    try {
      return obj["token"];
    } catch (e) {
      LogHelper.error("Gelen cevapdan token alınamadı", [e.toString(), obj]);
    }

    return null;
  }

  static Future<bool> setToken(String token) async {
    try {
      SessionHelper.token = token;
      BaseHelper.writeToLocal("token", token, 1000 * 60 * 60 * 24 * 20);
      return true;
    } catch (e) {
      LogHelper.error(
          tr("Token atama esnasında hata oluştu"), [e.toString(), token]);
    }

    return false;
  }

  static Future<bool> setUser(dynamic user) async {
    try {
      SessionHelper.user = user;
      BaseHelper.writeToLocal("user", user, 1000 * 60 * 60 * 24 * 20);
      return true;
    } catch (e) {
      LogHelper.error(
          tr("User atama esnasında hata oluştu"), [e.toString(), user]);
    }

    return false;
  }

  static Future<dynamic> getLoggedInUserInfo(String token) async {
    String url = getBaseUrlWithToken() + "getLoggedInUserInfo";
    return await SessionHelper.httpGetJsonA(url);
  }

  static Future<void> fillUserFromLocal() async {
    String? token = await BaseHelper.readFromLocal("token");
    if (token == null) return;

    await SessionHelper.setToken(token);

    dynamic user = await BaseHelper.readFromLocal("user");
    if (user == null) return;

    await SessionHelper.setUser(user);
  }

  static Future<bool> logout() async {
    try {
      String url = getBaseUrlWithToken() + "logOut";
      dynamic rt = await SessionHelper.httpPostJson(url, null, true);
      if (rt == null) return false;

      bool control = false;
      try {
        control = rt["data"]["message"] == "success" ||
            rt["data"]["message"] == "fail.token";
      } catch (e) {}

      if (control) {
        BaseHelper.removeFromLocal("token");
        BaseHelper.removeFromLocal("user");
        SessionHelper.token = "";
        SessionHelper.user = null;

        return true;
      }
    } catch (e) {
      LogHelper.error(tr("Kullnıcı çıkış yapamadı!", e.toString()));
    }

    return false;
  }
}

class AngaryosHttpOverrides extends HttpOverrides {
  @override
  HttpClient createHttpClient(SecurityContext? context) {
    return super.createHttpClient(context)
      ..badCertificateCallback =
          (X509Certificate cert, String host, int port) => true;
  }
}
