import 'package:backdrop/backdrop.dart';
import 'package:bkashbd_eu/app/modules/home/controllers/home_controller.dart';
import 'package:bkashbd_eu/app/modules/home/list_adapter/menu.dart';
import 'package:bkashbd_eu/app/routes/app_pages.dart';
import 'package:bkashbd_eu/libraries/bmprogresshud/progresshud.dart';
import 'package:bkashbd_eu/utils/app_manager.dart';
import 'package:bkashbd_eu/utils/hex_color.dart';
import 'package:flutter/material.dart';

import 'package:get/get.dart';

class MenuViewView extends GetView<HomeController> {
  BuildContext? ctx;

  @override
  Widget build(BuildContext context) {
    ctx = context;
    return Scaffold(
      body: Container(
        decoration: BoxDecoration(
          color: HexColor("#0F4EDC"),
        ),
        child: Obx(() {
          List<MenuItem> menuItems = [];

          /*
          "UserController::list",
            "UserController::create",
            "UserController::remove",
            "UserController::update",
            "UserController::permission",
            "UserController::update_permission",
            "StoreController::list",
            "StoreController::create",
            "StoreController::update",
            "StoreController::remove",
            "VendorController::list",
            "VendorController::create",
            "VendorController::update",
            "VendorController::remove",
            "RechargeController::list",
            "RechargeController::create",
            "RechargeController::update",
            "PromotionController::list",
            "PromotionController::create",
            "PromotionController::update",
            "PromotionController::remove",
            "ReportController::vendor_adjustment",
            "ReportController::store_adjustment",
            "ReportController::transaction",
            "StoreController::adjust",
            "VendorController::adjust",
            "MFSController::list",
            "MFSController::create",
            "MFSController::update",
            "MFSController::remove",
            "MFSController::package_list",
            "MFSController::create_package",
            "MFSController::update_package",
            "ReportController::mfs_summery",
            "ReportController::reseller_balance_recharge",
            "ReportController::reseller_due_adjust",
            "ReportController::reseller_due_statement",
            "ReportController::payment_doc_upload_statement",
            "RechargeController::upload_payment_doc"
          */

          if (controller.permissions.contains("StoreController::list")) {
            menuItems.add(
                MenuItem(icon: Icons.people_outline, menu_name: "Resellers"));
          }

          if (controller.permissions.contains("VendorController::list")) {
            menuItems.add(MenuItem(
                icon: Icons.person_add_alt_1_outlined, menu_name: "Vendor"));
          }

          if (controller.permissions.contains("MFSController::list")) {
            menuItems.add(MenuItem(icon: Icons.sim_card, menu_name: "MFS"));
          }

          if (controller.permissions.contains("UserController::list")) {
            menuItems
                .add(MenuItem(icon: Icons.manage_accounts, menu_name: "Users"));
          }

          menuItems.add(MenuItem(icon: Icons.logout, menu_name: "Logout"));

          if (controller.permissions
                  .contains("ReportController::transaction") ||
              controller.permissions
                  .contains("ReportController::mfs_summery") ||
              controller.permissions
                  .contains("ReportController::reseller_balance_recharge") ||
              controller.permissions
                  .contains("ReportController::reseller_due_adjust") ||
              controller.permissions
                  .contains("ReportController::reseller_due_statement") ||
              controller.permissions
                  .contains("ReportController::payment_doc_upload_statement")) {
            menuItems.add(MenuItem(treatAsDivider: 1, menu_name: "Report"));
          }

          if (controller.permissions
              .contains("ReportController::transaction")) {
            menuItems.add(MenuItem(
                icon: Icons.request_quote, menu_name: "Recharge History"));
          }
          if (controller.permissions
              .contains("ReportController::mfs_summery")) {
            menuItems.add(
                MenuItem(icon: Icons.description, menu_name: "MFS Summery"));
          }
          if (controller.permissions
              .contains("ReportController::reseller_balance_recharge")) {
            menuItems.add(
                MenuItem(icon: Icons.article, menu_name: "Balance Recharge"));
          }
          if (controller.permissions
              .contains("ReportController::reseller_due_adjust")) {
            menuItems.add(MenuItem(
                icon: Icons.description_outlined,
                menu_name: "Payment History"));
          }
          if (controller.permissions
              .contains("ReportController::reseller_due_statement")) {
            menuItems.add(
                MenuItem(icon: Icons.summarize, menu_name: "Due Statement"));
          }
          if (controller.permissions
              .contains("ReportController::payment_doc_upload_statement")) {
            menuItems.add(MenuItem(
                icon: Icons.description, menu_name: "Payment Document Upload"));
          }

          return Menu(menuItems, context, onItemClick).getView();
        }),
      ),
    );
  }

  void onItemClick(String menu_name) {
    //MyToast.show(obj.name!, context, duration: MyToast.LENGTH_SHORT);
    print("------------------> Clicked ${menu_name}");
    Backdrop.of(ctx!).concealBackLayer();
    switch (menu_name) {
      case "Resellers":
        {
          //ProgressHud.showLoading();
        }
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

      case "Logout":
        {
          AppManager.instance.setLogOut();
          Get.toNamed(Routes.LOGIN);
        }
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
    }
  }
}
