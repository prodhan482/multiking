import 'dart:math';

import 'package:bkashbd_eu/utils/hex_color.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';

class AppConstants {
  static const transparent = Colors.transparent;
  static const black = Color.fromRGBO(0, 0, 0, 1.0);
  static const primaryTextColorBlack = Color.fromRGBO(51, 51, 51, 1.0);
  static const primaryTextColorGrey = Color.fromRGBO(51, 51, 51, 0.5);
  static const white = Color.fromRGBO(255, 255, 255, 1.0);
  static const red = Color.fromRGBO(255, 0, 0, 1.0);
  static const primaryTextColorRed = Color.fromRGBO(238, 71, 71, 1.0);
  static const primaryAppColor = Color.fromRGBO(1, 160, 233, 1.0);
  static const primaryAppDisableColor = Color.fromRGBO(178, 226, 248, 1.0);
  static Color bgDisabledColor = HexColor("#95A3B8");
  static Color bgGradientTopColor = HexColor("#E0EEFF");
  static Color bgGradientBottomColor = HexColor("#E9F5F0");
  static Color btnShadowColor = Color.fromRGBO(98, 142, 132, 0.2);
  static Color btnShadowColor25 = Color.fromRGBO(98, 142, 132, 0.25);
  static Color switchTreeColor = HexColor("#09ABD1");
  static Color switchDiaryColor = HexColor("#0AC699");
  static Color switchInActiveColor = HexColor("#DDDDDD");
  static Color switchShareActiveColor = HexColor("#FF816A");
  static Color tabbarTextColor = HexColor("#696969");
  static Color textColorGrey33 = HexColor("#333333");
  static Color textColorGrey45 = HexColor("#454545");
  static Color textColorGrey64 = HexColor("#646464");
  static Color textColorGrey86 = HexColor("#868686");
  static Color textColorGreyA2 = HexColor("#A5A4A2");
  static Color textColorGreyB2 = HexColor("#B2B2B2");
  static Color borderColorGrey = HexColor("#A8B4A3");
  static Color shadowColorGrey = HexColor("#628E84");
  static Color viewColorGrey = HexColor("#EDF0EF");
  static Color viewColorPurple = HexColor("#E0E1FF");
  static Color viewColorGreen = HexColor("#D1FFF4");
  static Color viewColorGreenB0 = HexColor("#68C7B0");
  static Color bordercolorBlue = HexColor("#B9C6FD");
  static Color bordercolorBlue2 = HexColor("#B9C6FF");
  static Color popupBGColor = HexColor("#1F2F55");
  static Color redishColor = HexColor("#FFDCD6");

  static const screenPath = "assets/screen/";
  static const imagePath = "assets/images/";
  static const imagePathOnBoarding = imagePath + "onboarding/";
  static const imagePathBMProgresshud = "assets/images/bmprogresshud/";
  static const svgPath = "assets/svgs/";

  //https://bd50.ocdev.me/fox
  //https://bd50.ocdev.me/beats/
  // static const web3dMyUrl = "https://google.com";
  // static const web3dTeamUrl = "https://facebook.com";
  static const web3dMyUrl = "https://bd50.ocdev.me/beats/";
  static const web3dTeamUrl = "https://bd50.ocdev.me/fox";

  static const USER_TOKEN = 'logged_user_token';
  static const USER_NAME = 'logged_user_name';
  static const USER_EMAIL = 'logged_user_email';
  static const USER_LANGUAGE = 'logged_user_language';
}

