import 'package:angaryos/helper/BaseHelper.dart';
import 'package:angaryos/helper/MenuHelper.dart';
import 'package:angaryos/helper/ResponsiveHelper.dart';
import 'package:angaryos/helper/SessionHelper.dart';
import 'package:angaryos/helper/ThemeHelper.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';

class SideMenu extends StatefulWidget {
  SideMenu({Key? key}) : super(key: key);

  @override
  _SideMenuState createState() => _SideMenuState();
}

class _SideMenuState extends State<SideMenu> {
  String searchedText = "", lastSearchedText = "temp";
  List<Widget> menuData = [];

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
        initiallyExpanded: item["opened"],
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

  List<Widget> getBottomMenu() {
    List<Widget> bottomMenuData = [];

    for (var item in MenuHelper.getBottomMenuData())
      bottomMenuData.add(getMenuTreeRecursive(item));

    return bottomMenuData;
  }

  Future<List<Widget>> getSideMenu(String searchedText) async {
    List<Widget> sideMenuData = [];

    List<dynamic> temp = await MenuHelper.getSideMenuData(searchedText);
    for (var item in temp) sideMenuData.add(getMenuTreeRecursive(item));

    return sideMenuData;
  }

  Future<List<Widget>> getMenu() async {
    if (searchedText != lastSearchedText) menuData = [];
    if (menuData.length != 0) return menuData;

    menuData = [
      SizedBox(
        height: 20,
      ),
      Row(
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(vertical: 0, horizontal: 10),
            child: Image.network(
              BaseHelper.logoUrl100,
              width: 55,
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 0, 0, 0),
            child: Column(
              children: [
                Text("Angaryos", style: TextStyle(fontSize: 24)),
                SizedBox(
                  height: 5,
                ),
                Text(
                  "Mobile",
                  style: TextStyle(letterSpacing: 12),
                )
              ],
            ),
          )
        ],
      ),
      SizedBox(
        height: 20,
      ),
      Divider(
        height: 1,
        color: dividerLight,
      )
    ];

    if (ResponsiveHelper.isDesktop(context)) {
      menuData += getBottomMenu();
      menuData += [
        Divider(
          height: 1,
          color: dividerLight,
        )
      ];
    }

    if (SessionHelper.user != null) {
      menuData += [
        Padding(
          padding: const EdgeInsets.fromLTRB(15, 10, 15, 10),
          child: TextField(
              onChanged: (value) {
                setState(() {
                  searchedText = value.toLowerCase();
                });
              },
              style: TextStyle(fontSize: 16.0),
              decoration: InputDecoration(
                contentPadding: EdgeInsets.fromLTRB(10, 0, 10, 0),
                border: InputBorder.none,
                labelText: tr('Filtrele'),
              )),
        ),
        Divider(
          height: 1,
          color: dividerLight,
        )
      ];

      menuData += await getSideMenu(searchedText);
    }

    menuData += [
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
    ];

    lastSearchedText = searchedText;

    return menuData;
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<Widget>>(
      future: getMenu(),
      builder: (BuildContext context, AsyncSnapshot<List<Widget>> ss) {
        if (ss.hasData)
          return Drawer(
              child: ListView(
            children: ss.data!,
          ));
        else if (ss.hasError)
          return Drawer(
              child: ListView(
            children: [Text(tr("Menü çizilemedi!"))],
          ));
        else
          return Drawer(
              child: ListView(
            children: [],
          ));
      },
    );
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
