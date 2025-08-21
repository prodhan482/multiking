import 'dart:convert';

/*class RechargeListResponse {
  RechargeListResponse(
      {
      this.rightNow,
      this.timestamp,
      this.success,
      this.data});

  String? rightNow;
  String? timestamp;
  bool? success;
  List<List<String?>>? data;
  //List<dynamic> savedNumbers;

  factory RechargeListResponse.fromJson(Map<String, dynamic> json) =>
      RechargeListResponse(
        rightNow: json["right_now"],
        timestamp: json["timestamp"],
        success: json["success"],
        data: List<List<String>>.from(
            json["data"].map((x) => List<String>.from(x.map((x) {
                  if (x == null) {
                    return "";
                  }
                  return x.toString();
                })))),
      );

  Map<String, dynamic> toJson() => {
        "right_now": rightNow,
        "timestamp": timestamp,
        "success": success,
        "data": List<List<String?>>.from(
            data!.map((x) => List<List<String?>>.from(x.map((x) {
                  if (x == null) {
                    return "";
                  }
                  return x.toString();
                }))))
      };
}

class MfsList {
  MfsList({
    this.mfsId,
    this.mfsName,
    this.imagePath,
    this.defaultCommission,
    this.defaultCharge,
    this.status,
    this.mfsType,
    this.createdBy,
    this.createdAt,
    this.modifiedAt,
  });

  String? mfsId;
  String? mfsName;
  String? imagePath;
  String? defaultCommission;
  String? defaultCharge;
  String? status;
  String? mfsType;
  String? createdBy;
  String? createdAt;
  String? modifiedAt;

  factory MfsList.fromJson(Map<String, dynamic> json) => MfsList(
        mfsId: json["mfs_id"],
        mfsName: json["mfs_name"],
        imagePath: json["image_path"],
        defaultCommission: json["default_commission"],
        defaultCharge: json["default_charge"],
        status: json["status"],
        mfsType: json["mfs_type"],
        createdBy: json["created_by"],
        createdAt: json["created_at"],
        modifiedAt: json["modified_at"],
      );

  Map<String, dynamic> toJson() => {
        "mfs_id": mfsId,
        "mfs_name": mfsName,
        "image_path": imagePath,
        "default_commission": defaultCommission,
        "default_charge": defaultCharge,
        "status": status,
        "mfs_type": mfsType,
        "created_by": createdBy,
        "created_at": createdAt,
        "modified_at": modifiedAt,
      };
}*/

class RechargeListResponse {
  RechargeListResponse({
    this.rightNow,
    this.timestamp,
    this.success,
    this.data,
    this.euroServiceChargeList1,
    this.euroServiceChargeList2,
    this.storeList,
    this.vendorList,
    this.currentBalance,
    this.loanBalance,
    this.conversionRate,
    this.mfsList,
    this.mfsPackageList,
    this.commissionPercent,
    this.storeCommissionPercent,
    this.storeMfsSlab,
    //this.savedNumbers,
  });

  String? rightNow;
  String? timestamp;
  bool? success;
  List<List<String?>>? data;
  List<EuroServiceChargeList>? euroServiceChargeList1;
  List<EuroServiceChargeList>? euroServiceChargeList2;
  List<StoreList>? storeList;
  List<StoreList>? vendorList;
  String? currentBalance;
  String? loanBalance;
  String? conversionRate;
  List<MfsList>? mfsList;
  List<MfsPackageList>? mfsPackageList;
  List<CommissionPercent>? commissionPercent;
  List<CommissionPercent>? storeCommissionPercent;
  String? storeMfsSlab;
  //List<dynamic> savedNumbers;

  factory RechargeListResponse.fromJson(Map<String, dynamic> json) =>
      RechargeListResponse(
        rightNow: json["right_now"],
        timestamp: json["timestamp"],
        success: json["success"],
        data: List<List<String>>.from(
            json["data"].map((x) => List<String>.from(x.map((x) {
                  if (x == null) {
                    return "";
                  }
                  return x.toString();
                })))),
        euroServiceChargeList1: List<EuroServiceChargeList>.from(
            json["euroServiceChargeList_1"]
                .map((x) => EuroServiceChargeList.fromJson(x))),
        euroServiceChargeList2: List<EuroServiceChargeList>.from(
            json["euroServiceChargeList_2"]
                .map((x) => EuroServiceChargeList.fromJson(x))),

        storeList: List<StoreList>.from(
            json["storeList"].map((x) => StoreList.fromJson(x))),

        vendorList: List<StoreList>.from(
            json["vendorList"].map((x) => StoreList.fromJson(x))),
        currentBalance: json["current_balance"],
        loanBalance: json["loan_balance"],
        conversionRate: json["conversion_rate"],
        mfsList: List<MfsList>.from(
            json["mfs_list"].map((x) => MfsList.fromJson(x))),
        mfsPackageList: List<MfsPackageList>.from(
            json["mfs_package_list"].map((x) => MfsPackageList.fromJson(x))),
        commissionPercent: List<CommissionPercent>.from(
            json["commission_percent"]
                .map((x) => CommissionPercent.fromJson(x))),
        //storeCommissionPercent:
        //    CommissionPercent.fromJson(json["store_commission_percent"]),
        //storeMfsSlab: json["store_mfs_slab"],
        //savedNumbers: List<dynamic>.from(json["saved_numbers"].map((x) => x)),
      );

