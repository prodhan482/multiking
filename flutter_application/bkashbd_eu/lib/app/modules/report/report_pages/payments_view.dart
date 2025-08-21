import 'package:flutter/material.dart';

import 'package:get/get.dart';

class PaymentsView extends GetView {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('PaymentsView'),
        centerTitle: true,
      ),
      body: Center(
        child: Text(
          'PaymentsView is working',
          style: TextStyle(fontSize: 20),
        ),
      ),
    );
  }
}
