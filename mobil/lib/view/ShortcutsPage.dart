import 'package:angaryos/helper/BaseHelper.dart';
import 'package:flutter/material.dart';

class ShortcutsPage extends StatelessWidget {
  final String? data;

  const ShortcutsPage({Key? key, @required this.data}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(tr("Kısayollar")),
      ),
      body: Center(
        child: Column(
          children: [
            Text("kısayollarım"),
            Text(data ?? ""),
            ElevatedButton.icon(
                onPressed: () {
                  BaseHelper.navigate("shortcutsUpdate", context);
                },
                icon: Icon(Icons.add),
                label: Text(tr("Kısayolları Düzenle")))
          ],
        ),
      ),
    );
  }
}
