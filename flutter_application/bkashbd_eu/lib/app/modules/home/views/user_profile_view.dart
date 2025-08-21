import 'package:bkashbd_eu/app/constants.dart';
import 'package:bkashbd_eu/app/modules/home/controllers/home_controller.dart';
import 'package:bkashbd_eu/app/routes/app_pages.dart';
import 'package:bkashbd_eu/libraries/bmprogresshud/bmprogresshud.dart';
import 'package:bkashbd_eu/utils/app_manager.dart';
import 'package:bkashbd_eu/utils/circle_image.dart';
import 'package:bkashbd_eu/utils/hex_color.dart';
import 'package:bkashbd_eu/utils/img.dart';
import 'package:bkashbd_eu/utils/my_text.dart';
import 'package:bkashbd_eu/utils/primitive_extensions.dart';
import 'package:flutter/material.dart';

import 'package:get/get.dart';

class UserProfileView extends GetView<HomeController> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
        body: SingleChildScrollView(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: <Widget>[
          Container(
            child: Row(
              children: <Widget>[
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    Text(AppManager.instance.userModel!.data!.storeName!,
                        style: MyText.headline(context)!.copyWith(
                            color: HexColor("#000000"),
                            fontWeight: FontWeight.bold)),
                  ],
                ),
                Spacer(),
                //Uri.parse(_adVertData.webLink).isAbsolute
                CircleImage(
                  imageProvider: Image.network(Uri.parse(
                                  AppManager.instance.userModel!.data!.logo!)
                              .isAbsolute
                          ? AppManager.instance.userModel!.data!.logo!
                          : '${Constants.BASE_URL}/${AppManager.instance.userModel!.data!.logo!}')
                      .image,
                  size: 60,
                ),
              ],
            ),
            padding: EdgeInsets.symmetric(horizontal: 15, vertical: 25),
          ),
          Container(
            child: Text("PROFILE",
                style:
                    MyText.body1(context)!.copyWith(color: Colors.grey[500])),
            margin: EdgeInsets.fromLTRB(15, 18, 15, 0),
          ),
          Card(
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(0)),
            elevation: 2,
            margin: EdgeInsets.fromLTRB(0, 10, 0, 5),
            child: Container(
              width: double.infinity,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Container(height: 10),
                  InkWell(
                    onTap: () {
                      showSheet(context);
                    },
                    child: Container(
                      padding:
                          EdgeInsets.symmetric(horizontal: 15, vertical: 15),
                      child: Row(
                        children: <Widget>[
                          Icon(Icons.password,
                              size: 25.0, color: Colors.grey[500]),
                          Container(width: 10),
                          Text("Change Password",
                              style: MyText.subhead(context)!.copyWith(
                                  color: Colors.grey[600],
                                  fontWeight: FontWeight.w500)),
                          Spacer(),
                          Icon(Icons.chevron_right,
                              size: 25.0, color: Colors.grey[500]),
                        ],
                      ),
                    ),
                  ),
                  InkWell(
                    onTap: () {
                      _showDialog(
                          false, context, "Log Out", "Are You Sure?", "Logout",
                          () {
                        AppManager.instance.setLogOut();
                        Get.toNamed(Routes.LOGIN);
                      }, "Cancel", () {});
                    },
                    child: Container(
                      padding:
                          EdgeInsets.symmetric(horizontal: 15, vertical: 15),
                      child: Row(
                        children: <Widget>[
                          Icon(Icons.email, size: 25.0, color: Colors.red[400]),
                          Container(width: 10),
                          Text("Logout",
                              style: MyText.subhead(context)!.copyWith(
                                  color: Colors.grey[600],
                                  fontWeight: FontWeight.w500)),
                          Spacer(),
                          Icon(Icons.logout, size: 25.0, color: Colors.red),
                        ],
                      ),
                    ),
                  ),
                  /*InkWell(
                    onTap: () {},
                    child: Container(
                      padding:
                          EdgeInsets.symmetric(horizontal: 15, vertical: 15),
                      child: Row(
                        children: <Widget>[
                          Icon(Icons.email, size: 25.0, color: Colors.red[400]),
                          Container(width: 10),
                          Text("Change Email",
                              style: MyText.subhead(context)!.copyWith(
                                  color: Colors.grey[600],
                                  fontWeight: FontWeight.w500)),
                          Spacer(),
                          Icon(Icons.chevron_right,
                              size: 25.0, color: Colors.grey[500]),
                        ],
                      ),
                    ),
                  ),*/
                  Container(height: 10),
                ],
              ),
            ),
          ),

          /*
          Container(
            child: Text("NOTIFICATION",
                style:
                    MyText.body1(context)!.copyWith(color: Colors.grey[500])),
            margin: EdgeInsets.fromLTRB(15, 18, 15, 0),
          ),
          Card(
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(0)),
            elevation: 2,
            margin: EdgeInsets.fromLTRB(0, 10, 0, 5),
            child: Container(
              width: double.infinity,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Container(height: 10),
                  InkWell(
                    onTap: () {},
                    child: Container(
                      padding:
                          EdgeInsets.symmetric(horizontal: 15, vertical: 15),
                      child: Row(
                        children: <Widget>[
                          Icon(Icons.email,
                              size: 25.0, color: Colors.grey[500]),
                          Container(width: 10),
                          Text("Email Notification",
                              style: MyText.subhead(context)!.copyWith(
                                  color: Colors.grey[600],
                                  fontWeight: FontWeight.w500)),
                          Spacer(),
                          Switch(
                            value: true,
                            onChanged: (value) {},
                            activeColor: HexColor("#F23E7F"),
                            inactiveThumbColor: Colors.grey,
                          ),
                        ],
                      ),
                    ),
                  ),
                  InkWell(
                    onTap: () {},
                    child: Container(
                      padding:
                          EdgeInsets.symmetric(horizontal: 15, vertical: 2),
                      child: Row(
                        children: <Widget>[
                          Icon(Icons.mobile_friendly,
                              size: 25.0, color: Colors.yellow[800]),
                          Container(width: 10),
                          Text("Mobile Notification",
                              style: MyText.subhead(context)!.copyWith(
                                  color: Colors.grey[600],
                                  fontWeight: FontWeight.w500)),
                          Spacer(),
                          Switch(
                            value: false,
                            onChanged: (value) {},
                            activeColor: HexColor("#F23E7F"),
                            inactiveThumbColor: Colors.grey,
                          ),
                        ],
                      ),
                    ),
                  ),
                  Container(height: 10),
                ],
              ),
            ),
          )
          */
        ],
      ),
    ));
  }

  void showSheet(context) {
    TextStyle(color: Colors.white, height: 1.4, fontSize: 16);
    showModalBottomSheet(
        context: context,
        builder: (BuildContext bc) {
          return Scaffold(
            //resizeToAvoidBottomInset: false,
            body: SingleChildScrollView(
              child: Container(
                padding: EdgeInsets.all(20.sp),
                color: Colors.white,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: <Widget>[
                    GetBuilder<HomeController>(
                      builder: (_c) => TextField(
                          keyboardType: TextInputType.emailAddress,
                          decoration:
                              InputDecoration(labelText: "Old Password"),
                          onChanged: (text) {
                            _c.oldPassword.value = text;
                            _c.update();
                          }),
                    ),
                    GetBuilder<HomeController>(
                      builder: (_c) => TextField(
                          keyboardType: TextInputType.emailAddress,
                          decoration:
                              InputDecoration(labelText: "New Password"),
                          onChanged: (text) {
                            _c.newPassword.value = text;
                            _c.update();
                          }),
                    ),
                    Container(height: 20),
                    SizedBox(
                      width: double.infinity, // match_parent
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          primary: HexColor("#0F4EDC"),
                        ),
                        child: Text(
                          "Update",
                          style: TextStyle(
                              color: Colors.white, fontWeight: FontWeight.bold),
                        ),
                        onPressed: () {
                          ProgressHud.showLoading();
                          controller.updatePassword((message) {
                            ProgressHud.dismiss();

                            ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                              content: Text(
                                  "Password Have Been Updated. Please re-login."),
                            ));

                            if (message == "success") {
                              AppManager.instance.setLogOut();
                              Get.toNamed(Routes.LOGIN);
                            }
                          });
                        },
                      ),
                    ),
                    Container(height: 10)
                  ],
                ),
              ),
            ),
          );
        });
  }

  BoxDecoration myBoxDecoration() {
    return BoxDecoration(
      border: Border.all(
          width: 1, //
          color: Colors.grey[400]! //                  <--- border width here
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
}
