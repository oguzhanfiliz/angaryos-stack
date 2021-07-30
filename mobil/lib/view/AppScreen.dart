import 'package:angaryos/helper/BaseHelper.dart';
import 'package:angaryos/helper/SessionHelper.dart';
import 'package:angaryos/helper/ThemeHelper.dart';
import 'package:angaryos/route/RouteGenerator.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';

import 'package:angaryos/view/MainScreen.dart';

class App extends StatefulWidget {
  // This widget is the root of your application.
  @override
  _AppState createState() => _AppState();
}

class _AppState extends State<App> {
  preLoad(BuildContext context) async {
    BaseHelper.setActiveContext(context);
  }

  @override
  void initState() {
    super.initState();
    BaseHelper.setActiveContext(context);
    BaseHelper.loadLanguage(BaseHelper.defaultLanguage);
    SessionHelper.fillUserFromLocal();
    if (!kIsWeb) BaseHelper.firebaseDynamicLinkControl();
  }

  getBasePage(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: BaseHelper.applicationTitle,
      theme: ThemeData.dark().copyWith(
        scaffoldBackgroundColor: bgColor,
        textTheme: Theme.of(context).textTheme.apply(
            fontFamily: 'Open Sans',
            bodyColor: Colors.white,
            displayColor: Colors.white),
        canvasColor: secondaryColor,
      ),
      home: MainScreen(),
      initialRoute: "/" + BaseHelper.defaultLanguage + "/",
      onGenerateRoute: RouteGenerator.generatoeRoute,
    );
  }

  @override
  Widget build(BuildContext context) {
    this.preLoad(context);
    return getBasePage(context);
  }
}
