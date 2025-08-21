import 'dart:ffi';

class UserModel {
  String? rightNow;
  String? timestamp;
  bool? success;
  CurrentBalance? currentBalance;
  Data? data;

  UserModel(
      {this.rightNow,
      this.timestamp,
      this.success,
      this.currentBalance,
      this.data});

  UserModel.fromJson(Map<String, dynamic> json) {
    rightNow = json['right_now'];
    timestamp = json['timestamp'];
    success = json['success'];
    currentBalance = json['current_balance'] != null
        ? CurrentBalance.fromJson(json['current_balance'])
        : null;
    data = json['data'] != null ? Data.fromJson(json['data']) : null;
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = Map<String, dynamic>();
    data['right_now'] = this.rightNow;
    data['timestamp'] = this.timestamp;
    data['success'] = this.success;
    if (this.currentBalance != null) {
      data['current_balance'] = this.currentBalance!.toJson();
    }
    if (this.data != null) {
      data['data'] = this.data!.toJson();
    }
    return data;
  }
}

class CurrentBalance {
  String? currency;
  String? amount;
  String? due_euro;

  CurrentBalance({this.currency, this.amount, this.due_euro});

  CurrentBalance.fromJson(Map<String, dynamic> json) {
    currency = json['currency'];
    amount = json['amount'];
    if (json['due_euro'] is Int8) {
      due_euro = json['due_euro'].toString();
    } else {
      due_euro = json['due_euro'];
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = Map<String, dynamic>();
    data['currency'] = this.currency;
    data['amount'] = this.amount;
    data['due_euro'] = this.due_euro;
    return data;
  }
}

class Data {
  String? userId;
  String? storeVendorId;
  String? storeVendorAdmin;
  String? userType;
  String? username;
  List<String>? permissionLists;
  List<String>? allowedMfsIds;
  String? storeName;
  String? storeOwnerName;
  String? storePhoneNumber;
  String? storeAddress;
  String? storeBaseCurrency;
  String? logo;
  List<CurrencyConversionsList>? currencyConversionsList;

  Data(
      {this.userId,
      this.storeVendorId,
      this.storeVendorAdmin,
      this.userType,
      this.username,
      this.permissionLists,
      this.allowedMfsIds,
      this.storeName,
      this.storeOwnerName,
      this.storePhoneNumber,
      this.storeBaseCurrency,
      this.storeAddress,
      this.logo,
      this.currencyConversionsList});

  Data.fromJson(Map<String, dynamic> json) {
    userId = json['user_id'];
    storeVendorId = (json['store_vendor_id'] ?? "").toString();
    storeVendorAdmin = json['store_vendor_admin'];
    userType = json['user_type'];
    username = json['username'];
    permissionLists = json['permission_lists'].cast<String>();
    if (json['allowed_mfs_ids'] != null) {
      allowedMfsIds = <String>[];
      json['allowed_mfs_ids'].forEach((v) {
        allowedMfsIds!.add(v);
      });
    }
    storeName = json['storeName'];
    storeOwnerName = json['storeOwnerName'];
    storePhoneNumber = json['storePhoneNumber'];
    storeAddress = json['storeAddress'];
    storeBaseCurrency = json['storeBaseCurrency'];
    logo = json['logo'];
    if (json['currency_conversions_list'] != null) {
      currencyConversionsList = <CurrencyConversionsList>[];
      json['currency_conversions_list'].forEach((v) {
        currencyConversionsList!.add(CurrencyConversionsList.fromJson(v));
      });
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = Map<String, dynamic>();
    data['user_id'] = this.userId;
    data['store_vendor_id'] = this.storeVendorId;
    data['store_vendor_admin'] = this.storeVendorAdmin;
    data['user_type'] = this.userType;
    data['username'] = this.username;
    data['permission_lists'] = this.permissionLists;
    if (this.allowedMfsIds != null) {
      data['allowed_mfs_ids'] = this.allowedMfsIds!.map((v) => v).toList();
    }
    data['storeName'] = this.storeName;
    data['storeOwnerName'] = this.storeOwnerName;
    data['storePhoneNumber'] = this.storePhoneNumber;
    data['storeAddress'] = this.storeAddress;
    data['storeBaseCurrency'] = this.storeBaseCurrency;
    data['logo'] = this.logo;
    if (this.currencyConversionsList != null) {
      data['currency_conversions_list'] =
          this.currencyConversionsList!.map((v) => v.toJson()).toList();
    }
    return data;
  }
}

class CurrencyConversionsList {
  String? type;
  String? name;
  String? convAmount;

  CurrencyConversionsList({this.type, this.name, this.convAmount});

  CurrencyConversionsList.fromJson(Map<String, dynamic> json) {
    type = json['type'];
    name = json['name'];
    if ((json['conv_amount'] is int) || json['conv_amount'] is double) {
      convAmount = json['conv_amount'].toString();
    } else {
      convAmount = json['conv_amount'];
    }
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = Map<String, dynamic>();
    data['type'] = this.type;
    data['name'] = this.name;
    data['conv_amount'] = this.convAmount;
    return data;
  }
}
