import 'package:get/get.dart';

import 'package:bkashbd_eu/app/modules/home/bindings/home_binding.dart';
import 'package:bkashbd_eu/app/modules/home/views/home_view.dart';
import 'package:bkashbd_eu/app/modules/landing/bindings/landing_binding.dart';
import 'package:bkashbd_eu/app/modules/landing/views/landing_view.dart';
import 'package:bkashbd_eu/app/modules/login/bindings/login_binding.dart';
import 'package:bkashbd_eu/app/modules/login/views/login_view.dart';
import 'package:bkashbd_eu/app/modules/report/bindings/report_binding.dart';
import 'package:bkashbd_eu/app/modules/report/views/report_view.dart';

part 'app_routes.dart';

class AppPages {
  AppPages._();

  static const INITIAL = Routes.LOGIN;

  static final routes = [
    GetPage(
      name: _Paths.HOME,
      page: () => HomeView(),
      binding: HomeBinding(),
    ),
    GetPage(
      name: _Paths.LANDING,
      page: () => LandingView(),
      binding: LandingBinding(),
    ),
    GetPage(
      name: _Paths.REPORT,
      page: () => ReportView(),
      binding: ReportBinding(),
      transition: Transition.zoom,
      children: [
        GetPage(
          name: _Paths.REPORT_TYPE,
          page: () => ReportView(),
          binding: ReportBinding(),
        ),
      ],
    ),
    GetPage(
      name: _Paths.LOGIN,
      page: () => LoginView(),
      binding: LoginBinding(),
    ),
  ];
}