class HttpCodes {
  static const HTTP_CONTINUE = 100;
  static const HTTP_SWITCHING_PROTOCOLS = 101;
  static const HTTP_PROCESSING = 102;            // RFC2518
  static const HTTP_OK = 200;
  static const HTTP_CREATED = 201;
  static const HTTP_ACCEPTED = 202;
  static const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
  static const HTTP_NO_CONTENT = 204;
  static const HTTP_RESET_CONTENT = 205;
  static const HTTP_PARTIAL_CONTENT = 206;
  static const HTTP_MULTI_STATUS = 207;          // RFC4918
  static const HTTP_ALREADY_REPORTED = 208;      // RFC5842
  static const HTTP_IM_USED = 226;               // RFC3229
  static const HTTP_MULTIPLE_CHOICES = 300;
  static const HTTP_MOVED_PERMANENTLY = 301;
  static const HTTP_FOUND = 302;
  static const HTTP_SEE_OTHER = 303;
  static const HTTP_NOT_MODIFIED = 304;
  static const HTTP_USE_PROXY = 305;
  static const HTTP_RESERVED = 306;
  static const HTTP_TEMPORARY_REDIRECT = 307;
  static const HTTP_PERMANENTLY_REDIRECT = 308;  // RFC7238
  static const HTTP_BAD_REQUEST = 400;
  static const HTTP_UNAUTHORIZED = 401;
  static const HTTP_PAYMENT_REQUIRED = 402;
  static const HTTP_FORBIDDEN = 403;
  static const HTTP_NOT_FOUND = 404;
  static const HTTP_METHOD_NOT_ALLOWED = 405;
  static const HTTP_NOT_ACCEPTABLE = 406;
  static const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
  static const HTTP_REQUEST_TIMEOUT = 408;
  static const HTTP_CONFLICT = 409;
  static const HTTP_GONE = 410;
  static const HTTP_LENGTH_REQUIRED = 411;
  static const HTTP_PRECONDITION_FAILED = 412;
  static const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
  static const HTTP_REQUEST_URI_TOO_LONG = 414;
  static const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
  static const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
  static const HTTP_EXPECTATION_FAILED = 417;
  static const HTTP_I_AM_A_TEAPOT = 418;                                               // RFC2324
  static const HTTP_MISDIRECTED_REQUEST = 421;                                         // RFC7540
  static const HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
  static const HTTP_LOCKED = 423;                                                      // RFC4918
  static const HTTP_FAILED_DEPENDENCY = 424;                                           // RFC4918
  static const HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;   // RFC2817
  static const HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
  static const HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
  static const HTTP_TOO_MANY_REQUESTS = 429;                                           // RFC6585
  static const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
  static const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
  static const HTTP_INTERNAL_SERVER_ERROR = 500;
  static const HTTP_NOT_IMPLEMENTED = 501;
  static const HTTP_BAD_GATEWAY = 502;
  static const HTTP_SERVICE_UNAVAILABLE = 503;
  static const HTTP_GATEWAY_TIMEOUT = 504;
  static const HTTP_VERSION_NOT_SUPPORTED = 505;
  static const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
  static const HTTP_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
  static const HTTP_LOOP_DETECTED = 508;                                               // RFC5842
  static const HTTP_NOT_EXTENDED = 510;                                                // RFC2774
  static const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585
}

class AppFonts {
  static const brandonGrotesque = 'Brandon Grotesque';
  static const dnpShueiMGoStd =
      'Noto Sans JP'; // In the mid time of development this font is changed to Noto Sans JP because dnp shueiMGoStd font is paid
}

class APIConstants {
  static const image_upload_url =
      "https://j692tg34jk.execute-api.ap-northeast-1.amazonaws.com/dev/api/v1/image";

  static const api_base_url = "https://bd50.ocdev.me/api/";
  static const api_login = 'user/login/';
  static const api_forget_password = 'user/forget-password/';
  static const api_password_reissue = 'user/password-reissue/';
  static const api_departments_list = 'user/departments/list/';
  static const api_signup = 'user/users/userSignup/';
  static const api_company_details = 'user/companies/detail/';
  static const api_invitation_code = 'user/inviteUsers/detail/';
  static const api_diary_list = "user/dashboard_diaries/list/";
  static const api_feel_list = "user/dashboard_feels/list/";
  static const api_awarness_list = "user/dashboard_awarness/list/";
  static const api_angel_list = "user/dashboard_angels/list/";
  static const api_music_list = "user/dashboard_musics/list";
  static const api_diary_create = "user/dashboard_diaries/create/";
  static const api_diary_update = "user/dashboard_diaries/update/";
  static const api_notice_list = "user/dashboard_notices/list/";
  static const api_user_update = "user/users/addUpdate/";

  static const api_user_list = "user/users/list/";
  static const api_invite_users = "user/inviteUsers/addUpdate/";

  static const client_secret = "yuwEcjRvQ7OOLUztdfJtWRkBP7ENxvgrxeLFGrMP";
  static const bearerKey = "Authorization";
  static const LoginAPIKeys loginAPIKeys = const LoginAPIKeys();
  static const refresh_token = "refresh_token";
  static const LoginAPIKeyValues loginAPIKeyValues = const LoginAPIKeyValues();
}

