import 'package:angaryos/helper/BaseHelper.dart';
import 'package:angaryos/helper/SessionHelper.dart';
import 'package:angaryos/view/LayoutScreen.dart';
import 'package:flutter/material.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return LayoutScreen(pageWidget: ProfilePage());
  }
}

class ProfilePage extends StatefulWidget {
  ProfilePage({Key? key}) : super(key: key);

  @override
  _ProfilePageState createState() => _ProfilePageState();
}

class _ProfilePageState extends State<ProfilePage> {
  @override
  void initState() {
    super.initState();
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
            child: ElevatedButton.icon(
                onPressed: () async {
                  if (await SessionHelper.logout())
                    BaseHelper.navigate("login", context);
                  else
                    BaseHelper.messageBox(
                        context,
                        "Bir hata oluştu! Lütfen daha sonra tekrar deneyin",
                        "",
                        "error");
                },
                icon: Icon(Icons.add),
                label: Text(tr("Çıkış Yap"))),
          )
        ],
      ),
    );
  }
}
