import 'package:bkashbd_eu/app/routes/app_pages.dart';
import 'package:bkashbd_eu/utils/app_manager.dart';
import 'package:bkashbd_eu/utils/themes.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'language/language.dart';
import 'libraries/bmprogresshud/progresshud.dart';

void main() async {
  await AppManager.instance.init();
  runApp(
    AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark,
      child: MyApp(),
    ),
  );

  SystemChrome.setPreferredOrientations(
      [DeviceOrientation.portraitDown, DeviceOrientation.portraitUp]);
  SystemChrome.setEnabledSystemUIMode(SystemUiMode.manual, overlays: []);
}

class MyApp extends StatefulWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  _MyAppState createState() => _MyAppState();
}

class _MyAppState extends State<MyApp> {
  @override
  void initState() {
    // TODO: implement initState
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return ProgressHud(
      key: Key("global_progresshud_key"),
      isGlobalHud: true,
      child: GetMaterialApp(
        title: "bKashBD.eu",
        debugShowCheckedModeBanner: false,
        translations: Language(),
        //locale: AppManager.instance.getCurrentLocale(),
        theme: NHTheme.theme(),
        //initialRoute: (AppManager.instance.isLoggedIn()?Routes.HOME:Routes.LOGIN),
        initialRoute: Routes.LANDING,
        getPages: AppPages.routes,
      ),
    );
  }
}
