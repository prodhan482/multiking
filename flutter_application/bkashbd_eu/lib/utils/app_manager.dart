import 'dart:convert';

import 'package:bkashbd_eu/models/user_model.dart';
import 'package:bkashbd_eu/utils/constants.dart';
import 'package:flutter/material.dart';
import 'package:flutter/painting.dart' as painting;
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:get_storage/get_storage.dart';

class AppManager {
  AppManager._privateConstructor();
  static final AppManager _instance = AppManager._privateConstructor();
  static AppManager get instance => _instance;
  GetStorage? storage;

  String universalLinkToken = "";
  UserModel? userModel;

  init() async {
    await GetStorage.init();
    storage = GetStorage();
  }

  void setAuthorizationToken(String token) async {
    storage?.write(UserDefaultKeys.kUserDefaultSecreteTokenKey, token);
  }

  void setRefreshToken(String refreshToken) async {
    storage?.write(UserDefaultKeys.kUserDefaultRefreshTokenKey, refreshToken);
  }

  String? getAuthorizationToken() {
    return storage?.read<String>(UserDefaultKeys.kUserDefaultSecreteTokenKey);
  }

  void setCurrentUserId(String userId) async {
    storage?.write(UserDefaultKeys.kUserDefaultCurrentUserIdKey, userId);
  }

  String? getCurrentUserId() {
    return storage?.read<String>(UserDefaultKeys.kUserDefaultCurrentUserIdKey);
  }

  void setFirstTime({required bool value}) {
    storage?.write(UserDefaultKeys.kUserDefaultFirstTimeKey, value);
  }

  bool isFirstTime() {
    return storage?.read<bool>(UserDefaultKeys.kUserDefaultFirstTimeKey) ??
        true;
  }

  String getToday(String _dateFormatStr) {
    DateFormat _dateFormat = DateFormat(_dateFormatStr);
    DateTime _now = DateTime.now();
    String _today = _dateFormat.format(_now);
    return _today;
  }

  void setLongAngelVisitedDate() {
    String _today = getToday("yyyy-MM-dd");
    storage?.write(UserDefaultKeys.kUserDefaultLongAngelVisitedKey, _today);
  }

  String getLongAngelVisitedDate() {
    return storage
            ?.read<String>(UserDefaultKeys.kUserDefaultLongAngelVisitedKey) ??
        "1970-01-01";
  }

  void setShortAngelVisitedDate() {
    String _today = getToday("yyyy-MM-dd");
    storage?.write(UserDefaultKeys.kUserDefaultShortAngelVisitedKey, _today);
  }

  String getShortAngelVisitedDate() {
    return storage
            ?.read<String>(UserDefaultKeys.kUserDefaultShortAngelVisitedKey) ??
        "1970-01-01";
  }

  void setUserData(UserModel? _userModel) {
    AppManager.instance.setLogin(value: true);
    AppManager.instance.setCurrentUserId(_userModel?.data!.userId! ?? "");
    storage?.write("user_data", json.encode(_userModel?.toJson()));
    setUserDataFromSaved();
  }

  void setUserDataFromSaved() {
    userModel = UserModel.fromJson(json.decode(
        AppManager.instance.storage?.read<String>("user_data") ?? "{}"));
  }

  void setLogin({required bool value}) {
    storage?.write(UserDefaultKeys.kUserDefaultLoginKey, value);
  }

  bool isLoggedIn() {
    return storage?.read<bool>(UserDefaultKeys.kUserDefaultLoginKey) ?? false;
  }

  void setLogOut(/*Function(bool) callBack*/) {
    setLogin(value: false);
    // Get.updateLocale(AppManager.instance.getCurrentLocale());
    // preferences?.clear().then((value) {
    //   callBack(value);
    // }).catchError((error){
    //   callBack(false);
    // });
  }

  void showToast(BuildContext context, String text) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(text),
      duration: Duration(milliseconds: 700),
    ));
  }

  // Here it is!
  Size textSize(String text, TextStyle style) {
    final TextPainter textPainter = TextPainter(
        text: TextSpan(text: text, style: style),
        maxLines: 1,
        textDirection: painting.TextDirection.ltr)
      ..layout(minWidth: 0, maxWidth: double.infinity);
    return textPainter.size;
  }
}
