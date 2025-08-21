import 'package:get/get.dart';

class ReportController extends GetxController {
  //TODO: Implement ReportController

  final String reportType;

  final count = 0.obs;

  @override
  void onReady() {
    super.onReady();
  }

  void increment() => count.value++;

  ReportController(this.reportType);

  @override
  void onInit() {
    super.onInit();
    Get.log('ProductDetailsController created with id: $reportType');
  }

  @override
  void onClose() {
    Get.log('ProductDetailsController close with id: $reportType');
    super.onClose();
  }
}
