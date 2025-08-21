import 'package:bkashbd_eu/app/modules/home/controllers/home_controller.dart';
import 'package:bkashbd_eu/app/modules/home/list_adapter/admin_recharge_list.dart';
import 'package:bkashbd_eu/app/modules/home/list_adapter/vendor_recharge_list.dart';
import 'package:bkashbd_eu/libraries/bmprogresshud/progresshud.dart';
import 'package:bkashbd_eu/utils/app_manager.dart';
import 'package:bkashbd_eu/utils/my_text.dart';
import 'package:flutter/material.dart';

import 'package:get/get.dart';

import 'store_recharge_view.dart';

var RECHARGE_ROW_LIMIT = "200";

class RechargeListView extends GetView<HomeController> {
  @override
  Widget build(BuildContext context) {
    if (AppManager.instance.userModel!.data!.userType == "store") {
      RECHARGE_ROW_LIMIT = "1";
    }

    controller.getRechargeInfo(RECHARGE_ROW_LIMIT, () {});

    return Scaffold(
      body: RefreshIndicator(
          onRefresh: () {
            return Future.delayed(
              Duration(seconds: 1),
              () {
                ProgressHud.showLoading();
                controller.getRechargeInfo(RECHARGE_ROW_LIMIT, () {
                  ProgressHud.dismiss();
                });
              },
            );
          },
          child: GetX<HomeController>(
            init: HomeController(),
            builder: (_controller) {
              if ((AppManager.instance.userModel!.data!.userType != "store" &&
                      !(_controller.rechargeList.value.length > 0 &&
                          _controller.rechargeList.value[0].length > 1)) ||
                  (AppManager.instance.userModel!.data!.userType == "store" &&
                      _controller.mfsList!.value.length == 1)) {
                return Loading(context);
              }

              switch (AppManager.instance.userModel!.data!.userType) {
                case "super_admin":
                  return AdminRechargeList(_controller.rechargeList.value,
                          _controller.mfsList!.value, doReload)
                      .getView();
                case "vendor":
                  return VendorRechargeList(_controller.rechargeList.value,
                          _controller.mfsList!.value, doReload)
                      .getView();
                case "store":
                  return StoreRechargeView(
                      mfsList: _controller.mfsList!.value,
                      mfsPackageList: _controller.mfsPackageList!.value);
                default:
                  return AdminRechargeList(_controller.rechargeList.value,
                          _controller.mfsList!.value, doReload)
                      .getView();
              }
            },
          )),
    );
  }

  void doReload() {
    ProgressHud.showLoading();
    controller.getRechargeInfo(RECHARGE_ROW_LIMIT, () {
      ProgressHud.dismiss();
    });
  }
}

Widget Loading(BuildContext context) {
  return Align(
    child: Container(
      width: 105,
      height: 100,
      alignment: Alignment.center,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: <Widget>[
          Text("Loading...",
              style: MyText.body1(context)!.copyWith(color: Colors.grey[800])),
          Container(height: 20),
          Container(
            height: 4,
            child: LinearProgressIndicator(
              backgroundColor: Colors.grey[300],
            ),
          ),
        ],
      ),
    ),
    alignment: Alignment.center,
  );
}
