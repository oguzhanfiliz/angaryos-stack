import 'package:angaryos/view/LayoutScreen.dart';
import 'package:flutter/material.dart';

class AnnouncementsScreen extends StatelessWidget {
  const AnnouncementsScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return LayoutScreen(pageWidget: AnnouncementsPage());
  }
}

class AnnouncementsPage extends StatefulWidget {
  AnnouncementsPage({Key? key}) : super(key: key);

  @override
  _AnnouncementsPageState createState() => _AnnouncementsPageState();
}

class _AnnouncementsPageState extends State<AnnouncementsPage> {
  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      child: Text("duyurularım"),
    );
  }
}
