import 'package:angaryos/helper/BaseHelper.dart';
import 'package:angaryos/view/LayoutScreen.dart';
import 'package:flutter/material.dart';

class DataTableScreen extends StatefulWidget {
  String tableName;
  DataTableScreen({Key? key, required this.tableName}) : super(key: key);

  @override
  _DataTableScreenState createState() => _DataTableScreenState();
}

class _DataTableScreenState extends State<DataTableScreen> {
  @override
  Widget build(BuildContext context) {
    return LayoutScreen(pageWidget: DataTablePage(tableName: widget.tableName));
  }
}

class DataTablePage extends StatefulWidget {
  String tableName;
  DataTablePage({Key? key, required this.tableName}) : super(key: key);

  @override
  _DataTablePageState createState() => _DataTablePageState();
}

class _DataTablePageState extends State<DataTablePage> {
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
            child: Text(tr("Bu Sayfa Yapım Aşamasında")),
          )
        ],
      ),
    );
  }
}
