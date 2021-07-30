import 'package:angaryos/helper/BaseHelper.dart';
import 'package:angaryos/helper/MenuHelper.dart';
import 'package:angaryos/helper/ResponsiveHelper.dart';
import 'package:angaryos/helper/ThemeHelper.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'dart:html' as html;

class SideMenu extends StatefulWidget {
  SideMenu({Key? key}) : super(key: key);

  @override
  _SideMenuState createState() => _SideMenuState();
}

class _SideMenuState extends State<SideMenu> {
  getMenuRow(dynamic item, int deep) {
    return Padding(
      padding: EdgeInsets.only(left: 25.0 * deep),
      child: Row(
        children: <Widget>[
          Icon(item["icon"]),
          Padding(
            padding: EdgeInsets.only(left: 8.0),
            child: Text(
              item["title"],
              style: TextStyle(fontSize: 12),
            ),
          )
        ],
      ),
    );
  }

  getMenuItem(dynamic item, int deep) {
    return ListTile(
      title: getMenuRow(item, deep),
      onTap: () {
        BaseHelper.setPageWidgetOnLayout!(item);
      },
    );
  }

  getMenuTreeRecursive(dynamic item, [int? deep]) {
    deep ??= 0;
    if (item["type"] == "multi") {
      return ExpansionTile(
        title: getMenuRow(item, deep),
        textColor: item["color"],
        iconColor: item["color"],
        children: [
          for (dynamic subItem in item["children"])
            getMenuTreeRecursive(subItem, deep + 1),
        ],
      );
    } else {
      return getMenuItem(item, deep);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Drawer(
        child: ListView(
      children: [
        SizedBox(
          height: 20,
        ),
        if (ResponsiveHelper.isDesktop(context))
          for (var item in MenuHelper.getBottomMenuData())
            getMenuTreeRecursive(item),
        if (ResponsiveHelper.isDesktop(context))
          Divider(
            height: 1,
            color: dividerLight,
          ),
        for (var item in MenuHelper.getSideMenuData())
          getMenuTreeRecursive(item),
        Divider(
          height: 1,
          color: dividerLight,
        ),
        ListTile(
          title: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                "v0.0.1",
                style: TextStyle(fontSize: 10, color: textGray),
              ),
              Text(
                '© 2021, omersavas',
                style: TextStyle(fontSize: 10, color: textGray),
              )
            ],
          ),
          onTap: () {},
        ),
      ],
    ));
  }
}

class DrawerListTile extends StatelessWidget {
  const DrawerListTile({
    Key? key,
    // For selecting those three line once press "Command+D"
    required this.title,
    required this.icon,
    required this.press,
  }) : super(key: key);

  final String title;
  final IconData icon;
  final VoidCallback press;

  @override
  Widget build(BuildContext context) {
    return ListTile(
      onTap: press,
      horizontalTitleGap: 0.0,
      leading: Icon(icon),
      title: Text(
        title,
        style: TextStyle(color: Colors.black),
      ),
    );
  }
}
