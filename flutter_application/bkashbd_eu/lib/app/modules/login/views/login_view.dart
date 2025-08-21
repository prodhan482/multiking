import 'package:bkashbd_eu/app/modules/login/providers/login_provider.dart';
import 'package:bkashbd_eu/app/routes/app_pages.dart';
import 'package:bkashbd_eu/libraries/bmprogresshud/bmprogresshud.dart';
import 'package:bkashbd_eu/utils/app_manager.dart';
import 'package:bkashbd_eu/utils/constants.dart';
import 'package:bkashbd_eu/utils/hex_color.dart';
import 'package:bkashbd_eu/utils/img.dart';
import 'package:bkashbd_eu/utils/my_text.dart';
import 'package:bkashbd_eu/utils/primitive_extensions.dart';
import 'package:flutter/material.dart';

import 'package:get/get.dart';

import '../controllers/login_controller.dart';

class LoginView extends GetView<LoginController> {
  @override
  Widget build(BuildContext context) {
    //LoginController c = Get.put(LoginController());

    LoginProvider _loginProvider = LoginProvider();

    return Scaffold(
      //resizeToAvoidBottomInset: false,
      backgroundColor: Colors.white,
      appBar:
          PreferredSize(child: Container(), preferredSize: Size.fromHeight(0)),
      body: Container(
        padding: EdgeInsets.symmetric(vertical: 30, horizontal: 30),
        width: double.infinity,
        height: double.infinity,
        child: Stack(
          children: [
            Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Container(height: 30),
                  Container(
                    child: Image.asset(Img.get('white_bg_logo.png')),
                    height: 80,
                  ),
                  Container(height: 15),
                  Text("Sign in to continue",
                      style: MyText.subhead(context)!.copyWith(
                          color: Colors.blueGrey[300],
                          fontWeight: FontWeight.bold)),
                ]),
            Positioned(
              bottom: 0,
              child: Container(
                  decoration: BoxDecoration(
                      color: Colors.white,
                      border: Border.all(color: Colors.white),
                      borderRadius: BorderRadius.only(
                          bottomLeft: Radius.circular(10.0),
                          bottomRight: Radius.circular(10.0))),
                  width: (MediaQuery.of(context).size.width - 65),
                  child: Padding(
                      padding: EdgeInsets.all(1.0),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: <Widget>[
                          GetBuilder<LoginController>(
                            builder: (_c) => TextField(
                                keyboardType: TextInputType.text,
                                decoration:
                                    InputDecoration(labelText: "User Name"),
                                onChanged: (text) {
                                  _c.setUserNameTextFieldVal(text);
                                  _c.update();
                                }),
                          ),
                          Container(height: 25),
                          GetX<LoginController>(
                              init: LoginController(),
                              builder: (_c) => TextField(
                                    obscureText:
                                        _c.getobscurePasswordTextFieldVal(),
                                    onChanged: (text) {
                                      _c.setUserPasswordTextFieldVal(text);
                                    },
                                    decoration: InputDecoration(
                                        labelText: 'Password',
                                        suffixIcon: IconButton(
                                            icon: Icon(
                                                _c.getobscurePasswordTextFieldVal()
                                                    ? Icons.visibility
                                                    : Icons.visibility_off),
                                            onPressed: () {
                                              _c.setobscurePasswordTextFieldVal(
                                                  !_c.getobscurePasswordTextFieldVal());
                                            })),
                                  )),

                          /*
            GetBuilder<LoginController>(
              builder: (_c) => TextField(
                obscureText: _c.getobscurePasswordTextFieldVal(),
                decoration: InputDecoration(
                    labelText: 'Password',
                    suffixIcon: IconButton(
                        icon: Icon(_c.getobscurePasswordTextFieldVal()
                            ? Icons.visibility
                            : Icons.visibility_off),
                        onPressed: () {
                          print("ola");
                          _c.setobscurePasswordTextFieldVal(
                              !_c.getobscurePasswordTextFieldVal());
                          _c.update();
                        })),
              ),
            ),
            */
                          /*
            Obx(() => TextField(
                  obscureText: controller.getobscurePasswordTextFieldVal(),
                  decoration: InputDecoration(
                      labelText: 'Password',
                      suffixIcon: IconButton(
                          icon: Icon(controller.getobscurePasswordTextFieldVal()
                              ? Icons.visibility
                              : Icons.visibility_off),
                          onPressed: () {
                            print("ola");
                            c.setobscurePasswordTextFieldVal(
                                !c.getobscurePasswordTextFieldVal());
                          })),
                )),
                */
                          Container(height: 5),
                          Container(
                            width: double.infinity,
                            //width: (MediaQuery.of(context).size.width - 65),
                            child: GetBuilder<LoginController>(
                                builder: (_c) => ElevatedButton(
                                      style: ElevatedButton.styleFrom(
                                          primary: HexColor("#0F4EDC"),
                                          elevation: 0),
                                      child: Text("Login",
                                          style: TextStyle(
                                              color: Colors.white,
                                              fontWeight: FontWeight.bold,
                                              fontSize: 16.sp)),
                                      onPressed: () {
                                        //_loginProvider.onInit();
                                        var validationMessage = _c.validation();
                                        _c.update();
                                        if (validationMessage.isEmpty) {
                                          ProgressHud.showLoading();
                                          _loginProvider
                                              .doLogin(_c.loginRequest,
                                                  AppManager.instance)
                                              .then((response) {
                                            ProgressHud.dismiss();
                                            Get.toNamed(Routes.HOME);
                                          }, onError: (err_code) {
                                            ProgressHud.dismiss();
                                            //change(null,status: RxStatus.error(err.toString()));
                                            if (err_code == "401") {
                                              _showDialog(
                                                  context,
                                                  "Unauthorized",
                                                  "Your have provide unauthorized credentials.",
                                                  "Ok",
                                                  () {});
                                            }
                                          });
                                        } else {
                                          _showDialog(context, "Alart",
                                              validationMessage, "Ok", () {});
                                        }
                                      },
                                    )),
                          ),
                          Container(height: 50),
                        ],
                        mainAxisSize: MainAxisSize.min,
                      ))),
            ),
          ],
        ),
      ),
    );
  }

  Future<void> _showDialog(BuildContext context, String title, String message,
      String bttTxt, Function callback) async {
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
              child: Text(bttTxt,
                  style: TextStyle(
                      color: Colors.black,
                      fontWeight: FontWeight.bold,
                      fontSize: 16.sp)),
              onPressed: () {
                Navigator.of(context).pop();
                callback();
              },
            ),
          ],
        );
      },
    );
  }
}
