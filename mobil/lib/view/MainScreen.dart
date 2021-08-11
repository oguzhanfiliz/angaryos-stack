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
    fillNews();
  }

  List<dynamic>? news = null;

  fillNewsFromLocal() async {
    try {
      var temp = await BaseHelper.readFromLocal("news");
      if (temp == null) return false;

      setState(() {
        news = temp;
      });

      return true;
    } catch (e) {
      return false;
    }
  }

  fillNewsFromPipe() {
    try {
      var temp = BaseHelper.readFromPipe("news");
      if (temp == null) return false;

      setState(() {
        news = temp;
      });

      return true;
    } catch (e) {
      return false;
    }
  }

  fillNewsFromServer() async {
    try {
      String url = SessionHelper.getListPageUrl("public_contents", true, true);
      var rt = await SessionHelper.httpGetJsonA(url);
      setState(() {
        news = rt["records"];
      });

      BaseHelper.writeToLocal("news", rt["records"], 1000 * 60 * 60);
      BaseHelper.writeToPipe("news", rt["records"]);
    } catch (e) {}
  }

  fillNews() async {
    if (news != null) return;
    if (fillNewsFromPipe()) return;
    if (await fillNewsFromLocal()) return;
    fillNewsFromServer();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      child: Column(
        children: [
          SizedBox(
            height: 20,
          ),
          Center(
            child: Text(tr("Bu Sayfa Yapım Aşamasında")),
          )
        ],
      ),
    );
  }
}
