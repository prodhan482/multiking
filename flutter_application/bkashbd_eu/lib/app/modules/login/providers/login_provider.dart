import 'dart:convert';

import 'package:bkashbd_eu/app/base/base_get_connect.dart';
import 'package:bkashbd_eu/models/user_model.dart';
import 'package:bkashbd_eu/utils/app_manager.dart';
import 'package:bkashbd_eu/utils/constants.dart';
import 'package:get/get.dart';

class LoginProvider extends BaseGetConnect {
  @override
  void onInit() {
    super.onInit();
  }

  Future<DoLoginResponse> doLogin(Map data, AppManager instance) async {
    onInit();
    var header = Map<String, String>();
    header['mobile-app'] = "yes";
    final response = await post("login", data, headers: header);
    print(response.bodyString);
    var i = DoLoginResponse.fromJson(jsonDecode(response.bodyString!));
    i.code = response.statusCode;
    if (i.code == HttpCodes.HTTP_OK) {
      AppManager.instance.setAuthorizationToken(i.token!);
      print("Hi 1");
      await getUserDetails();
      print("Hi 2");
      return i;
    } else {
      return Future.error(response.statusCode!.toString());
    }
  }

  Future<bool> getUserDetails() async {
    var header = Map<String, String>();
    header['Authorization'] =
        "Bearer ${AppManager.instance.getAuthorizationToken()}";
    header['mobile-app'] = "yes";
    try {
      final response = await get("profile", headers: header);
      print(AppManager.instance.getAuthorizationToken());
      print(response.bodyString);
      if (response.statusCode == HttpCodes.HTTP_OK) {
        var kk = UserModel.fromJson(jsonDecode(response.bodyString!));
        AppManager.instance.setUserData(kk);
      } else {
        return Future.error(response.statusCode!.toString());
      }
    } catch (e) {
      e.printInfo();
      return Future.error('Error');
    }
    return true;
  }
}

class DoLoginResponse {
  String? rightNow;
  int? timestamp;
  bool? success;
  String? token;
  int? code;

  DoLoginResponse(
      {this.rightNow, this.timestamp, this.success, this.token, this.code});

  DoLoginResponse.fromJson(Map<String, dynamic> json) {
    rightNow = json['right_now'];
    timestamp = json['timestamp'];
    success = json['success'];
    token = json['token'];
    code = json['code'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['right_now'] = this.rightNow;
    data['timestamp'] = this.timestamp;
    data['success'] = this.success;
    data['token'] = this.token;
    data['code'] = this.code;
    return data;
  }
}
