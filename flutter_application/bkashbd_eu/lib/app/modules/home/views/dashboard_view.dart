import 'package:bkashbd_eu/app/modules/home/controllers/home_controller.dart';
import 'package:bkashbd_eu/utils/hex_color.dart';
import 'package:bkashbd_eu/utils/my_text.dart';
import 'package:bkashbd_eu/utils/primitive_extensions.dart';
import 'package:flutter/material.dart';

import 'package:get/get.dart';

class DashboardView extends GetView<HomeController> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SingleChildScrollView(
        padding: EdgeInsets.symmetric(vertical: 10, horizontal: 10),
        child: Column(
          children: <Widget>[
            Card(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(2)),
              color: Colors.white,
              elevation: 2,
              clipBehavior: Clip.antiAliasWithSaveLayer,
              child: Container(
                padding: EdgeInsets.symmetric(vertical: 15, horizontal: 15),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: <Widget>[
                    Expanded(
                        child: Obx(() => Text(
                              controller.dashboardNotice.value,
                              style: MyText.body2(context)!
                                  .copyWith(color: HexColor("#000000")),
                              textAlign: TextAlign.center,
                            )))
                  ],
                ),
              ),
            ),
            Container(
              child: GetBuilder<HomeController>(
                builder: (_c) => GridView.count(
                    crossAxisCount: 2,
                    childAspectRatio: 2.0,
                    shrinkWrap: true,
                    children: List.generate(
                        (controller.dashboardHighlightedBlock != null
                            ? controller.dashboardHighlightedBlock!.length
                            : 0), (index) {
                      return Card(
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(2)),
                        color: Colors.white,
                        elevation: 2,
                        clipBehavior: Clip.antiAliasWithSaveLayer,
                        child: Container(
                          height: 50.sp,
                          padding: EdgeInsets.symmetric(
                              vertical: 15, horizontal: 15),
                          child: Row(
                            children: <Widget>[
                              CircleAvatar(
                                backgroundColor: Colors.lightGreen[500],
                                child: Icon(
                                  Icons.person,
                                  color: Colors.white,
                                ),
                              ),
                              Container(width: 10),
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: <Widget>[
                                  Text(
                                    controller.dashboardHighlightedBlock![index]
                                        .value!,
                                    style: MyText.subhead(context)!.copyWith(
                                        color: HexColor("#919191"),
                                        fontWeight: FontWeight.bold),
                                    textAlign: TextAlign.center,
                                  ),
                                  Container(height: 5),
                                  Text(
                                    controller.dashboardHighlightedBlock![index]
                                        .title!,
                                    style: MyText.caption(context)!.copyWith(
                                        color: HexColor("#919191"),
                                        fontWeight: FontWeight.bold),
                                    textAlign: TextAlign.center,
                                  )
                                ],
                              )
                            ],
                          ),
                        ),
                      );
                    })),
              ),
            ),
            Container(height: 5),
            Card(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(2)),
              color: Colors.white,
              elevation: 2,
              clipBehavior: Clip.antiAliasWithSaveLayer,
              child: Container(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    Container(
                      padding:
                          EdgeInsets.symmetric(vertical: 20, horizontal: 20),
                      child: Row(
                        children: <Widget>[
                          Obx(() => Text(
                                controller.tableTitle.value,
                                style: MyText.subhead(context)!.copyWith(
                                    color: HexColor("#919191"),
                                    fontWeight: FontWeight.bold),
                                textAlign: TextAlign.center,
                              )),
                          Spacer(),
                          //Icon(Icons.add, color: HexColor("#919191"))
                        ],
                      ),
                    ),
                    Divider(height: 0),
                    Container(height: 15),
                    Container(
                      child: GetBuilder<HomeController>(
                          builder: (_c) => ListView.builder(
                                physics: NeverScrollableScrollPhysics(),
                                itemCount: (_c.tableRows != null
                                    ? _c.tableRows!.length
                                    : 0),
                                shrinkWrap: true,
                                itemBuilder: (context, position) {
                                  return Container(
                                    padding: EdgeInsets.symmetric(
                                        vertical: 10, horizontal: 20),
                                    child: Row(
                                      children: <Widget>[
                                        Text(
                                          _c.tableRows![position][0],
                                          style: MyText.body2(context)!
                                              .copyWith(
                                                  color: HexColor("#919191")),
                                          textAlign: TextAlign.center,
                                        ),
                                        Spacer(),
                                        Text(
                                          _c.tableRows![position][1],
                                          style: MyText.body2(context)!
                                              .copyWith(
                                                  color: HexColor("#919191")),
                                          textAlign: TextAlign.center,
                                        ),
                                      ],
                                    ),
                                  );
                                },
                              )),
                    )
                  ],
                ),
              ),
            ),
            Container(height: 5),
            Card(
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(2)),
              color: Colors.white,
              elevation: 2,
              clipBehavior: Clip.antiAliasWithSaveLayer,
              child: Container(
                padding: EdgeInsets.symmetric(vertical: 15, horizontal: 15),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: <Widget>[
                    Container(width: 10),
                  ],
                ),
              ),
            )
          ],
        ),
      ),
    );
  }
}
