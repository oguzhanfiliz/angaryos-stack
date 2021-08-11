import 'package:angaryos/helper/BaseHelper.dart';
import 'package:angaryos/helper/ThemeHelper.dart';
import 'package:angaryos/view/AnnouncementsScreen.dart';
import 'package:angaryos/view/ContactScreen.dart';
import 'package:angaryos/view/DataTableScreen.dart';
import 'package:angaryos/view/LoginScreen.dart';
import 'package:angaryos/view/ProfileScreen.dart';
import 'package:angaryos/view/MainScreen.dart';
import 'package:flutter/material.dart';

import 'SessionHelper.dart';

class MenuHelper {
  static List<dynamic> bottomMenuData = [];

  //****    Variables    *****//
  static List<dynamic> getBottomMenuData() {
    if (bottomMenuData.length != 0) return bottomMenuData;

    bottomMenuData = [
      {
        "type": "single",
        "title": "Anasayfa",
        "color": textWhite,
        "icon": Icons.home,
        "route": "/",
        "pageWidget": MainPage()
      },
      {
        "type": "single",
        "title": "Duyuru",
        "color": textWhite,
        "icon": Icons.campaign,
        "route": "announcements",
        "pageWidget": AnnouncementsPage()
      },
      {
        "type": "single",
        "title": "İletişim",
        "color": textWhite,
        "icon": Icons.info_outline,
        "route": "contact",
        "pageWidget": ContactPage()
      },
      {
        "type": "single",
        "title": SessionHelper.user == null ? "Giriş" : "Profil",
        "color": textWhite,
        "icon": Icons.face,
        "route": SessionHelper.user == null ? "login" : "profile",
        "pageWidget": SessionHelper.user == null ? LoginPage() : ProfilePage()
      },
    ];

    return bottomMenuData;
  }

  static Future<List<dynamic>> getSideMenuData(String searchedText) async {
    List<dynamic> sideMenuData = [];

    if (SessionHelper.user == null) return sideMenuData;

    for (dynamic tableGroup in SessionHelper.user["menu"]["tableGroups"]) {
      dynamic temp = {
        "type": "multi",
        "opened": searchedText.length > 0,
        "title": tableGroup["name_basic"],
        "color": textWhite,
        "icon": convertToIconFromZmdiIconName(tableGroup["icon"]),
        "children": []
      };

      List<dynamic>? tables =
          SessionHelper.user["menu"]["tables"][tableGroup["id"].toString()];

      if (tables != null)
        for (dynamic table in tables) {
          if (searchedText != "") {
            String d = table["display_name"].toString().toLowerCase();
            String n = table["name"].toString().toLowerCase();

            if (!d.contains(searchedText) && !n.contains(searchedText))
              continue;
          }

          temp["children"].add({
            "type": "single",
            "title": table["display_name"],
            "color": textWhite,
            "icon": Icons.arrow_right,
            "route": "/",
            "pageWidget": DataTablePage(tableName: table["name"])
          });
        }

      if (temp["children"].length == 0) continue;

      sideMenuData.add(temp);
    }

    return sideMenuData;
  }

  static IconData convertToIconFromZmdiIconName(String zmdiIconName) {
    zmdiIconName = zmdiIconName.replaceAll('zmdi ', '');
    switch (zmdiIconName) {
      case "zmdi-settings":
        return Icons.settings;
      case "zmdi-accounts":
        return Icons.people;
      case "zmdi-grid":
        return Icons.grid_on;
      case "zmdi-lock":
        return Icons.lock;
      case "zmdi-pin-drop":
        return Icons.pin_drop;
      case "zmdi-map":
        return Icons.map;
      case "zmdi-repeat":
        return Icons.repeat;
      case "zmdi-view-dashboard":
        return Icons.dashboard;
      case "zmdi-view-subtitles":
        return Icons.subtitles;
      default:
        return Icons.image_aspect_ratio;
    }
  }
}
