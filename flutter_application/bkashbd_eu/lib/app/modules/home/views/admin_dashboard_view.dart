import 'package:flutter/material.dart';

import 'package:get/get.dart';

class AdminDashboardView extends GetView {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('AdminDashboardView'),
        centerTitle: true,
      ),
      body: Center(
        child: Text(
          'AdminDashboardView is working',
          style: TextStyle(fontSize: 20),
        ),
      ),
    );
  }
}
