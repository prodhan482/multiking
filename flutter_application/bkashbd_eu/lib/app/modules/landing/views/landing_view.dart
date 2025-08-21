import 'dart:async';

import 'package:bkashbd_eu/app/modules/landing/providers/landing_provider.dart';
import 'package:bkashbd_eu/app/routes/app_pages.dart';
import 'package:bkashbd_eu/utils/app_manager.dart';
import 'package:bkashbd_eu/utils/hex_color.dart';
import 'package:bkashbd_eu/utils/img.dart';
//import 'package:bkashbd_eu/utils/my_text.dart';
import 'package:bkashbd_eu/utils/primitive_extensions.dart';
import 'package:flutter/material.dart';
import 'package:package_info_plus/package_info_plus.dart';

import 'package:get/get.dart';
import 'package:url_launcher/url_launcher.dart';

import '../controllers/landing_controller.dart';

class LandingView extends GetView<LandingController> {
  String version = "";

  startTime(BuildContext context) async {
    LandingProvider _landingProvider = LandingProvider();
    PackageInfo packageInfo = await PackageInfo.fromPlatform();
    version = packageInfo.version;

    return Timer(Duration(seconds: 2), () {
      _landingProvider.getAppVersion().then((response) {
        var mandatoryUpdateTo =
            int.parse(response.mandatoryUpdateTo!.replaceAll(".", ""));
        var allowVersionUpTo =
            int.parse(response.allowVersionUpTo!.replaceAll(".", ""));
        var appVersion = int.parse(version.replaceAll(".", ""));
        if (appVersion < mandatoryUpdateTo) {
          // You must have to Update App. Or it will now Run.
          _showDialog(
              true,
              context,
              "Need Mandatory Update",
              "You cannot run this application. Press the button to download new application.",
              "Download", () {
            _launchURL(response.download_url!);
          }, "", () {});
        } else if (!(appVersion < mandatoryUpdateTo) &&
            (appVersion < allowVersionUpTo)) {
          // App can run but you need to update it as soon as possible.
          _showDialog(
              true,
              context,
              "New Update Available",
              "New Update have been available. Please download now.",
              "Skip",
              () {
                Get.offAndToNamed((AppManager.instance.isLoggedIn()
                    ? Routes.HOME
                    : Routes.LOGIN));
              },
              "Download",
              () {
                _launchURL(response.download_url!);
              });
        } else {
          if (AppManager.instance.isLoggedIn())
            AppManager.instance.setUserDataFromSaved();

          Get.offAndToNamed(
              (AppManager.instance.isLoggedIn() ? Routes.HOME : Routes.LOGIN));
        }
      }, onError: (err) {
        // Show Dialog
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    startTime(context);

    return Scaffold(
      backgroundColor: Colors.white,
      body: Align(
        child: Container(
          width: 180,
          height: 150,
          alignment: Alignment.center,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: <Widget>[
              Container(
                height: 100.h,
                child: Image.asset(Img.get('white_bg_logo.png')),
              ),
              Container(height: 10.h),
              /*Text("", style: MyText.headline(context)!.copyWith(
                    color: Colors.grey[800], fontWeight: FontWeight.w600
                )),
                Text("Flutter Version", style: MyText.body1(context)!.copyWith(
                    color: Colors.grey[500]
                )),*/
              Container(height: 20.h),
              Container(
                height: 5.h,
                child: LinearProgressIndicator(
                  valueColor:
                      AlwaysStoppedAnimation<Color>(HexColor("#0F4EDC")),
                  backgroundColor: Colors.grey[300],
                ),
              ),
            ],
          ),
        ),
        alignment: Alignment.center,
      ),
    );
  }

  Future<void> _showDialog(
      bool Disablepop,
      BuildContext context,
      String title,
      String message,
      String btt1Txt,
      Function callback1,
      String btt2Txt,
      Function callback2) async {
    return showDialog<void>(
      context: context,
      barrierDismissible: false, // user must tap button!
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(title),
          content: SingleChildScrollView(
            child: ListBody(
              children: <Widget>[
                Text(message),
              ],
            ),
          ),
          actions: <Widget>[
            TextButton(
              child: Text(btt1Txt,
                  style: TextStyle(
                      color: Colors.black,
                      fontWeight: FontWeight.bold,
                      fontSize: 16.sp)),
              onPressed: () {
                if (!Disablepop) Navigator.of(context).pop();
                callback1();
              },
            ),
            (btt2Txt.length > 2
                ? TextButton(
                    child: Text(btt2Txt,
                        style: TextStyle(
                            color: Colors.black,
                            fontWeight: FontWeight.bold,
                            fontSize: 16.sp)),
                    onPressed: () {
                      if (!Disablepop) Navigator.of(context).pop();
                      callback2();
                    },
                  )
                : Container())
          ],
        );
      },
    );
  }

  void _launchURL(String _url) async {
    if (!await launch(_url)) throw 'Could not launch $_url';
  }
}
