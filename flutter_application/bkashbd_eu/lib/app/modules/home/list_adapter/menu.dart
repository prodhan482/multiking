import 'package:bkashbd_eu/utils/hex_color.dart';
import 'package:bkashbd_eu/utils/img.dart';
import 'package:bkashbd_eu/utils/my_text.dart';
import 'package:flutter/material.dart';

class Menu {
  List items = <MenuItem>[];
  List itemsTile = <Material>[];
  late Function onItemClick;

  Menu(this.items, BuildContext context, onItemClick) {
    this.onItemClick = onItemClick;

    for (var i = 0; i < items.length; i++) {
      //itemsTile
      //    .add(ItemTile(index: i, object: items[i], onClick: this.onItemClick));

      if (items[i].treatAsDivider == 1) {
        itemsTile.add(Material(
          color: Colors.transparent,
          child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: <Widget>[
                Container(height: 15),
                Container(
                    child: Divider(height: 0, color: Colors.grey),
                    margin: EdgeInsets.symmetric(horizontal: 10)),
                Container(
                  padding: EdgeInsets.only(left: 10, top: 20, bottom: 15),
                  child: Text(items[i].menu_name,
                      style:
                          MyText.body2(context)!.copyWith(color: Colors.white)),
                ),
              ]),
        ));
      } else {
        itemsTile.add(Material(
            color: Colors.transparent,
            child: InkWell(
              onTap: () {
                this.onItemClick(items[i].menu_name);
                //print("ok --------------> ");
              },
              child: ListTile(
                leading: Icon(items[i].icon, color: Colors.white),
                title: Text(items[i].menu_name,
                    style:
                        MyText.subhead(context)!.copyWith(color: Colors.white)),
              ),
            )));
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

// ignore: must_be_immutable
class ItemTile extends StatelessWidget {
  final String object;
  final int index;
  final Function onClick;

  const ItemTile({
    Key? key,
    required this.index,
    required this.object,
    required this.onClick,
  }) : super(key: key);

  void onItemClick(String obj) {
    onClick(index, obj);
  }

  @override
  Widget build(BuildContext context) {
    if (false) {
      // for section view
      return Padding(
        padding: EdgeInsets.symmetric(vertical: 15, horizontal: 18),
        child: Text("object.name!",
            style: MyText.subhead(context)!
                .copyWith(color: Colors.grey, fontWeight: FontWeight.w500)),
      );
    } else {
      // for people vew
      return InkWell(
        onTap: () {
          //onItemClick(object);
        },
        child: Padding(
          padding: EdgeInsets.symmetric(vertical: 5),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.max,
            children: <Widget>[
              Container(width: 18),
              Container(
                  child: CircleAvatar(
                    backgroundImage: AssetImage(Img.get('white_bg_logo.png')),
                  ),
                  width: 50,
                  height: 50),
              Container(width: 18),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: <Widget>[
                    Text(
                      "object.name!",
                      style: MyText.medium(context).copyWith(
                          color: Colors.grey[800],
                          fontWeight: FontWeight.normal),
                    ),
                    Container(height: 5),
                    Text(
                      "MyStrings.middle_lorem_ipsum",
                      maxLines: 2,
                      style:
                          MyText.subhead(context)!.copyWith(color: Colors.grey),
                    ),
                    Container(height: 15),
                    Divider(color: Colors.grey[300], height: 0),
                  ],
                ),
              ),
            ],
          ),
        ),
      );
    }
  }
}

class MenuItem {
  IconData? icon = Icons.reorder;
  String? menu_name;
  int? treatAsDivider = 0;

  MenuItem({this.icon, this.menu_name, this.treatAsDivider});
}
