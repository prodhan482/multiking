import 'dart:convert';

import 'package:bkashbd_eu/app/base/base_get_connect.dart';
import 'package:bkashbd_eu/app/modules/home/model/recharge_list_response.dart';
import 'package:bkashbd_eu/models/user_model.dart';
import 'package:bkashbd_eu/utils/app_manager.dart';
import 'package:bkashbd_eu/utils/constants.dart';
import 'package:get/get.dart';

class HomeProvider extends BaseGetConnect {
  @override
  void onInit() {
    super.onInit();
  }

  Future<bool> getUserDetails() async {
    onInit();

    var header = Map<String, String>();
    header['Authorization'] =
        "Bearer ${AppManager.instance.getAuthorizationToken()}";
    header['mobile-app'] = "yes";
    try {
      final response = await get("profile", headers: header);
      print(response.bodyString);
      print(AppManager.instance.getAuthorizationToken());
      if (response.statusCode == HttpCodes.HTTP_OK) {
        AppManager.instance
            .setUserData(UserModel.fromJson(jsonDecode(response.bodyString!)));
      } else {
        return Future.error(response.statusCode!.toString());
      }
    } catch (e) {
      e.printInfo();
      return Future.error('Error');
    }
    return true;
  }

  Future<DashboardResponse> getDashboard() async {
    onInit();

    var header = Map<String, String>();
    header['Authorization'] =
        "Bearer ${AppManager.instance.getAuthorizationToken()}";
    header['mobile-app'] = "yes";
    print(AppManager.instance.getAuthorizationToken());

    final response = await get("dashboard", headers: header);
    print(response.bodyString);
    if (response.statusCode == HttpCodes.HTTP_OK) {
      return DashboardResponse.fromJson(jsonDecode(response.bodyString!));
    } else {
      return Future.error(response.statusCode!.toString());
    }
  }

  Future<CommonResponse> doPasswordUpdate(String new_password) async {
    onInit();

    var header = Map<String, String>();
    header['Authorization'] =
        "Bearer ${AppManager.instance.getAuthorizationToken()}";
    header['mobile-app'] = "yes";
    print(AppManager.instance.getAuthorizationToken());

    final response = await patch("me/update", {"new_password": new_password},
        headers: header);
    print(response.bodyString);
    if (response.statusCode == HttpCodes.HTTP_OK) {
      return CommonResponse.fromJson(jsonDecode(response.bodyString!));
    } else {
      return Future.error(response.statusCode!.toString());
    }
  }

  Future<RechargeListResponse> getRechargeInfo(String rowLimit) async {
    onInit();
    try {
      var header = Map<String, String>();
      header['Authorization'] =
          "Bearer ${AppManager.instance.getAuthorizationToken()}";
      header['mobile-app'] = "yes";

      var postData = Map<String, String>();
      if (rowLimit.isNotEmpty) {
        postData["limit"] = rowLimit;
      }

      final response =
          await post("recharge/activity", postData, headers: header);
      print(AppManager.instance.getAuthorizationToken());
      print(response.bodyString!);

      if (response.statusCode == HttpCodes.HTTP_OK) {
        print("-----encodingStart getRechargeInfo");
        var mm =
            RechargeListResponse.fromJson(jsonDecode(response.bodyString!));
        print("-----encodingEnd getRechargeInfo");
        return mm;
      } else {
        return Future.error(response.statusCode!.toString());
      }
    } catch (e) {
      e.printInfo();
      return Future.error('Error');
    }
  }

  Future<CommonResponse> approveRejectRecharge(
      String recharge_id, String recharge_status, String note) async {
    onInit();
    try {
      var header = Map<String, String>();
      header['Authorization'] =
          "Bearer ${AppManager.instance.getAuthorizationToken()}";
      header['mobile-app'] = "yes";

      var postData = Map<String, String>();
      postData['recharge_status'] = recharge_status;
      postData['note'] = note;

      final response = await post(
          "recharge/approve_reject/${recharge_id}", postData,
          headers: header);
      print(AppManager.instance.getAuthorizationToken());
      print(response.bodyString!);
      print("-----statusCode ${response.statusCode}");

      if (response.statusCode == HttpCodes.HTTP_OK) {
        print("-----encodingStart ");
        var mm = CommonResponse.fromJson(jsonDecode(response.bodyString!));
        print("-----encodingEnd ");
        return mm;
      } else {
        return Future.error(response.statusCode!.toString());
      }
    } catch (e) {
      e.printInfo();
      return Future.error('Error');
    }
  }

  Future<CommonResponse> lockUnlockRecharge(
      String recharge_id, bool lock) async {
    onInit();
    try {
      var header = Map<String, String>();
      header['Authorization'] =
          "Bearer ${AppManager.instance.getAuthorizationToken()}";
      header['mobile-app'] = "yes";

      var postData = Map<String, String>();

      final response = await post(
          "recharge/${lock ? 'lock' : 'unlock'}/${recharge_id}", postData,
          headers: header);
      print(AppManager.instance.getAuthorizationToken());
      print(response.bodyString!);
      print("-----statusCode lockUnlockRecharge ${response.statusCode}");

      if (response.statusCode == HttpCodes.HTTP_OK) {
        print("-----encodingStart lockUnlockRecharge");
        var mm = CommonResponse.fromJson(jsonDecode(response.bodyString!));
        print("-----encodingEnd lockUnlockRecharge");
        return mm;
      } else {
        return Future.error(response.statusCode!.toString());
      }
    } catch (e) {
      e.printInfo();
      return Future.error('Error');
    }
  }

