import 'package:flutter/material.dart';

import 'package:get/get.dart';

class RechargesView extends GetView {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('RechargesView'),
        centerTitle: true,
      ),
      body: Center(
        child: Text(
          'RechargesView is working',
          style: TextStyle(fontSize: 20),
        ),
      ),
    );
  }
}
