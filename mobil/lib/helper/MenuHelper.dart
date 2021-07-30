import 'package:angaryos/helper/ThemeHelper.dart';
import 'package:angaryos/view/AnnouncementsScreen.dart';
import 'package:angaryos/view/ContactScreen.dart';
import 'package:angaryos/view/LoginScreen.dart';
import 'package:angaryos/view/ProfileScreen.dart';
import 'package:angaryos/view/MainScreen.dart';
import 'package:flutter/material.dart';

import 'SessionHelper.dart';

class MenuHelper {
  //****    Variables    *****//
  static List<dynamic> getBottomMenuData() {
    return [
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
  }

  static List<dynamic> getSideMenuData() {
    return [
      {
        "type": "single",
        "title": "Göstergeler",
        "color": textWhite,
        "icon": Icons.space_dashboard,
        "route": "/",
        "pageWidget": ContactPage()
      },
      {
        "type": "multi",
        "title": "Ayarlar",
        "color": textWhite,
        "icon": Icons.settings,
        "children": [
          {
            "type": "single",
            "title": "Göstergeler2",
            "color": textWhite,
            "icon": Icons.space_dashboard,
            "route": "/",
            "pageWidget": ContactPage()
          },
          {
            "type": "single",
            "title": "Göstergeler3",
            "color": textWhite,
            "icon": Icons.space_dashboard,
            "route": "/",
            "pageWidget": ContactPage()
          },
        ]
      }
    ];
  }
}
