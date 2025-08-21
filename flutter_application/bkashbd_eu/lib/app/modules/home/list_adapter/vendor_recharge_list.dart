import 'package:bkashbd_eu/app/constants.dart';
import 'package:bkashbd_eu/app/modules/home/controllers/home_controller.dart';
import 'package:bkashbd_eu/app/modules/home/model/recharge_list_response.dart';
import 'package:bkashbd_eu/libraries/bmprogresshud/progresshud.dart';
import 'package:bkashbd_eu/utils/hex_color.dart';
import 'package:bkashbd_eu/utils/img.dart';
import 'package:bkashbd_eu/utils/my_text.dart';
import 'package:bkashbd_eu/utils/primitive_extensions.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:get/get_state_manager/src/rx_flutter/rx_obx_widget.dart';
import 'package:share/share.dart';

class VendorRechargeList {
  List<List<String?>>? items = <List<String>>[];
  List? mfsList = <MfsList>[];
  List itemsTile = <ItemTile>[];
  Function doReload;

  VendorRechargeList(this.items, this.mfsList, this.doReload) {
    for (var i = 0; i < items!.length; i++) {
      var mfsLogo = "https://via.placeholder.com/150";
      if (items![i].length > 2) {
        for (var ipos = 0; ipos < mfsList!.length; ipos++) {
          if (items![i][1]!.contains(mfsList![ipos].mfsName)) {
            mfsLogo = ("${Constants.BASE_URL}/${mfsList![ipos]!.imagePath}");
            break;
          }
        }
        items![i].add(mfsLogo);
        itemsTile
            .add(ItemTile(index: i, object: items![i], doReload: doReload));
      }
    }
  }

  Widget getView() {
    return Container(
      child: ListView.builder(
        itemBuilder: (BuildContext context, int index) => itemsTile[index],
        itemCount: itemsTile.length,
      ),
    );
  }
}

class ItemTile extends GetView<HomeController> {
  final List<String?> object;
  final int index;
  final Function doReload;

