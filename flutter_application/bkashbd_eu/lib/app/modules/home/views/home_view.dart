import 'package:backdrop/backdrop.dart';
import 'package:bkashbd_eu/app/modules/home/providers/home_provider.dart';
import 'package:bkashbd_eu/app/modules/home/views/menu_view_view.dart';
import 'package:bkashbd_eu/app/modules/home/views/recharge_list_view.dart';
import 'package:bkashbd_eu/app/modules/home/views/user_profile_view.dart';
import 'package:bkashbd_eu/utils/app_manager.dart';
import 'package:bkashbd_eu/utils/hex_color.dart';
import 'package:bkashbd_eu/utils/img.dart';
import 'package:bkashbd_eu/utils/my_text.dart';
import 'package:flutter/material.dart';

import 'package:get/get.dart';

import '../controllers/home_controller.dart';
import 'dashboard_view.dart';

class HomeView extends GetView<HomeController> {
  GlobalKey<ScaffoldState> scaffoldKey = GlobalKey<ScaffoldState>();

  @override
  Widget build(BuildContext context) {
    //final HomeController _tabx = Get.put(HomeController());

    controller.loadUserProfileData(() {
      controller.prepareScreenValue();
    });

    return SafeArea(
        child: Scaffold(
            key: scaffoldKey,
            backgroundColor: Colors.white,
            body: BackdropScaffold(
                appBar: BackdropAppBar(
                  //automaticallyImplyLeading: false,
                  title: Text("Dashboard",
                      style: MyText.subhead(context)!.copyWith(
                          color: Colors.white, fontWeight: FontWeight.w500)),
                  backgroundColor: HexColor("#0F4EDC"),
                  brightness: Brightness.dark,
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
                ),
                backLayer: MenuViewView(),
                subHeader: GetBuilder<HomeController>(
                  builder: (_controller) {
                    return (AppManager.instance.userModel!.data!.userType ==
                            "store"
                        ? BackdropSubHeader(
                            title: Text(_controller.balance.value),
                          )
                        : Container());
                  },
                ),
                frontLayer: GetX<HomeController>(
                  init: HomeController(),
                  builder: (_controller) {
                    switch (_controller.getBottomNavPosition()) {
                      case 1:
                        return RechargeListView();
                      case 2:
                        return UserProfileView();
                      case 0:
                      default:
                        return DashboardView();
                    }
                  },
                ),
                bottomNavigationBar: GetBuilder<HomeController>(
                  builder: (_controller) => BottomNavigationBar(
                    backgroundColor: Colors.white,
                    selectedItemColor: HexColor("#F23E7F"),
                    unselectedItemColor: HexColor("#919191"),
                    currentIndex: _controller.getBottomNavPosition(),
                    onTap: (int index) {
                      _controller.setBottomNavPosition(index);
                    },
                    items: [
                      BottomNav('Home', Icons.dashboard, null),
                      BottomNav('Recharge', Icons.phone_android, null),
                      BottomNav('Settings', Icons.settings, null)
                    ].map((BottomNav d) {
                      return BottomNavigationBarItem(
                        icon: Icon(d.icon),
                        label: d.title,
                      );
                    }).toList(),
                  ),
                ))));
  }
}

class BottomNav {
  String title;
  IconData icon;
  Color? color;
  bool badge = false;
  String badgeText = "";

  BottomNav(this.title, this.icon, this.color);
  BottomNav.count(
      this.title, this.icon, this.color, this.badge, this.badgeText);
}
