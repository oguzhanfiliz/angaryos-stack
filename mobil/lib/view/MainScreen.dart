import 'package:angaryos/helper/BaseHelper.dart';
import 'package:angaryos/helper/ResponsiveHelper.dart';
import 'package:angaryos/helper/SessionHelper.dart';
import 'package:angaryos/helper/ThemeHelper.dart';
import 'package:angaryos/view/LayoutScreen.dart';
import 'package:flutter/material.dart';
import 'SideMenu.dart';

class MainScreen extends StatelessWidget {
  const MainScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return LayoutScreen(pageWidget: MainPage());
  }
}

class MainPage extends StatefulWidget {
  MainPage({Key? key}) : super(key: key);

  @override
  _MainPageState createState() => _MainPageState();
}

class _MainPageState extends State<MainPage> {
  @override
  void initState() {
    super.initState();
  }

  List<dynamic>? news = null;

  fillNewsFromPipe() {
    var temp = BaseHelper.readFromPipe("news");
    if (temp == null) return false;

    setState(() {
      news = temp;
    });

    return true;
  }

  fillNewsFromServer() async {
    String url = SessionHelper.getListPageUrl("public_contents", true, true);
    var rt = await SessionHelper.httpGetJsonA(url);
    setState(() {
      news = rt["records"];
    });

    BaseHelper.writeToPipe("news", rt["records"]);
  }

  fillNews() async {
    //if (news != null) return;
    //if (fillNewsFromPipe()) return;
    //fillNewsFromServer();
  }

  @override
  Widget build(BuildContext context) {
    fillNews();

    return Text("data");
  }
}
