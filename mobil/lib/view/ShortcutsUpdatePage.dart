import 'package:angaryos/helper/BaseHelper.dart';
import 'package:flutter/material.dart';

class ShortcutsUpdatePage extends StatelessWidget {
  final String? data;

  const ShortcutsUpdatePage({Key? key, @required this.data}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(tr("Kısayolları Düzenle")),
      ),
      body: Center(
        child: Column(
          children: [Text("kısayollarımı düzenle"), Text(data ?? "")],
        ),
      ),
    );
  }
}
