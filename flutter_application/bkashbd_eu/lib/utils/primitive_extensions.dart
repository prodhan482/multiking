import 'package:bkashbd_eu/utils/constants.dart';

extension IntExtension on num {
  ///[ScreenUtil.setWidth]
  double get w => SizeConfig.setWidth(this);

  ///[ScreenUtil.setHeight]
  double get h => SizeConfig.setHeight(this);

  ///[ScreenUtil.setHeight]
  double get s => SizeConfig.setScale(this);

  ///[ScreenUtil.radius]
  double get r => SizeConfig.radius(this);

  ///[ScreenUtil.setSp]
  double get sp => SizeConfig.setSp(this);

  ///[ScreenUtil.setSp]
  @Deprecated('please use [sp]')
  double get ssp => SizeConfig.setSp(this);

  ///[ScreenUtil.setSp]
  @Deprecated(
      'please use [sp] , and set textScaleFactor: 1.0 , for example: Text("text", textScaleFactor: 1.0)')
  double get nsp => SizeConfig.setSp(this);

  ///屏幕宽度的倍数
  ///Multiple of screen width
  double get sw => SizeConfig.screenWidth * this;

  ///屏幕高度的倍数
  ///Multiple of screen height
  double get sh => SizeConfig.screenHeight * this;
}
