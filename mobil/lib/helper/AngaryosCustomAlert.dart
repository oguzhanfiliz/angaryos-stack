import 'package:angaryos/helper/ResponsiveHelper.dart';
import 'package:angaryos/helper/ThemeHelper.dart';
import 'package:flutter/material.dart';

import 'BaseHelper.dart';

class AngaryosCustomAlert extends StatelessWidget {
  String title;
  String? text;
  Color? color;
  String? buttonText;
  IconData? icon;
  String? type;

  AngaryosCustomAlert(this.title, this.text,
      [this.type, this.color, this.buttonText, this.icon]) {
    text ??= "";
    if (type != null) {
      buttonText ??= tr("Tamam");

      switch (type) {
        case "success":
          color ??= Colors.green;
          icon ??= Icons.check_circle;
          break;
        case "warning":
          color ??= Colors.orangeAccent;
          icon ??= Icons.error_outline;
          break;
        case "info":
          color ??= Colors.lightBlueAccent;
          icon ??= Icons.info_outline;
          break;
        case "error":
          color ??= Colors.redAccent;
          icon ??= Icons.error;
          break;
        default:
      }
    }

    color ??= Colors.green;
    buttonText ??= tr("Tamam");
    icon ??= Icons.check_circle;
  }

  @override
  Widget build(BuildContext context) {
    return Dialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(4)),
      child: Container(
        height: 250,
        width: ResponsiveHelper.getResponsiveWith(context),
        child: Column(
          children: [
            Expanded(
              child: Container(
                color: const Color(0x42424200),
                child: Icon(
                  icon,
                  size: 60,
                  color: color,
                ),
              ),
            ),
            Expanded(
              child: Container(
                color: color,
                child: SizedBox.expand(
                  child: Padding(
                    padding: const EdgeInsets.all(15.0),
                    child: SingleChildScrollView(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            title,
                            style: TextStyle(
                              color: Colors.white,
                            ),
                            textAlign: TextAlign.center,
                          ),
                          if (text!.length > 0)
                            SizedBox(
                              height: 10,
                            ),
                          if (text!.length > 0) Text(text!),
                          if (text!.length > 0)
                            SizedBox(
                              height: 10,
                            ),
                          if (text!.length == 0)
                            SizedBox(
                              height: 30,
                            ),
                          ElevatedButton(
                              onPressed: () {
                                Navigator.of(context).pop();
                              },
                              style: ButtonStyle(
                                backgroundColor:
                                    MaterialStateProperty.all<Color>(textWhite),
                                foregroundColor:
                                    MaterialStateProperty.all<Color>(
                                        Colors.black),
                                overlayColor:
                                    MaterialStateProperty.resolveWith<Color>(
                                  (Set<MaterialState> states) {
                                    if (states.contains(MaterialState.hovered))
                                      return Colors.blue.withOpacity(0.04);
                                    if (states
                                            .contains(MaterialState.focused) ||
                                        states.contains(MaterialState.pressed))
                                      return Colors.blue.withOpacity(0.12);
                                    return Colors.blue.withOpacity(
                                        0.02); // Defer to the widget's default.
                                  },
                                ),
                              ),
                              child: Text(tr("Tamam")))

                          /*RaisedButton(
                            color: Colors.white,
                            child: Text('Okay'),
                            onPressed: () => {Navigator.of(context).pop()},
                          )*/
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            )
          ],
        ),
      ),
    );
  }
}