class LoginAPIKeys {
  const LoginAPIKeys();
  final String grantType = "grant_type";
  final String clientType = "client_type";
  final String clientId = "client_id";
  final String clientSecret = "client_secret";
  final String pushToken = "push_token";
  final String email = "email";
  final String password = "password";
}

class LoginAPIKeyValues {
  const LoginAPIKeyValues();
  final String grantType = "password";
  final String clientType = "Android";
  final String clientId = "2";
  final String clientSecret = "yuwEcjRvQ7OOLUztdfJtWRkBP7ENxvgrxeLFGrMP";
  final String pushToken = "dummy";
}

class UserDefaultKeys {
  static const kUserDefaultFirstTimeKey = "kFirstTimeKey";
  static const kUserDefaultLoginKey = "kLoginKey";
  static const kUserDefaultSecreteTokenKey = "kSecreteTokenKey";
  static const kUserDefaultCurrentUserIdKey = "kCurrentUserIdKey";
  static const kUserDefaultTokenExpireDateKey = "kCurrentTokenExpireDateKey";
  static const kMapItemPosition = "kMapItemPosition";
  static const kUserDefaultRefreshTokenKey = "kCurrentTokenRefreshKey";
  static const kUserDefaultLongAngelVisitedKey =
      "kUserDefaultLongAngelVisitedKey";
  static const kUserDefaultShortAngelVisitedKey =
      "kUserDefaultShortAngelVisitedKey";
  static const kUserDefaultLanguageCodeKey = "kUserDefaultLanguageCodeKey";
}

abstract class ServerConstants {
  static const BASE_URL = 'https://bd50.ocdev.me/api';

  static const API_PATH_LOGIN = 'user/login?username';
  static const API_PATH_USER_FORGOT_PASSWORD = 'user/forget-password';
  static const API_PATH_USER_RESET_PASSWORD = 'user/password-reissue';
  static const API_PATH_GET_DEPARTMENT_LIST_BY_COMPANY_ID =
      'user/departments/list';
  static const API_PATH_REGISTRATION = 'user/users/userSignup';
}

class ErrorConfig {
  // Form Error
  static final RegExp emailValidatorRegExp =
      RegExp(r"^[a-zA-Z0-9.]+@[a-zA-Z0-9]+\.[a-zA-Z]+");
  static const String kEmailNullError = "メールアドレスを入力してください";
  static const String kInvalidEmailError = "有効なメールアドレスを入力してください";
  static const String kPassNullError = "パスワードを入力してください";
  static const String kShortPassError = "パスワードが短すぎます";
  static const String kMatchPassError = "パスワードが一致しません";
}

class SizeConfig {
  static MediaQueryData? _mediaQueryData;
  static Size defaultSize = Size(375.0, 812.0);
  static double screenWidth = defaultSize.width;
  static double screenHeight = defaultSize.height;
  static EdgeInsets padding = EdgeInsets.all(0.0);
  static Orientation orientation = Orientation.portrait;
  static double pixelRatio = 1.0;
  static double scaleWidth = 1.0;
  static double scaleHeight = 1.0;
  static double scaleText = 1.0;

  void init(BuildContext context) {
    _mediaQueryData = MediaQuery.of(context);
    screenWidth = _mediaQueryData?.size.width ?? defaultSize.width;
    screenHeight = _mediaQueryData?.size.height ?? defaultSize.height;
    orientation = _mediaQueryData?.orientation ?? Orientation.portrait;
    padding = _mediaQueryData?.padding ?? EdgeInsets.all(0.0);
    pixelRatio = _mediaQueryData?.devicePixelRatio ?? 1.0;

    scaleWidth = screenWidth / defaultSize.width;
    scaleHeight = screenHeight / defaultSize.height;
    scaleText = min(scaleWidth, scaleHeight);
  }

  static double setWidth(num width) => width * scaleWidth;
  static double setHeight(num height) => height * scaleHeight;
  static double setScale(num value) => value * scaleText;
  static double radius(num r) => r * scaleText;
  static double setSp(num fontSize) => fontSize * scaleText;
}

void NHLog(String msg) {
  if (kDebugMode) {
    debugPrint(msg, wrapWidth: 2048);
  }
}
