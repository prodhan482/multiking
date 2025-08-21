import 'dart:convert';

import 'package:bkashbd_eu/app/base/base_get_connect.dart';

class LandingProvider extends BaseGetConnect {
  @override
  void onInit() {
    super.onInit();
  }

  Future<ApiResponse> getAppVersion() async {
    onInit();
    final response = await get("app_welcome");
    print(response.bodyString);
    return ApiResponse.fromJson(jsonDecode(response.bodyString!));
  }
}

class ApiResponse {
  String? rightNow;
  String? allowVersionUpTo;
  String? mandatoryUpdateTo;
  String? download_url;

  ApiResponse(
      {this.rightNow,
      this.allowVersionUpTo,
      this.mandatoryUpdateTo,
      this.download_url});

  ApiResponse.fromJson(Map<String, dynamic> json) {
    rightNow = json['right_now'];
    allowVersionUpTo = json['allow_version_up_to'];
    mandatoryUpdateTo = json['mandatory_update_to'];
    download_url = json['download_url'];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = new Map<String, dynamic>();
    data['right_now'] = this.rightNow;
    data['allow_version_up_to'] = this.allowVersionUpTo;
    data['mandatory_update_to'] = this.mandatoryUpdateTo;
    data['download_url'] = this.download_url;
    return data;
  }
}
