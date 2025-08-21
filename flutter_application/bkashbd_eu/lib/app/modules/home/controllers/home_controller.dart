import 'package:bkashbd_eu/app/modules/home/model/recharge_list_response.dart';
import 'package:bkashbd_eu/app/modules/home/providers/home_provider.dart';
import 'package:bkashbd_eu/app/routes/app_pages.dart';
import 'package:bkashbd_eu/utils/app_manager.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

class HomeController extends GetxController with SingleGetTickerProviderMixin {
  final count = 0.obs;

  RxInt bottomNavPosition = 0.obs;
  HomeProvider _api = HomeProvider();
  RxString balance = "Loading.....".obs;
  RxString dashboardNotice = "Preparing Screen".obs;
  List<HighlightedBlock>? dashboardHighlightedBlock;
  RxString tableTitle = "".obs;
  List<List<String>>? tableRows;
  RxList<String> permissions = [""].obs;

  RxString newPassword = "".obs;
  RxString oldPassword = "".obs;

  RxList<List<String?>> rechargeList = [
    [""]
  ].obs;
  RxList<MfsList>? mfsList = [MfsList()].obs;
  RxList<MfsPackageList>? mfsPackageList = [MfsPackageList()].obs;
  RxList<EuroServiceChargeList>? euroServiceChargeList =
      [EuroServiceChargeList()].obs;

  RxString rechargeApproveRejectNote = "".obs;

  RxString selectedMfsId = "".obs;
  RxString selectedPackageId = "".obs;
  RxString selectedPackageName = "".obs;
  RxString selectedTypeId = "".obs;
  RxString sendByEuroOrOtherCu = "".obs;
  RxString selectedChargeWithoutChargeId = "".obs;
  RxString enteredMobileNumber = "".obs;
  TextEditingController enteredSendAmount = TextEditingController();
  TextEditingController enteredReceivedAmount = TextEditingController();
  TextEditingController enteredConversionRate = TextEditingController();
  TextEditingController enteredServiceCharge = TextEditingController();

  RxBool sendingEuro = true.obs;

  String rechargeServiceCharge = "0";
  String rechargeCharge = "0";
  String rechargeServiceCommission = "0";
  String visualSendMoney = "0";

  void calculateReceiveMoney() {
    if (sendingEuro.value) {
      calculateServiceCharge();
      rechargeCharge = (double.parse(rechargeServiceCharge) > 0
          ? "${(double.parse(rechargeServiceCharge) / double.parse(enteredSendAmount.text))}"
          : "0");
      rechargeServiceCommission = "0";

      if (selectedChargeWithoutChargeId.value == "Without Charge") {
        visualSendMoney =
            "${double.parse(enteredSendAmount.text) * double.parse(enteredConversionRate.text)}";
      } else {
        visualSendMoney =
            "${((double.parse(enteredSendAmount.text) * double.parse(enteredConversionRate.text)) - (double.parse(rechargeServiceCharge) - (-1)))}";
      }
    } else {}
  }

  void calculateSendMoney() {}

  void calculateServiceCharge() {
    euroServiceChargeList!.forEach((serviceCharge) {
      if (enteredSendAmount.text.isNotEmpty &&
          (double.parse(serviceCharge!.from!) <
              double.parse(enteredSendAmount.text)) &&
          (double.parse(serviceCharge!.to!) >=
              double.parse(enteredSendAmount.text))) {
        rechargeServiceCharge = serviceCharge!.charge!;
        enteredServiceCharge.text = serviceCharge!.charge!;
      }
    });
  }

  int getBottomNavPosition() => bottomNavPosition.value;
  void setBottomNavPosition(int _position) {
    bottomNavPosition.value = _position;
    update();
  }

  void loadUserProfileData(Function done) {
    _api.getUserDetails().then((response) {
      permissions.value = AppManager.instance.userModel!.data!.permissionLists!;
      loadDashboardData(() => done());
      done();
    }, onError: (err_code) {
      if (err_code == "401") {
        AppManager.instance.setLogOut();
        Get.toNamed(Routes.LOGIN);
      }
      done();
    });
  }

