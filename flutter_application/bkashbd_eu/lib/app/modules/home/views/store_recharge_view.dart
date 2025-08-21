import 'package:bkashbd_eu/app/constants.dart';
import 'package:bkashbd_eu/app/modules/home/controllers/home_controller.dart';
import 'package:bkashbd_eu/app/modules/home/model/recharge_list_response.dart';
import 'package:bkashbd_eu/utils/app_manager.dart';
import 'package:bkashbd_eu/utils/hex_color.dart';
import 'package:bkashbd_eu/utils/my_text.dart';
import 'package:bkashbd_eu/utils/primitive_extensions.dart';
import 'package:collection/collection.dart';
import 'package:flutter/material.dart';

import 'package:get/get.dart';

class StoreRechargeView extends GetView<HomeController> {
  final List? mfsList;
  final List? mfsPackageList;

  const StoreRechargeView(
      {Key? key, required this.mfsList, required this.mfsPackageList})
      : super(key: key);

  @override
  Widget build(BuildContext context) {
    Map<String, List<MfsPackageList>> mfsPackageListById = {};
    if (mfsPackageList != null) {
      for (var mfsPackage in mfsPackageList!) {
        if (mfsPackage.mfsId != null &&
            !mfsPackageListById.containsKey(mfsPackage.mfsId)) {
          mfsPackageListById[mfsPackage.mfsId] = [];
        }
        if (mfsPackage.mfsId != null) {
          mfsPackageListById[mfsPackage.mfsId]!.add(mfsPackage);
        }
      }
    }

    Map<String, MfsList> mfsListById = {};
    if (mfsList != null) {
      for (var mfs in mfsList!) {
        if (mfs.mfsId != null) {
          mfsListById[mfs.mfsId] = mfs;
        }
      }
    }

    return Scaffold(
      body: SingleChildScrollView(
        padding: EdgeInsets.all(15.s),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: <Widget>[
            GetBuilder<HomeController>(
                init: HomeController(),
                builder: (controller) => Wrap(
                      alignment: WrapAlignment.center,
                      spacing: 10.0, // gap between adjacent chips
                      runSpacing: 4.0, // gap between lines
                      children: mfsList!
                          .mapIndexed<Widget>((position, mfs) => InkWell(
                              child: Container(
                                  child: Image.network(
                                      "${Constants.BASE_URL}/${mfs.imagePath}",
                                      fit: BoxFit.cover),
                                  width: 60),
                              onTap: () {
                                controller.selectedMfsId.value =
                                    mfsList![position].mfsId;
                                controller.update();
                              }))
                          .toList(),
                    )),
            Container(height: 30.s),
            GetBuilder<HomeController>(
                init: HomeController(),
                builder: (controller) => GestureDetector(
                    behavior: HitTestBehavior.translucent,
                    onTap: () {
                      showMFSchangeBS(context, (selected_mfs) {
                        controller.selectedMfsId.value = selected_mfs.mfsId;
                        controller.update();
                      });
                    },
                    child: Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(10.0),
                      decoration: BoxDecoration(
                          color: Colors.white,
                          border: Border.all(color: Colors.grey.shade400)),
                      child: ((!(controller.selectedMfsId.value.length > 2 &&
                              mfsListById.length > 1))
                          ? Text('Select A MFS',
                              style: MyText.body1(context)!
                                  .copyWith(color: Colors.grey.shade400))
                          : Row(
                              children: <Widget>[
                                Container(
                                    child: Image.network(
                                        "${Constants.BASE_URL}/${mfsListById[controller.selectedMfsId]!.imagePath}",
                                        fit: BoxFit.cover),
                                    height: 30.s),
                                Container(width: 10.s),
                                Flexible(
                                    fit: FlexFit.tight,
                                    child: Text(
                                        mfsListById[controller.selectedMfsId]!
                                            .mfsName!,
                                        textAlign: TextAlign.left))
                              ],
                            )),
                    ))),
            Container(height: 15.s),
            Container(
              height: 45.s,
              decoration: BoxDecoration(
                  color: Colors.white,
                  border: Border.all(color: Colors.grey.shade400)),
              alignment: Alignment.centerLeft,
              padding: EdgeInsets.symmetric(horizontal: 25),
              child: GetBuilder<HomeController>(
                  init: HomeController(),
                  builder: (controller) => TextField(
                        maxLines: 1,
                        keyboardType: TextInputType.phone,
                        onChanged: (text) {
                          controller.enteredMobileNumber.value = text;
                          controller.update();
                        },
                        decoration: InputDecoration(
                            contentPadding: EdgeInsets.all(-12),
                            border: InputBorder.none,
                            hintText: "Put Mobile Number",
                            hintStyle: MyText.body1(context)!
                                .copyWith(color: Colors.grey.shade400)),
                      )),
            ),
            Container(height: 15.s),
            GetX<HomeController>(
                init: HomeController(),
                builder: (controller) => Visibility(
                    visible: (mfsPackageListById
                        .containsKey(controller.selectedMfsId.value)),
                    child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          GestureDetector(
                              behavior: HitTestBehavior.translucent,
                              onTap: () {
                                if (controller.selectedMfsId.value.isEmpty ||
                                    !mfsPackageListById.containsKey(
                                        controller.selectedMfsId.value)) {
                                  AppManager.instance.showToast(
                                      context, "Please Select a MFS");
                                } else {
                                  showPackageChangeBS(
                                      context,
                                      mfsPackageListById,
                                      mfsListById[controller.selectedMfsId
                                          .value]!, (selectedPackage) {
                                    controller.selectedPackageId.value =
                                        selectedPackage.rowId;
                                    controller.selectedPackageName.value =
                                        selectedPackage.packageName;
                                  });
                                }
                              },
                              child: Container(
                                width: double.infinity,
                                padding: const EdgeInsets.all(10.0),
                                decoration: BoxDecoration(
                                    color: Colors.white,
                                    border: Border.all(
                                        color: Colors.grey.shade400)),
                                //child: Text('Select A MFS'),
                                child: ((controller
                                        .selectedPackageName.value.isNotEmpty)
                                    ? Text(controller.selectedPackageName.value,
                                        textAlign: TextAlign.left)
                                    : Text("Select A Package",
                                        textAlign: TextAlign.left,
                                        style: MyText.body1(context)!.copyWith(
                                            color: Colors.grey.shade400))),
                              )),
                          Container(height: 15.s),
                        ]))),
            GetBuilder<HomeController>(
                init: HomeController(),
                builder: (controller) => GestureDetector(
                    behavior: HitTestBehavior.translucent,
                    onTap: () {
                      if (controller.selectedMfsId.value.length == 0) {
                        AppManager.instance
                            .showToast(context, "Please Select a MFS");
                      } else {
                        showTypeChangeBS(
                            context,
                            (mfsListById![controller.selectedMfsId.value]!
                                        .mfsType ==
                                    "mobile_recharge"
                                ? ["prepaid", "postpaid"]
                                : ["personal", "agent"]),
                            mfsListById[controller.selectedMfsId]!,
                            (selectedPackage) {
                          controller.selectedTypeId.value = selectedPackage;
                          controller.update();
                        });
                      }
                    },
                    child: Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(10.0),
                      decoration: BoxDecoration(
                          color: Colors.white,
                          border: Border.all(color: Colors.grey.shade400)),
                      //child: Text('Select A MFS'),
                      child: ((controller.selectedTypeId.value.isNotEmpty)
                          ? Text(controller.selectedTypeId.value.toUpperCase(),
                              textAlign: TextAlign.left)
                          : Text("Select A Type",
                              textAlign: TextAlign.left,
                              style: MyText.body1(context)!
                                  .copyWith(color: Colors.grey.shade400))),
                    ))),
            Container(height: 15.s),
            Container(
              height: 45.s,
              decoration: BoxDecoration(
                  color: Colors.white,
                  border: Border.all(color: Colors.grey.shade400)),
              alignment: Alignment.centerLeft,
              padding: EdgeInsets.symmetric(horizontal: 25),
              child: GetBuilder<HomeController>(
                  init: HomeController(),
                  builder: (controller) => TextField(
                        maxLines: 1,
                        controller: controller.enteredSendAmount,
                        onChanged: (text) {
                          controller.calculateReceiveMoney();
                        },
                        keyboardType: TextInputType.number,
                        decoration: InputDecoration(
                            contentPadding: EdgeInsets.all(-12),
                            border: InputBorder.none,
                            hintText: "Send Amount",
                            hintStyle: MyText.body1(context)!
                                .copyWith(color: Colors.grey.shade400)),
                      )),
            ),
            Container(height: 15.s),
            Row(
              children: <Widget>[
                Flexible(
                    fit: FlexFit.tight,
                    child: GetBuilder<HomeController>(
                        init: HomeController(),
                        builder: (controller) => GestureDetector(
                              onTap: () {
                                controller.sendingEuro.value = true;
                              },
                              child: Container(
                                  width: double.infinity,
                                  padding: const EdgeInsets.all(10.0),
                                  decoration:
                                      BoxDecoration(color: Colors.red[400]),
                                  child: Text("Send in EURO",
                                      textAlign: TextAlign.center,
                                      style: TextStyle(color: Colors.white))),
                            ))),
                Flexible(
                    fit: FlexFit.tight,
                    child: GetBuilder<HomeController>(
                        init: HomeController(),
                        builder: (controller) => GestureDetector(
                            onTap: () {
                              controller.sendingEuro.value = false;
                            },
                            child: Container(
                              width: double.infinity,
                              padding: const EdgeInsets.all(10.0),
                              decoration:
                                  BoxDecoration(color: HexColor("#0F4EDC")),
                              //child: Text('Select A MFS'),
                              child: Text(
                                  "Send in ${AppManager.instance.userModel!.data!.storeBaseCurrency!.split("_").join(" ").toUpperCase()}",
                                  textAlign: TextAlign.center,
                                  style: TextStyle(color: Colors.white)),
                            ))))
              ],
            ),
            Obx(() => Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Visibility(
                        child: RechargeByEuro(mfsListById: mfsListById),
                        visible: controller.sendingEuro.value),
                    Visibility(
                        child: RechargeByOtherCurrency(),
                        visible: !controller.sendingEuro.value)
                  ],
                )),
            Container(height: 15.s),
            Container(
              width: double.infinity,
              //width: (MediaQuery.of(context).size.width - 65),
              child: GetBuilder<HomeController>(
                  builder: (_c) => ElevatedButton(
                        style: ElevatedButton.styleFrom(
                            primary: HexColor("#0F4EDC"), elevation: 0),
                        child: Text("Do Recharge",
                            style: TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.bold,
                                fontSize: 16.sp)),
                        onPressed: () {
                          ShowConfirmation(context, () {});
                        },
                      )),
            ),
            Container(height: 50),
          ],
        ),
      ),
    );
  }

  void showMFSchangeBS(BuildContext context, Function callback) {
    showModalBottomSheet(
        context: context,
        builder: (BuildContext bc) {
          return SafeArea(
              child: SingleChildScrollView(
                  child: Container(
                      color: Colors.white,
                      padding: EdgeInsets.symmetric(vertical: 0, horizontal: 5),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: <Widget>[
                          Container(
                            height: 20.s,
                          ),
                          Padding(
                            padding: const EdgeInsets.only(left: 10.0),
                            child: Align(
                              alignment: Alignment.centerLeft,
                              child: Text("Select A MFS",
                                  style: TextStyle(
                                    color: Colors.grey[700],
                                  )),
                            ),
                          ),
                          Container(
                            height: 20.s,
                          ),
                          Column(
                            children: mfsList!.map<Widget>((mfs) {
                              return ListTile(
                                leading: Container(
                                    child: Image.network(
                                        "${Constants.BASE_URL}/${mfs.imagePath}",
                                        fit: BoxFit.cover),
                                    width: 30),
                                title: Text(mfs.mfsName),
                                onTap: () {
                                  callback(mfs);
                                  Navigator.pop(context);
                                },
                              );
                            }).toList(),
                          )
                        ],
                      ))));
        });
  }

  void showPackageChangeBS(
      BuildContext context,
      Map<String, List<MfsPackageList>> mfsPackageListById,
      MfsList SelectedMFS,
      Function callback) {
    showModalBottomSheet(
        context: context,
        builder: (BuildContext bc) {
          return SafeArea(
              child: SingleChildScrollView(
                  child: Container(
                      color: Colors.white,
                      padding: EdgeInsets.symmetric(vertical: 0, horizontal: 5),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: <Widget>[
                          Container(
                            height: 20.s,
                          ),
                          Padding(
                            padding: const EdgeInsets.only(left: 10.0),
                            child: Align(
                              alignment: Alignment.centerLeft,
                              child: Text("Change Package",
                                  style: TextStyle(
                                    color: Colors.grey[700],
                                  )),
                            ),
                          ),
                          Container(
                            height: 20.s,
                          ),
                          Column(
                            children: mfsPackageListById[SelectedMFS.mfsId]!
                                .map<Widget>((mfsPackage) {
                              return ListTile(
                                leading: Container(
                                    child: Image.network(
                                        "${Constants.BASE_URL}/${SelectedMFS.imagePath}",
                                        fit: BoxFit.cover),
                                    width: 30),
                                title: Text(mfsPackage.packageName!),
                                subtitle: Text(
                                    "Amount: ${mfsPackage.amount!}\nCost: ${mfsPackage.amount!}\n"),
                                onTap: () {
                                  callback(mfsPackage);
                                  Navigator.pop(context);
                                },
                              );
                            }).toList(),
                          )
                        ],
                      ))));
        });
  }

  void showTypeChangeBS(BuildContext context, List<String> types,
      MfsList SelectedMFS, Function callback) {
    showModalBottomSheet(
        context: context,
        builder: (BuildContext bc) {
          return SafeArea(
              child: SingleChildScrollView(
                  child: Container(
                      color: Colors.white,
                      padding: EdgeInsets.symmetric(vertical: 0, horizontal: 5),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: <Widget>[
                          Container(
                            height: 20.s,
                          ),
                          Padding(
                            padding: const EdgeInsets.only(left: 10.0),
                            child: Align(
                              alignment: Alignment.centerLeft,
                              child: Text("Change Type",
                                  style: TextStyle(
                                    color: Colors.grey[700],
                                  )),
                            ),
                          ),
                          Container(
                            height: 20.s,
                          ),
                          Column(
                            children: types.map<Widget>((type) {
                              return ListTile(
                                leading: Container(
                                    child: Image.network(
                                        "${Constants.BASE_URL}/${SelectedMFS.imagePath}",
                                        fit: BoxFit.cover),
                                    width: 30),
                                title: Text(type.toUpperCase()),
                                onTap: () {
                                  callback(type);
                                  Navigator.pop(context);
                                },
                              );
                            }).toList(),
                          )
                        ],
                      ))));
        });
  }

  void ShowConfirmation(BuildContext context, Function callback) {
    showModalBottomSheet(
        context: context,
        builder: (BuildContext bc) {
          return SafeArea(
              child: SingleChildScrollView(
                  child: Container(
                      color: Colors.white,
                      padding: EdgeInsets.symmetric(vertical: 0, horizontal: 5),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: <Widget>[
                          Container(
                            height: 20.s,
                          ),
                          Padding(
                            padding: const EdgeInsets.only(left: 10.0),
                            child: Align(
                              alignment: Alignment.centerLeft,
                              child: Text("Please confirm about the recharge.",
                                  style: TextStyle(
                                    color: Colors.grey[700],
                                  )),
                            ),
                          ),
                          Container(
                            height: 20.s,
                          ),
                          Container(height: 15.s),
                          Container(
                            width: double.infinity,
                            //width: (MediaQuery.of(context).size.width - 65),
                            child: GetBuilder<HomeController>(
                                builder: (_c) => ElevatedButton(
                                      style: ElevatedButton.styleFrom(
                                          primary: HexColor("#0F4EDC"),
                                          elevation: 0),
                                      child: Text("Confirm",
                                          style: TextStyle(
                                              color: Colors.white,
                                              fontWeight: FontWeight.bold,
                                              fontSize: 16.sp)),
                                      onPressed: () {
                                        callback();
                                        Navigator.pop(context);
                                      },
                                    )),
                          ),
                          Container(height: 15.s),
                        ],
                      ))));
        });
  }
}

