import 'package:bkashbd_eu/utils/constants.dart';
import 'package:bkashbd_eu/utils/primitive_extensions.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';

class NHTheme {
  static ThemeData theme() {
    return ThemeData(
      scaffoldBackgroundColor: Colors.white,
      fontFamily: "Exo",
      appBarTheme: appBarTheme(),
      // inputDecorationTheme: inputDecorationTheme(),
      textTheme: textTheme(),
      visualDensity: VisualDensity.adaptivePlatformDensity,
    );
  }

  static InputDecorationTheme inputDecorationTheme() {
    TextStyle hintStyle = TextStyle(
      color: AppConstants.textColorGrey64.withOpacity(0.30),
      fontSize: 17.sp,
      fontFamily: AppFonts.dnpShueiMGoStd,
      fontWeight: FontWeight.w400,
      letterSpacing: 0.0,
    );

    TextStyle errorStyle = TextStyle(
      color: AppConstants.primaryTextColorRed,
      fontSize: 17.sp,
      fontFamily: AppFonts.dnpShueiMGoStd,
      fontWeight: FontWeight.w400,
      letterSpacing: 0.0,
    );

    return InputDecorationTheme(
      enabledBorder: outlineInputBorder(AppConstants.borderColorGrey),
      border: outlineInputBorder(AppConstants.borderColorGrey),
      focusedBorder: outlineFocusedBorder(AppConstants.switchDiaryColor),
      errorBorder: outlineErrorInputBorder(AppConstants.primaryTextColorRed),
      focusedErrorBorder:
          outlineErrorInputBorder(AppConstants.primaryTextColorRed),
      hintStyle: hintStyle,
      errorStyle: errorStyle,
    );
  }

  static OutlineInputBorder outlineInputBorder(Color color) {
    return OutlineInputBorder(borderSide: BorderSide(color: color, width: 1.s));
  }

  static OutlineInputBorder outlineFocusedBorder(Color color) {
    return OutlineInputBorder(
      borderSide: BorderSide(color: color, width: 1.s),
    );
  }

  static OutlineInputBorder outlineErrorInputBorder(Color color) {
    return OutlineInputBorder(
      borderSide: BorderSide(color: color),
    );
  }

  static TextTheme textTheme() {
    return TextTheme(
      headline1: headTextStyle(1),
      headline2: headTextStyle(2),
      headline3: headTextStyle(3),
      headline4: headTextStyle(4),
      headline5: headTextStyle(5),
      headline6: headTextStyle(6),
      subtitle1: subTitleStyle(1),
      subtitle2: subTitleStyle(2),
      bodyText1: bodyTextStyle(1),
      bodyText2: bodyTextStyle(2),
    );
  }

  // styles start from 1
  static TextStyle headTextStyle(int style) {
    return TextStyle(
        color: AppConstants.primaryTextColorBlack,
        fontFamily: AppFonts.brandonGrotesque,
        fontWeight: FontWeight.w600);
  }

  static TextStyle subTitleStyle(int style) {
    return TextStyle(
        color: AppConstants.primaryTextColorBlack,
        fontFamily: AppFonts.brandonGrotesque,
        fontWeight: FontWeight.w600);
  }

  static TextStyle bodyTextStyle(int style) {
    return TextStyle(
        color: AppConstants.primaryTextColorBlack,
        fontSize: 16.sp,
        fontFamily: AppFonts.brandonGrotesque,
        fontWeight: FontWeight.w500);
  }

  static AppBarTheme appBarTheme() {
    return AppBarTheme(
      color: Colors.green,
      elevation: 0,
      iconTheme: IconThemeData(color: Colors.white),
      textTheme: TextTheme(
        headline6: TextStyle(
          color: Colors.white,
        ),
      ),
    );
  }
}
