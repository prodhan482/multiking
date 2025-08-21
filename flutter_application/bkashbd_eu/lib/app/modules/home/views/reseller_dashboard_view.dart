import 'package:flutter/material.dart';

import 'package:get/get.dart';

class ResellerDashboardView extends GetView {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('ResellerDashboardView'),
        centerTitle: true,
      ),
      body: Center(
        child: Text(
          'ResellerDashboardView is working',
          style: TextStyle(fontSize: 20),
        ),
      ),
    );
  }
}