class RechargeByEuro extends GetView<HomeController> {
  final Map<String, MfsList> mfsListById;
  //final List? mfsPackageList;

  const RechargeByEuro({
    Key? key,
    required this.mfsListById,
    //required this.mfsPackageList
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(10.0),
        decoration: BoxDecoration(
            color: Colors.white,
            //border: Border.all(color: Colors.red.shade400)),
            border: Border(
                left: BorderSide(width: 2.0, color: Colors.red.shade400),
                bottom: BorderSide(width: 2.0, color: Colors.red.shade400),
                right: BorderSide(width: 2.0, color: Colors.red.shade400))),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: <
            Widget>[
          GetBuilder<HomeController>(
              init: HomeController(),
              builder: (controller) => GestureDetector(
                  behavior: HitTestBehavior.translucent,
                  onTap: () {
                    if (controller.selectedMfsId.value.length == 0) {
                      AppManager.instance
                          .showToast(context, "Please Select a MFS");
                    } else {
                      showWithWithoutChargeChangeBS(
                          context,
                          ["With Charge", "Without Charge"],
                          mfsListById[controller.selectedMfsId.value]!, (type) {
                        controller.selectedChargeWithoutChargeId.value = type;
                        controller.update();
                      });
                    }
                  },
                  child: Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(10.0),
                    decoration: BoxDecoration(
                        color: Colors.white,
                        border: Border.all(color: Colors.grey.shade400)),
                    child: ((controller
                            .selectedChargeWithoutChargeId.value.isNotEmpty)
                        ? Text(controller.selectedChargeWithoutChargeId.value,
                            textAlign: TextAlign.left)
                        : Text("With/Without Charge",
                            textAlign: TextAlign.left,
                            style: MyText.body1(context)!
                                .copyWith(color: Colors.grey.shade400))),
                  ))),
          Container(height: 15.s),
          Container(
            height: 45.s,
            decoration: BoxDecoration(
                color: Colors.white,
                border: Border.all(color: Colors.grey.shade400)),
            alignment: Alignment.centerLeft,
            padding: EdgeInsets.symmetric(horizontal: 25),
            child: TextField(
              controller: controller.enteredReceivedAmount,
              onChanged: (text) {
                controller.calculateSendMoney();
              },
              maxLines: 1,
              keyboardType: TextInputType.number,
              decoration: InputDecoration(
                  contentPadding: EdgeInsets.all(-12),
                  border: InputBorder.none,
                  hintText: "Received Amount",
                  hintStyle: MyText.body1(context)!
                      .copyWith(color: Colors.grey.shade400)),
            ),
          ),
          Container(height: 15.s),
          Container(
              height: 45.s,
              alignment: Alignment.centerLeft,
              child: Row(children: [
                Expanded(
                    child: Container(
                        padding: EdgeInsets.symmetric(horizontal: 25.s),
                        decoration: BoxDecoration(
                            color: Colors.white,
                            border: Border.all(color: Colors.grey.shade400)),
                        child: TextField(
                          maxLines: 1,
                          onChanged: (text) {
                            controller.calculateReceiveMoney();
                          },
                          controller: controller.enteredConversionRate,
                          keyboardType: TextInputType.number,
                          decoration: InputDecoration(
                              contentPadding: EdgeInsets.all(-12),
                              border: InputBorder.none,
                              hintText: "Conv. Rate (1 € = ? BDT)",
                              hintStyle: MyText.body1(context)!
                                  .copyWith(color: Colors.grey.shade400)),
                        ))),
                Container(
                  padding: EdgeInsets.all(5.s),
                  height: double.infinity,
                  decoration: BoxDecoration(
                      color: Colors.white,
                      //border: Border.all(color: Colors.red.shade400)),
                      border: Border(
                          top: BorderSide(
                              width: 1.0, color: Colors.grey.shade400),
                          bottom: BorderSide(
                              width: 1.0, color: Colors.grey.shade400),
                          right: BorderSide(
                              width: 1.0, color: Colors.grey.shade400))),
                  child: Padding(
                    padding: EdgeInsets.fromLTRB(7.s, 7.s, 7.s, 7.s),
                    child: Text(
                        AppManager.instance.userModel!.data!.storeBaseCurrency!
                            .split("_")
                            .join(" ")
                            .toUpperCase(),
                        textAlign: TextAlign.left),
                  ),
                ),
                GestureDetector(
                  child: Container(
                    padding: EdgeInsets.all(5.s),
                    height: double.infinity,
                    decoration: BoxDecoration(
                        color: Colors.deepOrange,
                        //border: Border.all(color: Colors.red.shade400)),
                        border: Border(
                            top: BorderSide(
                                width: 1.0, color: Colors.deepOrange),
                            bottom: BorderSide(
                                width: 1.0, color: Colors.deepOrange),
                            right: BorderSide(
                                width: 1.0, color: Colors.deepOrange))),
                    child: Padding(
                      padding: EdgeInsets.fromLTRB(10.s, 7.s, 10.s, 7.s),
                      child: Text("Set",
                          textAlign: TextAlign.left,
                          style: TextStyle(color: Colors.white)),
                    ),
                  ),
                )
              ])),
          Container(height: 15.s),
          Container(
              height: 45.s,
              alignment: Alignment.centerLeft,
              child: Row(children: [
                Expanded(
                    child: Container(
                        padding: EdgeInsets.symmetric(horizontal: 25.s),
                        decoration: BoxDecoration(
                            color: Colors.white,
                            border: Border.all(color: Colors.grey.shade400)),
                        child: TextField(
                          maxLines: 1,
                          onChanged: (text) {
                            controller.calculateReceiveMoney();
                          },
                          controller: controller.enteredServiceCharge,
                          keyboardType: TextInputType.number,
                          decoration: InputDecoration(
                              contentPadding: EdgeInsets.all(-12),
                              border: InputBorder.none,
                              hintText: "Service Charge",
                              hintStyle: MyText.body1(context)!
                                  .copyWith(color: Colors.grey.shade400)),
                        ))),
                Container(
                  padding: EdgeInsets.all(5.s),
                  height: double.infinity,
                  decoration: BoxDecoration(
                      color: Colors.white,
                      //border: Border.all(color: Colors.red.shade400)),
                      border: Border(
                          top: BorderSide(
                              width: 1.0, color: Colors.grey.shade400),
                          bottom: BorderSide(
                              width: 1.0, color: Colors.grey.shade400),
                          right: BorderSide(
                              width: 1.0, color: Colors.grey.shade400))),
                  child: Padding(
                    padding: EdgeInsets.fromLTRB(0, 7.s, 0, 7.s),
                    child: Text("Euro (€)", textAlign: TextAlign.left),
                  ),
                ),
                GestureDetector(
                  child: Container(
                    padding: EdgeInsets.all(5.s),
                    height: double.infinity,
                    decoration: BoxDecoration(
                        color: Colors.deepOrange,
                        //border: Border.all(color: Colors.red.shade400)),
                        border: Border(
                            top: BorderSide(
                                width: 1.0, color: Colors.deepOrange),
                            bottom: BorderSide(
                                width: 1.0, color: Colors.deepOrange),
                            right: BorderSide(
                                width: 1.0, color: Colors.deepOrange))),
                    child: Padding(
                      padding: EdgeInsets.fromLTRB(10.s, 7.s, 10.s, 7.s),
                      child: Text("Set",
                          textAlign: TextAlign.left,
                          style: TextStyle(color: Colors.white)),
                    ),
                  ),
                )
              ]))
        ]));
  }

  void showWithWithoutChargeChangeBS(BuildContext context,
      List<String> chargeTypes, MfsList SelectedMFS, Function callback) {
    showModalBottomSheet(
        context: context,
        builder: (BuildContext bc) {
          return SafeArea(
              child: SingleChildScrollView(
                  child: Container(
                      color: Colors.white,
                      padding: EdgeInsets.symmetric(vertical: 0, horizontal: 5),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: <Widget>[
                          Container(
                            height: 20.s,
                          ),
                          Padding(
                            padding: const EdgeInsets.only(left: 10.0),
                            child: Align(
                              alignment: Alignment.centerLeft,
                              child: Text("Select With/Without Charge",
                                  style: TextStyle(
                                    color: Colors.grey[700],
                                  )),
                            ),
                          ),
                          Container(
                            height: 20.s,
                          ),
                          Column(
                            children: chargeTypes.map<Widget>((type) {
                              return ListTile(
                                leading: Container(
                                    child: Image.network(
                                        "${Constants.BASE_URL}/${SelectedMFS.imagePath}",
                                        fit: BoxFit.cover),
                                    width: 30),
                                title: Text(type),
                                onTap: () {
                                  callback(type);
                                  Navigator.pop(context);
                                },
                              );
                            }).toList(),
                          )
                        ],
                      ))));
        });
  }
}

