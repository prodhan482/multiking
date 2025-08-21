import 'package:backdrop/backdrop.dart';
import 'package:bkashbd_eu/utils/hex_color.dart';
import 'package:bkashbd_eu/utils/my_text.dart';
import 'package:bkashbd_eu/utils/string_casing_extension.dart';
import 'package:dropdown_search/dropdown_search.dart';
import 'package:flutter/material.dart';

import 'package:get/get.dart';

import '../controllers/report_controller.dart';

class ReportView extends GetWidget<ReportController> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
        body: BackdropScaffold(
            appBar: BackdropAppBar(
              //automaticallyImplyLeading: false,
              title: Text(
                  controller.reportType.split('_').join(" ").toTitleCase(),
                  style: MyText.subhead(context)!.copyWith(
                      color: Colors.white, fontWeight: FontWeight.w500)),
              backgroundColor: HexColor("#0F4EDC"),
              brightness: Brightness.dark,
              leading: IconButton(
                icon: Icon(Icons.arrow_back),
                onPressed: () {
                  Get.back();
                },
              ),
              actions: <Widget>[
                //BackdropToggleButton(icon: AnimatedIcons.ellipsis_search)
                Builder(builder: (BuildContext context) {
                  return IconButton(
                    icon: (Backdrop.of(context).isBackLayerRevealed
                        ? Icon(Icons.close)
                        : Icon(Icons.search)),
                    onPressed: () {
                      if (Backdrop.of(context).isBackLayerRevealed) {
                        Backdrop.of(context).concealBackLayer();
                      } else {
                        Backdrop.of(context).revealBackLayer();
                      }
                    },
                  );
                }),
              ],

              /*
              actions: <Widget>[
                IconButton(
                  icon: Icon(Icons.account_circle),
                  onPressed: () {},
                ),
                IconButton(
                  icon: Icon(Icons.notifications),
                  onPressed: () {},
                ),
              ],
              */
            ),
            backLayer: Menu(),
            subHeader: (false
                ? BackdropSubHeader(
                    title: Text("Balance 100 (Euro 100)"),
                  )
                : null),
            frontLayer: (() {
              switch (controller.reportType) {
                case "recharge_history":
                  return Container();
                case "balance_recharge":
                  return Container();
                case "payment_history":
                  return Container();
                case "payment_return":
                  return Container();
                case "due_statement":
                  return Container();
                default:
                  return Container();
              }
            }())));
  }
}

class Menu extends GetView {
  BuildContext? ctx;

  @override
  Widget build(BuildContext context) {
    ctx = context;
    return Scaffold(
      body: Container(
        decoration: BoxDecoration(
          color: HexColor("#0F4EDC"),
        ),
        child: Container(
          child: DropdownSearch<String>(
            mode: Mode.BOTTOM_SHEET,
            items: ["Brazil", "France", "Tunisia", "Canada"],
            label: "Select A Reseller",
            //onChanged: print,
            //selectedItem: "Tunisia",
            showSearchBox: true,
            //itemAsString: (String? u) => u.userAsString(),
            onChanged: (String? data) => print(data),
          ),
        ),
      ),
    );
  }

  void onItemClick(String menu_name) {
    //MyToast.show(obj.name!, context, duration: MyToast.LENGTH_SHORT);
    print("------------------> Clicked ${menu_name}");
    /*Backdrop.of(ctx!).concealBackLayer();
    switch (menu_name) {
      case "Resellers":
        {}
        break;

      case "Vendor":
        {}
        break;

      case "MFS":
        {}
        break;

      case "Users":
        {}
        break;

      // Reports <---START--->
      case "Recharge History":
        {
          Get.toNamed(Routes.REPORT("recharge_history"));
        }
        break;

      case "Balance Recharge":
        {
          Get.toNamed(Routes.REPORT("balance_recharge"));
        }
        break;

      case "Payment History":
        {
          Get.toNamed(Routes.REPORT("payment_history"));
        }
        break;

      case "Payment Return":
        {
          Get.toNamed(Routes.REPORT("payment_return"));
        }
        break;

      case "Due Statement":
        {
          Get.toNamed(Routes.REPORT("due_statement"));
        }
        break;
      // Reports <---END--->

      default:
        {}
        break;
    }*/
  }
}
