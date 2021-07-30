import 'package:angaryos/view/AnnouncementsScreen.dart';
import 'package:angaryos/view/ContactScreen.dart';
import 'package:angaryos/view/LoginScreen.dart';
import 'package:angaryos/view/ProfileScreen.dart';
import 'package:angaryos/view/MainScreen.dart';
import 'package:angaryos/view/FailPage.dart';
import 'package:angaryos/view/ShortcutsPage.dart';
import 'package:angaryos/view/ShortcutsUpdatePage.dart';
import 'package:flutter/material.dart';
import 'package:angaryos/helper/BaseHelper.dart';

class RouteGenerator {
  static List<dynamic> getRouteRules(String langPath, String data) {
    return [
      {"path": langPath, "screen": MainScreen()},
      {"path": langPath + "shortcuts", "screen": ShortcutsPage(data: data)},
      {
        "path": langPath + "shortcutsUpdate",
        "screen": ShortcutsUpdatePage(data: data)
      },
      {"path": langPath + "announcements", "screen": AnnouncementsScreen()},
      {"path": langPath + "contact", "screen": ContactScreen()},
      {"path": langPath + "login", "screen": LoginScreen()},
      {"path": langPath + "profile", "screen": ProfileScreen()}
    ];
  }

  static Route<dynamic> generatoeRoute(RouteSettings settings) {
    BaseHelper.currentUrlFull = settings.name ?? "";

    List<String> temp = settings.name!.split("?");
    String url = temp[0];

    String data = "";
    if (temp.length > 1) data = settings.name!.replaceAll(url + "?", "");

    String currentLanguagePath = "/" + BaseHelper.getCurrentLanguage(url) + "/";
    List<dynamic> routeRules = getRouteRules(currentLanguagePath, data);
    for (dynamic rule in routeRules) {
      if (url == rule["path"]) {
        return MaterialPageRoute(
            builder: (_) => rule["screen"],
            settings: RouteSettings(
                name: rule["path"] + (data == "" ? "" : "?" + data)));
      }
    }

    return MaterialPageRoute(
        builder: (_) => FailPage(),
        settings: RouteSettings(name: currentLanguagePath + 'fail'));
    //}
  }
}