  void loadDashboardData(Function done) {
    _api.getDashboard().then((response) {
      dashboardNotice.value = response.dashboardInfo!.notice!;
      dashboardHighlightedBlock = response.dashboardInfo!.highlightedBlocks;
      tableTitle.value = response.dashboardInfo!.table!.title!;
      tableRows = response.dashboardInfo!.table!.rows!;
      update();
      done();
    }, onError: (err_code) {
      if (err_code == "401") {
        AppManager.instance.setLogOut();
        Get.toNamed(Routes.LOGIN);
      }
      done();
    });
  }

  void updatePassword(Function done) {
    _api.doPasswordUpdate(newPassword.value).then((response) {
      done("success");
    }, onError: (err_code) {
      if (err_code == "401") {
        AppManager.instance.setLogOut();
        Get.toNamed(Routes.LOGIN);
      }
      done("error");
    });
  }

  void getRechargeInfo(String rowLimit, Function done) {
    _api.getRechargeInfo(rowLimit).then((response) {
      rechargeList.value = response.data!;
      print("------------> Assigned.");
      mfsList!.value = response.mfsList!;
      mfsPackageList!.value = response.mfsPackageList!;
      euroServiceChargeList!.value = response.euroServiceChargeList1!;
      enteredConversionRate.text = response.conversionRate!;
      print("------------> Done.");
      done();
    }, onError: (err_code) {
      if (err_code == "401") {
        AppManager.instance.setLogOut();
        Get.toNamed(Routes.LOGIN);
      }
      done();
    });
  }

  void approveRejectRecharge(
      String recharge_id, String recharge_status, String note, Function done) {
    _api.approveRejectRecharge(recharge_id, recharge_status, note).then(
        (response) {
      done();
    }, onError: (err_code) {
      if (err_code == "401") {
        AppManager.instance.setLogOut();
        Get.toNamed(Routes.LOGIN);
      }
      done();
    });
  }

  void lockUnlockRecharge(String recharge_id, bool lock, Function done) {
    _api.lockUnlockRecharge(recharge_id, lock).then((response) {
      done();
    }, onError: (err_code) {
      if (err_code == "401") {
        AppManager.instance.setLogOut();
        Get.toNamed(Routes.LOGIN);
      }
      done();
    });
  }

  void reInitRejectedRecharge(String recharge_id, Function done) {
    _api.reInitRejectedRecharge(recharge_id).then((response) {
      done();
    }, onError: (err_code) {
      if (err_code == "401") {
        AppManager.instance.setLogOut();
        Get.toNamed(Routes.LOGIN);
      }
      done();
    });
  }

  void updateRechargeNote(String recharge_id, String note, Function done) {
    _api.updateRechargeNote(recharge_id, note).then((response) {
      done();
    }, onError: (err_code) {
      if (err_code == "401") {
        AppManager.instance.setLogOut();
        Get.toNamed(Routes.LOGIN);
      }
      done();
    });
  }

  void reloadRechargeListingPage(Function done) {
    rechargeList = [
      [""]
    ].obs;
    mfsList = [MfsList()].obs;
    update();
    getRechargeInfo("200", () {
      update();
      done();
    });
  }

  void prepareScreenValue() {
    if (AppManager.instance.userModel!.data!.userType == "store") {
      // Reseller
      balance.value =
          "Balance ${AppManager.instance.userModel!.currentBalance!.currency} ${AppManager.instance.userModel!.currentBalance!.amount} (Euro ${AppManager.instance.userModel!.currentBalance!.due_euro})";
    } else {
      balance.value = "";
    }
    update();
  }

  String getNoteVal() => rechargeApproveRejectNote.value;

  void setNoteVal(String note) {
    rechargeApproveRejectNote.value = note;
  }

  @override
  void onInit() {
    super.onInit();
  }

  @override
  void onReady() {
    super.onReady();
  }

  @override
  void onClose() {}
}