  Future<CommonResponse> reInitRejectedRecharge(String recharge_id) async {
    onInit();
    try {
      var header = Map<String, String>();
      header['Authorization'] =
          "Bearer ${AppManager.instance.getAuthorizationToken()}";
      header['mobile-app'] = "yes";

      var postData = Map<String, String>();

      final response = await post("recharge/reinit/${recharge_id}", postData,
          headers: header);
      print(AppManager.instance.getAuthorizationToken());
      print(response.bodyString!);
      print("-----statusCode reInitRejectedRecharge ${response.statusCode}");

      if (response.statusCode == HttpCodes.HTTP_OK) {
        print("-----encodingStart reInitRejectedRecharge");
        var mm = CommonResponse.fromJson(jsonDecode(response.bodyString!));
        print("-----encodingEnd reInitRejectedRecharge");
        return mm;
      } else {
        return Future.error(response.statusCode!.toString());
      }
    } catch (e) {
      e.printInfo();
      return Future.error('Error');
    }
  }

  Future<CommonResponse> updateRechargeNote(
      String recharge_id, String note) async {
    onInit();
    try {
      var header = Map<String, String>();
      header['Authorization'] =
          "Bearer ${AppManager.instance.getAuthorizationToken()}";
      header['mobile-app'] = "yes";

      var postData = Map<String, String>();
      postData['note'] = note;

      final response = await post(
          "recharge/update_note/${recharge_id}", postData,
          headers: header);
      print(AppManager.instance.getAuthorizationToken());
      print(response.bodyString!);
      print("-----statusCode ${response.statusCode}");

      if (response.statusCode == HttpCodes.HTTP_OK) {
        print("-----encodingStart updateRechargeNote");
        var mm = CommonResponse.fromJson(jsonDecode(response.bodyString!));
        print("-----encodingEnd updateRechargeNote");
        return mm;
      } else {
        return Future.error(response.statusCode!.toString());
      }
    } catch (e) {
      e.printInfo();
      return Future.error('Error');
    }
  }
}

class CommonResponse {
  CommonResponse({
    this.rightNow,
    this.timestamp,
    this.success,
  });

  String? rightNow;
  int? timestamp;
  bool? success;

  factory CommonResponse.fromJson(Map<String, dynamic> json) => CommonResponse(
        rightNow: json["right_now"],
        timestamp: json["timestamp"],
        success: json["success"],
      );

  Map<String, dynamic> toJson() => {
        "right_now": rightNow,
        "timestamp": timestamp,
        "success": success,
      };
}

class DashboardResponse {
  DashboardResponse({
    this.rightNow,
    this.timestamp,
    this.success,
    this.dashboardInfo,
  });

  String? rightNow;
  int? timestamp;
  bool? success;
  DashboardInfo? dashboardInfo;

  factory DashboardResponse.fromJson(Map<String, dynamic> json) =>
      DashboardResponse(
        rightNow: json["right_now"],
        timestamp: json["timestamp"],
        success: json["success"],
        dashboardInfo: DashboardInfo.fromJson(json["dashboard_info"]),
      );

  Map<String, dynamic> toJson() => {
        "right_now": rightNow,
        "timestamp": timestamp,
        "success": success,
        "dashboard_info": dashboardInfo!.toJson(),
      };
}

class DashboardInfo {
  DashboardInfo({
    this.notice,
    this.highlightedBlocks,
    this.table,
  });

  String? notice;
  List<HighlightedBlock>? highlightedBlocks;
  Table? table;

  factory DashboardInfo.fromJson(Map<String, dynamic> json) => DashboardInfo(
        notice: json["notice"],
        highlightedBlocks: List<HighlightedBlock>.from(
            json["highlighted_blocks"]
                .map((x) => HighlightedBlock.fromJson(x))),
        table: Table.fromJson(json["table"]),
      );

  Map<String, dynamic> toJson() => {
        "notice": notice,
        "highlighted_blocks":
            List<dynamic>.from(highlightedBlocks!.map((x) => x.toJson())),
        "table": table!.toJson(),
      };
}

class HighlightedBlock {
  HighlightedBlock({
    this.iconClass,
    this.title,
    this.value,
  });

  String? iconClass;
  String? title;
  String? value;

  factory HighlightedBlock.fromJson(Map<String, dynamic> json) =>
      HighlightedBlock(
        iconClass: json["icon_class"],
        title: json["title"],
        value: json["value"],
      );

  Map<String, dynamic> toJson() => {
        "icon_class": iconClass,
        "title": title,
        "value": value,
      };
}

class Table {
  Table({
    this.title,
    this.rows,
  });

  String? title;
  List<List<String>>? rows;

  factory Table.fromJson(Map<String, dynamic> json) => Table(
        title: json["title"],
        rows: List<List<String>>.from(
            json["rows"].map((x) => List<String>.from(x.map((x) => x)))),
      );

  Map<String, dynamic> toJson() => {
        "title": title,
        "rows": List<dynamic>.from(
            rows!.map((x) => List<dynamic>.from(x.map((x) => x)))),
      };
}
