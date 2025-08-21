part of 'app_pages.dart';
// DO NOT EDIT. This is code generated via package:get_cli/get_cli.dart

abstract class Routes {
  static const HOME = _Paths.HOME;
  static const LANDING = _Paths.LANDING;
  static const LOGIN = _Paths.LOGIN;

  Routes._();

  static String REPORT(String report_type) => '/report/$report_type';
}

abstract class _Paths {
  static const HOME = '/home';
  static const LANDING = '/landing';
  static const REPORT = '/report';
  static const LOGIN = '/login';
  static const REPORT_TYPE = '/:report_type';
}
