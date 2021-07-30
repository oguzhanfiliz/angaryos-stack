import 'package:flutter/material.dart';

class ResponsiveHelper extends StatelessWidget {
  final Widget mobile;
  final Widget? tablet;
  final Widget desktop;

  const ResponsiveHelper({
    Key? key,
    required this.mobile,
    this.tablet,
    required this.desktop,
  }) : super(key: key);

  static bool isMobile(BuildContext context) =>
      MediaQuery.of(context).size.width < 850;

  static bool isTablet(BuildContext context) =>
      MediaQuery.of(context).size.width < 1100 &&
      MediaQuery.of(context).size.width >= 850;

  static bool isDesktop(BuildContext context) =>
      MediaQuery.of(context).size.width >= 1100;

  static double getResponsiveWith(BuildContext context,
      [double? mobilePadding, double? tabletWidth, double? desktopWidth]) {
    mobilePadding ??= 40.0;
    tabletWidth ??= 500.0;
    desktopWidth ??= tabletWidth;

    double wd = MediaQuery.of(context).size.width;

    if (isMobile(context)) {
      return wd - mobilePadding * 2;
    } else if (isTablet(context)) {
      return tabletWidth;
    } else {
      return desktopWidth;
    }
  }

  @override
  Widget build(BuildContext context) {
    if (isDesktop(context))
      return desktop;
    else if (isTablet(context) && tablet != null)
      return tablet!;
    else
      return mobile;
  }
}
