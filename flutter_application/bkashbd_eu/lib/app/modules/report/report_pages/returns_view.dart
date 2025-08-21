import 'package:flutter/material.dart';

import 'package:get/get.dart';

class ReturnsView extends GetView {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('ReturnsView'),
        centerTitle: true,
      ),
      body: Center(
        child: Text(
          'ReturnsView is working',
          style: TextStyle(fontSize: 20),
        ),
      ),
    );
  }
}