  Map<String, dynamic> toJson() => {
        "right_now": rightNow,
        "timestamp": timestamp,
        "success": success,
        "data": List<List<String?>>.from(
            data!.map((x) => List<List<String?>>.from(x.map((x) {
                  if (x == null) {
                    return "";
                  }
                  return x.toString();
                })))),
        "euroServiceChargeList_1": List<EuroServiceChargeList>.from(
            euroServiceChargeList1!.map((x) => x.toJson())),
        "euroServiceChargeList_2": List<EuroServiceChargeList>.from(
            euroServiceChargeList2!.map((x) => x.toJson())),
        "storeList": List<StoreList>.from(storeList!.map((x) => x.toJson())),
        "vendorList": List<StoreList>.from(vendorList!.map((x) => x.toJson())),
        "current_balance": currentBalance,
        "loan_balance": loanBalance,
        "conversion_rate": conversionRate,
        //"mfs_list": List<MfsList>.from(mfsList!.map((x) => x.toJson())),
        "mfs_package_list":
            List<MfsPackageList>.from(mfsPackageList!.map((x) => x)),
        "commission_percent":
            List<CommissionPercent>.from(commissionPercent!.map((x) => x)),
        "store_commission_percent":
            List<CommissionPercent>.from(storeCommissionPercent!.map((x) => x)),
        "store_mfs_slab": storeMfsSlab,
        //"saved_numbers": List<dynamic>.from(savedNumbers.map((x) => x)),
      };
}

class CommissionPercent {
  CommissionPercent({
    this.name,
    this.id,
    this.commission,
    this.charge,
  });

  String? name;
  String? id;
  String? commission;
  String? charge;

  factory CommissionPercent.fromJson(Map<String, dynamic> json) =>
      CommissionPercent(
        name: json["name"],
        id: json["id"],
        commission: json["commission"],
        charge: json["charge"],
      );

  Map<String, dynamic> toJson() => {
        "name": name,
        "id": id,
        "commission": commission,
        "charge": charge,
      };
}

class EuroServiceChargeList {
  EuroServiceChargeList({
    this.from,
    this.to,
    this.charge,
  });

  String? from;
  String? to;
  String? charge;

  factory EuroServiceChargeList.fromJson(Map<String, dynamic> json) =>
      EuroServiceChargeList(
        from: json["from"],
        to: json["to"],
        charge: json["charge"],
      );

  Map<String, dynamic> toJson() => {
        "from": from,
        "to": to,
        "charge": charge,
      };
}

class MfsList {
  MfsList({
    this.mfsId,
    this.mfsName,
    this.imagePath,
    this.defaultCommission,
    this.defaultCharge,
    this.status,
    this.mfsType,
    this.createdBy,
    this.createdAt,
    this.modifiedAt,
  });

  String? mfsId;
  String? mfsName;
  String? imagePath;
  String? defaultCommission;
  String? defaultCharge;
  String? status;
  String? mfsType;
  String? createdBy;
  String? createdAt;
  String? modifiedAt;

  factory MfsList.fromJson(Map<String, dynamic> json) => MfsList(
        mfsId: json["mfs_id"],
        mfsName: json["mfs_name"],
        imagePath: json["image_path"],
        defaultCommission: json["default_commission"],
        defaultCharge: json["default_charge"],
        status: json["status"],
        mfsType: json["mfs_type"],
        createdBy: json["created_by"],
        createdAt: json["created_at"],
        modifiedAt: json["modified_at"],
      );

  Map<String, dynamic> toJson() => {
        "mfs_id": mfsId,
        "mfs_name": mfsName,
        "image_path": imagePath,
        "default_commission": defaultCommission,
        "default_charge": defaultCharge,
        "status": status,
        "mfs_type": mfsType,
        "created_by": createdBy,
        "created_at": createdAt,
        "modified_at": modifiedAt,
      };
}

class StoreList {
  StoreList({
    this.id,
    this.name,
  });

  String? id;
  String? name;

  factory StoreList.fromJson(Map<String, dynamic> json) => StoreList(
        id: json["id"],
        name: json["name"],
      );

  Map<String, dynamic> toJson() => {
        "id": id,
        "name": name,
      };
}

class MfsPackageList {
  MfsPackageList({
    this.rowId,
    this.storeId,
    this.createdBy,
    this.createdAt,
    this.mfsId,
    this.discount,
    this.charge,
    this.startSlab,
    this.endSlab,
    this.packageName,
    this.amount,
    this.note,
  });

  String? rowId;
  String? storeId;
  String? createdBy;
  String? createdAt;
  String? mfsId;
  String? discount;
  String? charge;
  String? startSlab;
  String? endSlab;
  String? packageName;
  String? amount;
  String? note;

  factory MfsPackageList.fromJson(Map<String, dynamic> json) => MfsPackageList(
        rowId: json["row_id"],
        storeId: json["store_id"],
        createdBy: json["created_by"],
        createdAt: json["created_at"],
        mfsId: json["mfs_id"],
        discount: json["discount"],
        charge: json["charge"],
        startSlab: json["start_slab"],
        endSlab: json["end_slab"],
        packageName: json["package_name"],
        amount: json["amount"],
        note: json["note"],
      );

  Map<String, dynamic> toJson() => {
        "row_id": rowId,
        "store_id": storeId,
        "created_by": createdBy,
        "created_at": createdAt,
        "mfs_id": mfsId,
        "discount": discount,
        "charge": charge,
        "start_slab": startSlab,
        "end_slab": endSlab,
        "package_name": packageName,
        "amount": amount,
        "note": note,
      };
}