class RechargeByOtherCurrency extends GetView<HomeController> {
  //final List? mfsList;
  //final List? mfsPackageList;

  const RechargeByOtherCurrency({
    Key? key,
    //required this.mfsList,
    //required this.mfsPackageList
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(10.0),
        decoration: BoxDecoration(
            color: Colors.white,
            //border: Border.all(color: Colors.red.shade400)),
            border: Border(
                left: BorderSide(width: 2.0, color: HexColor("#0F4EDC")),
                bottom: BorderSide(width: 2.0, color: HexColor("#0F4EDC")),
                right: BorderSide(width: 2.0, color: HexColor("#0F4EDC")))),
        child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: <Widget>[
              Container(
                height: 45.s,
                decoration: BoxDecoration(
                    color: Colors.white,
                    border: Border.all(color: Colors.grey.shade400)),
                alignment: Alignment.centerLeft,
                padding: EdgeInsets.symmetric(horizontal: 25),
                child: TextField(
                  maxLines: 1,
                  keyboardType: TextInputType.number,
                  decoration: InputDecoration(
                      contentPadding: EdgeInsets.all(-12),
                      border: InputBorder.none,
                      hintText: "Received Amount",
                      hintStyle: MyText.body1(context)!
                          .copyWith(color: Colors.grey.shade400)),
                ),
              )
            ]));
  }
}