  const ItemTile({
    Key? key,
    required this.index,
    required this.object,
    required this.doReload,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Column(
      children: <Widget>[
        ExpansionTile(
          leading: Container(
              child: Image.network("${object[object.length - 1]}",
                  fit: BoxFit.cover),
              width: 60),
          key: UniqueKey(),
          title: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: <Widget>[
              Container(height: 10.sp),
              Row(
                mainAxisAlignment: MainAxisAlignment.start,
                children: <Widget>[
                  Text("#${object[0]}",
                      style: MyText.subtitle(context)!
                          .copyWith(color: Colors.grey[800])),
                  Spacer(),
                  case2(
                      "${object[5]}",
                      {
                        "Approved": Container(
                            padding: EdgeInsets.all(5),
                            decoration: BoxDecoration(
                                color: Colors.green,
                                borderRadius:
                                    BorderRadius.all(Radius.circular(5.0))),
                            child: Text("${object[5]}",
                                style: MyText.subtitle(context)!
                                    .copyWith(color: Colors.white))),
                        "Rejected": Container(
                            padding: EdgeInsets.all(5),
                            decoration: BoxDecoration(
                                color: Colors.red,
                                borderRadius:
                                    BorderRadius.all(Radius.circular(5.0))),
                            child: Text("${object[5]}",
                                style: MyText.subtitle(context)!
                                    .copyWith(color: Colors.white))),
                        "Requested": Container(
                            padding: EdgeInsets.all(5),
                            decoration: BoxDecoration(
                                color: Colors.grey,
                                borderRadius:
                                    BorderRadius.all(Radius.circular(5.0))),
                            child: Text("${object[5]}",
                                style: MyText.subtitle(context)!
                                    .copyWith(color: Colors.white))),
                        "Progressing": Container(
                            padding: EdgeInsets.all(5),
                            decoration: BoxDecoration(
                                color: Colors.grey,
                                borderRadius:
                                    BorderRadius.all(Radius.circular(5.0))),
                            child: Text("${object[5]}",
                                style: MyText.subtitle(context)!
                                    .copyWith(color: Colors.white)))
                      },
                      Container(
                          padding: EdgeInsets.all(5),
                          decoration: BoxDecoration(
                              color: Colors.red,
                              borderRadius:
                                  BorderRadius.all(Radius.circular(5.0))),
                          child: Text("${object[5]}",
                              style: MyText.subtitle(context)!
                                  .copyWith(color: Colors.white)))),
                ],
              ),
              Container(height: 10.sp),
              Text("Mobile: ${object[2]}\n[${object[3]}]",
                  style:
                      MyText.body1(context)!.copyWith(color: Colors.grey[800])),
              Container(height: 5.sp),
              Text("Note: ${object[10]}",
                  style: MyText.subtitle(context)!.copyWith(
                      color: Colors.grey[800], fontWeight: FontWeight.normal)),
              Container(height: 5.sp),
              Text(
                  "Store: ${object[8]}" +
                      (object[9]!.length > 2
                          ? " [Parent Store: ${object[9]}]"
                          : ""),
                  style: MyText.subtitle(context)!.copyWith(
                      color: Colors.grey[800], fontWeight: FontWeight.normal)),
              Container(height: 5.sp),
              Text("Created on: ${object[6]}",
                  style: MyText.subtitle(context)!.copyWith(
                      color: Colors.grey[800], fontWeight: FontWeight.normal)),
              Container(height: 5.sp),
              Text("Last Updated on: ${object[7]}",
                  style: MyText.subtitle(context)!.copyWith(
                      color: Colors.grey[800], fontWeight: FontWeight.normal))
            ],
          ),
          children: <Widget>[
            Wrap(
              spacing: 5.0,
              children: <Widget>[
                (object[5]! == "Requested"
                    ? GetBuilder<HomeController>(
                        init: HomeController(),
                        builder: (controller) => RaisedButton(
                              color: Colors.yellow, // background
                              textColor: Colors.white, // foreground
                              onPressed: () {
                                ProgressHud.showLoading();
                                controller.lockUnlockRecharge(
                                    object[4]!.split("|")[0], true, () {
                                  doReload();
                                  ProgressHud.dismiss();
                                });
                              },
                              child: Text('Lock',
                                  style: MyText.subtitle(context)!
                                      .copyWith(color: Colors.black)),
                            ))
                    : Container(width: 1.sp)),
                RaisedButton(
                  color: Colors.amber, // background
                  textColor: Colors.black, // foreground
                  onPressed: () {
                    Clipboard.setData(ClipboardData(text: object[2])).then((_) {
                      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                          content: Text("Mobile number copied to clipboard")));
                    });
                  },
                  child: Text('Copy Mobile Number',
                      style: MyText.subtitle(context)!
                          .copyWith(color: Colors.black)),
                ),
                Container(width: 1.sp),
                RaisedButton(
                  color: Colors.blueGrey, // background
                  textColor: Colors.white, // foreground
                  onPressed: () {
                    //bKashBD\nNumber '+row[2]+'\nAmount '+row[3]+'\nType '+row[1]+'\nRefer ID '+row[0]+'\nReseller: '+row[8]+'\nParent: '+row[9]+'
                    Share.share(
                        'bKashBD\nNumber: ' +
                            object[2]! +
                            '\nAmount: ' +
                            object[3]! +
                            '\nType: ' +
                            object[1]! +
                            '\nRefer ID: ' +
                            object[0]! +
                            '\nReseller: ' +
                            object[8]! +
                            '\nParent: ' +
                            object[9]!,
                        subject: 'bKashBD Share');
                  },
                  child: Text('Share',
                      style: MyText.subtitle(context)!
                          .copyWith(color: Colors.white)),
                ),
                (object[5]! == "Progressing"
                    ? GetBuilder<HomeController>(
                        init: HomeController(),
                        builder: (controller) => RaisedButton(
                          color: Colors.green, // background
                          textColor: Colors.white, // foreground
                          onPressed: () {
                            _showAcceptRejectConfirmation(
                                context,
                                "Accept Recharge Request",
                                "Are you sure about Accept?",
                                "Accept",
                                false, () {
                              ProgressHud.showLoading();
                              controller.approveRejectRecharge(
                                  object[4]!.split("|")[0],
                                  "approved",
                                  controller.getNoteVal(), () {
                                doReload();
                                ProgressHud.dismiss();
                              });
                            });
                          },
                          child: Text('Accept',
                              style: MyText.subtitle(context)!
                                  .copyWith(color: Colors.white)),
                        ),
                      )
                    : Container(width: 1.sp)),
                (object[5]! == "Progressing"
                    ? GetBuilder<HomeController>(
                        init: HomeController(),
                        builder: (controller) => RaisedButton(
                              color: Colors.red, // background
                              textColor: Colors.white, // foreground
                              onPressed: () {
                                _showAcceptRejectConfirmation(
                                    context,
                                    "Reject Recharge Request",
                                    "Are you sure about Accept?",
                                    "Reject",
                                    true, () {
                                  ProgressHud.showLoading();
                                  controller.approveRejectRecharge(
                                      object[4]!.split("|")[0],
                                      "rejected",
                                      controller.getNoteVal(), () {
                                    ProgressHud.dismiss();
                                    doReload();
                                  });
                                });
                              },
                              child: Text('Reject',
                                  style: MyText.subtitle(context)!
                                      .copyWith(color: Colors.white)),
                            ))
                    : Container(width: 1.sp)),
                ((object[5]! == "Approved" || object[5]! == "Rejected")
                    ? RaisedButton(
                        color: Colors.blue, // background
                        textColor: Colors.white, // foreground
                        onPressed: () {
                          _showUpdateNote(
                              context,
                              "Update Note",
                              "You can update your old not from here.",
                              "Update",
                              object[10]!, () {
                            ProgressHud.showLoading();
                            controller.updateRechargeNote(
                                object[4]!.split("|")[0],
                                controller.getNoteVal(), () {
                              ProgressHud.dismiss();
                              doReload();
                            });
                          });
                        },
                        child: Text('Update Note',
                            style: MyText.subtitle(context)!
                                .copyWith(color: Colors.white)),
                      )
                    : Container(width: 1.sp))
              ],
            ),
          ],
        ),
        Divider(height: 0)
      ],
    );
  }

  Future<void> _showUpdateNote(
    BuildContext context,
    String title,
    String message,
    String btt1Txt,
    String oldNote,
    Function callback,
  ) async {
    final TextEditingController _textController = TextEditingController();
    _textController.text = oldNote;
    return showDialog<void>(
      context: context,
      barrierDismissible: false, // user must tap button!
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(title),
          content: SingleChildScrollView(
            child: ListBody(
              children: <Widget>[
                Text(message,
                    style: TextStyle(
                        color: Colors.black, fontWeight: FontWeight.normal)),
                Container(height: 20),
                GetBuilder<HomeController>(builder: (_controller) {
                  return TextField(
                    controller: _textController,
                    onChanged: (text) {
                      _controller.setNoteVal(text);
                    },
                    keyboardType: TextInputType.multiline,
                    maxLines: 12,
                    minLines: 7,
                    decoration: InputDecoration(
                      hintText: 'Your Note',
                      hintStyle:
                          MyText.body1(context)!.copyWith(color: Colors.black),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(0),
                        borderSide:
                            BorderSide(color: Colors.amber[500]!, width: 2),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(0),
                        borderSide: BorderSide(color: Colors.black, width: 1),
                      ),
                    ),
                  );
                })
              ],
            ),
          ),
          actions: <Widget>[
            TextButton(
              child: Text("Cancel",
                  style: TextStyle(
                      color: Colors.black,
                      fontWeight: FontWeight.bold,
                      fontSize: 16.sp)),
              onPressed: () {
                Navigator.of(context).pop();
              },
            ),
            GetBuilder<HomeController>(builder: (_controller) {
              return TextButton(
                child: Text(btt1Txt,
                    style: TextStyle(
                        color: (Colors.black),
                        fontWeight: FontWeight.bold,
                        fontSize: 16.sp)),
                onPressed: () {
                  if (_controller.getNoteVal().length > 2) {
                    Navigator.of(context).pop();
                    callback();
                  } else {
                    ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text("Please enter a note.")));
                  }
                },
              );
            })
          ],
        );
      },
    );
  }

  Future<void> _showAcceptRejectConfirmation(
    BuildContext context,
    String title,
    String message,
    String btt1Txt,
    bool reject,
    Function callback,
  ) async {
    return showDialog<void>(
      context: context,
      barrierDismissible: false, // user must tap button!
      builder: (BuildContext context) {
        return AlertDialog(
          title: Text(title),
          content: SingleChildScrollView(
            child: ListBody(
              children: <Widget>[
                Text(message,
                    style: TextStyle(
                        color: Colors.black, fontWeight: FontWeight.normal)),
                Container(height: 20),
                GetBuilder<HomeController>(builder: (_controller) {
                  return TextField(
                    onChanged: (text) {
                      _controller.setNoteVal(text);
                    },
                    keyboardType: TextInputType.multiline,
                    maxLines: 12,
                    minLines: 7,
                    decoration: InputDecoration(
                      hintText:
                          'Put Note (for ${reject ? 'rejection' : 'Acception'})',
                      hintStyle:
                          MyText.body1(context)!.copyWith(color: Colors.black),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(0),
                        borderSide:
                            BorderSide(color: Colors.amber[500]!, width: 2),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(0),
                        borderSide: BorderSide(color: Colors.black, width: 1),
                      ),
                    ),
                  );
                })
              ],
            ),
          ),
          actions: <Widget>[
            TextButton(
              child: Text("Cancel",
                  style: TextStyle(
                      color: Colors.black,
                      fontWeight: FontWeight.bold,
                      fontSize: 16.sp)),
              onPressed: () {
                Navigator.of(context).pop();
              },
            ),
            GetBuilder<HomeController>(builder: (_controller) {
              return TextButton(
                child: Text(btt1Txt,
                    style: TextStyle(
                        color: (reject ? Colors.red : Colors.black),
                        fontWeight: FontWeight.bold,
                        fontSize: 16.sp)),
                onPressed: () {
                  if (_controller.getNoteVal().length > 2) {
                    Navigator.of(context).pop();
                    callback();
                  } else {
                    ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text("Please enter a note.")));
                  }
                },
              );
            })
          ],
        );
      },
    );
  }

  static void showToastClicked(BuildContext context, String action) {
    print(action);
    //MyToast.show(action+" clicked", context);
  }

  TValue case2<TOptionType, TValue>(
    TOptionType selectedOption,
    Map<TOptionType, TValue> branches, [
    TValue? defaultValue = null,
  ]) {
    if (!branches.containsKey(selectedOption)) {
      return defaultValue!;
    }

    return branches[selectedOption]!;
  }
}
