import 'package:angaryos/helper/BaseHelper.dart';
import 'package:angaryos/helper/MenuHelper.dart';
import 'package:angaryos/helper/ThemeHelper.dart';
import 'package:bubble_bottom_bar/bubble_bottom_bar.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:angaryos/view/SideMenu.dart';
import 'package:angaryos/helper/ResponsiveHelper.dart';

// ignore: must_be_immutable
class LayoutScreen extends StatefulWidget {
  LayoutScreen({Key? key, required this.pageWidget}) : super(key: key);

  Widget pageWidget;

  @override
  _LayoutScreenState createState() => _LayoutScreenState();
}

class _LayoutScreenState extends State<LayoutScreen> {
  @override
  void initState() {
    super.initState();
  }

  ResponsiveHelper? responsiveLayout;
  LayoutPage? layoutWidget = null;
  LayoutPage? mobile;
  Widget? tablet;
  Widget? desktop;

  setPageWidget(dynamic menuItem) {
    setState(() {
      widget.pageWidget = menuItem["pageWidget"];
      fillVariables(true);

      //TODO incele
      //if (kIsWeb) BaseHelper.navigate(menuItem["route"], context);
    });
  }

  fillVariables([bool? force]) {
    force ??= false;
    if (force) layoutWidget = null;

    if (layoutWidget != null) return;

    fillWidgets();
    fillResponsiveLayout();
    BaseHelper.setPageWidgetOnLayout = this.setPageWidget;
  }

  fillWidgets() {
    layoutWidget = LayoutPage(pageWidget: widget.pageWidget);

    mobile = layoutWidget;

    tablet = Row(
      children: [
        Expanded(
          flex: 2,
          child: SideMenu(),
        ),
        Expanded(
          flex: 6,
          child: layoutWidget!,
        ),
      ],
    );

    desktop = Row(
      children: [
        Expanded(
          flex: 2,
          child: SideMenu(),
        ),
        Expanded(
          flex: 6,
          child: layoutWidget!,
        ),
      ],
    );
  }

  fillResponsiveLayout() {
    this.responsiveLayout = getResponsiveLayout();
  }

  getResponsiveLayout() {
    return ResponsiveHelper(
      mobile: mobile!,
      tablet: tablet!,
      desktop: desktop!,
    );
  }

  @override
  Widget build(BuildContext context) {
    fillVariables();

    return Scaffold(
      body: this.responsiveLayout,
      floatingActionButton: ShortcutButton(),
      floatingActionButtonLocation: FloatingActionButtonLocation.endDocked,
      bottomNavigationBar:
          ResponsiveHelper.isDesktop(context) ? null : MainBottomBarMenu(),
    );
  }
}

class LayoutPage extends StatefulWidget {
  LayoutPage({Key? key, required this.pageWidget}) : super(key: key);
  final Widget pageWidget;

  @override
  _LayoutPageState createState() => _LayoutPageState();
}

class _LayoutPageState extends State<LayoutPage> {
  GlobalKey<ScaffoldState> _scaffoldKey = GlobalKey();

  @override
  void initState() {
    super.initState();
    BaseHelper.setActiveContext(context);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
        key: _scaffoldKey,
        drawer: ConstrainedBox(
          constraints: BoxConstraints(maxWidth: 250),
          child: SideMenu(),
        ),
        body: Container(
          color: bgColor,
          child: SafeArea(
            right: false,
            child: Column(
              children: [
                if (ResponsiveHelper.isMobile(context))
                  IconButton(
                    icon: Icon(
                      Icons.menu,
                      color: textWhite,
                    ),
                    onPressed: () {
                      _scaffoldKey.currentState!.openDrawer();
                    },
                  ),
                SingleChildScrollView(
                  child: Container(
                      width: MediaQuery.of(context).size.width,
                      //height: MediaQuery.of(context).size.height,
                      alignment: Alignment.topLeft,
                      child: widget.pageWidget),
                ),
              ],
            ),
          ),
        ));
  }
}

class ShortcutButton extends StatelessWidget {
  const ShortcutButton({
    Key? key,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(
          bottom: ResponsiveHelper.isDesktop(context) ? defaultPadding : 0),
      child: FloatingActionButton(
        onPressed: () {
          BaseHelper.navigate("shortcuts", context);
        },
        child: Icon(Icons.star_purple500, color: textWhite),
        backgroundColor: primaryColor,
      ),
    );
  }
}

class MainBottomBarMenu extends StatefulWidget {
  MainBottomBarMenu({Key? key}) : super(key: key);

  @override
  _MainBottomBarMenuState createState() => _MainBottomBarMenuState();
}

class _MainBottomBarMenuState extends State<MainBottomBarMenu> {
  bool started = false;
  int currentIndex = 0;
  List<BubbleBottomBarItem>? menuList = null;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance!.addPostFrameCallback((_) async {
      initializeCurrentIndex();
    });
  }

  getButtomMenuItem(String text, Color color, IconData icon) {
    return BubbleBottomBarItem(
        backgroundColor: color,
        icon: Icon(
          icon,
          color: color,
        ),
        activeIcon: Icon(
          icon,
          color: color,
        ),
        title: Text(text, style: TextStyle(color: color)));
  }

  getButtomMenuItems() {
    if (this.menuList == null) {
      this.menuList = [];
      var temp = MenuHelper.getBottomMenuData();
      for (dynamic data in temp)
        this
            .menuList!
            .add(getButtomMenuItem(data["title"], data["color"], data["icon"]));
    }

    return this.menuList;
  }

  initializeCurrentIndex() async {
    if (started) return;

    String? path = ModalRoute.of(context)!.settings.name;
    if (path == null) return;
    if (path.length < 2) return;

    if (path.substring(0, 4) == "/" + BaseHelper.currentLanguage + "/")
      path = path.substring(4);
    if (path.length == 0) return;

    if (path.substring(path.length - 1) == "?")
      path = path.substring(0, path.length - 1);

    int i = 0;
    for (dynamic data in MenuHelper.getBottomMenuData()) {
      if (data["route"] == path) {
        setState(() {
          currentIndex = i;
        });
        return;
      }
      i++;
    }

    started = true;
  }

  @override
  Widget build(BuildContext context) {
    return BubbleBottomBar(
      opacity: .13,
      currentIndex: currentIndex,
      onTap: (i) {
        setState(() {
          currentIndex = i;
          var data = MenuHelper.getBottomMenuData();
          BaseHelper.setPageWidgetOnLayout!(data[i]);
        });
      },
      borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
      elevation: 5,
      fabLocation: BubbleBottomBarFabLocation.end,
      hasNotch: true,
      hasInk: true,
      inkColor: textGray,
      backgroundColor: secondaryColor,
      items: getButtomMenuItems(),
    );
  }
}
