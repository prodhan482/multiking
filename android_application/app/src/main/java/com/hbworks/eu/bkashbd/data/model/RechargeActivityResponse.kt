package com.hbworks.eu.bkashbd.data.model

import com.google.gson.annotations.SerializedName

data class RechargeActivityResponse (
    @SerializedName("right_now"                ) var rightNow               : String?                           = null,
    @SerializedName("timestamp"                ) var timestamp              : String?                           = null,
    @SerializedName("success"                  ) var success                : Boolean?                          = null,
    @SerializedName("data"                     ) var data                   : ArrayList<ArrayList<String>>      = arrayListOf(),
    @SerializedName("euroServiceChargeList_2"  ) var euroServiceChargeListMobile : ArrayList<EuroServiceChargeList> = arrayListOf(),
    @SerializedName("euroServiceChargeList_1"  ) var euroServiceChargeListMfs : ArrayList<EuroServiceChargeList> = arrayListOf(),
    @SerializedName("storeList"                ) var storeList              : ArrayList<StoreInfo>                 = arrayListOf(),
    @SerializedName("vendorList"               ) var vendorList             : ArrayList<VendorInfo>                 = arrayListOf(),
    @SerializedName("current_balance"          ) var currentBalance         : String?                           = null,
    @SerializedName("loan_balance"             ) var loanBalance            : String?                           = null,
    @SerializedName("conversion_rate"          ) var conversionRate         : String?                           = null,
    @SerializedName("mfs_list"                 ) var mfsList                : ArrayList<MfsList>                = arrayListOf(),
    @SerializedName("mfs_package_list"         ) var mfsPackageList         : ArrayList<MfsPackageList>         = arrayListOf(),
    @SerializedName("commission_percent"       ) var commissionPercent      : ArrayList<CommissionPercent>?                = arrayListOf(),
    @SerializedName("store_commission_percent" ) var storeCommissionPercent : ArrayList<StoreCommissionPercent>?           = arrayListOf(),
    @SerializedName("store_mfs_slab"           ) var storeMfsSlab           : ArrayList<StoreCommissionPercent>?                           = null,
    @SerializedName("saved_numbers"            ) var savedNumbers           : ArrayList<String>                 = arrayListOf()
)

data class EuroServiceChargeList (
    @SerializedName("from") var from: String? = null,
    @SerializedName("to") var to: String? = null,
    @SerializedName("charge") var charge : String? = null
)

data class MfsList (
    @SerializedName("mfs_id"             ) var mfsId             : String? = null,
    @SerializedName("mfs_name"           ) var mfsName           : String? = null,
    @SerializedName("image_path"         ) var imagePath         : String? = null,
    @SerializedName("default_commission" ) var defaultCommission : String? = null,
    @SerializedName("default_charge"     ) var defaultCharge     : String? = null,
    @SerializedName("status"             ) var status            : String? = null,
    @SerializedName("mfs_type"           ) var mfsType           : String? = null,
    @SerializedName("created_by"         ) var createdBy         : String? = null,
    @SerializedName("created_at"         ) var createdAt         : String? = null,
    @SerializedName("modified_at"        ) var modifiedAt        : String? = null
)

data class MfsPackageList (
    @SerializedName("row_id"       ) var rowId       : String? = null,
    @SerializedName("store_id"     ) var storeId     : String? = null,
    @SerializedName("created_by"   ) var createdBy   : String? = null,
    @SerializedName("created_at"   ) var createdAt   : String? = null,
    @SerializedName("mfs_id"       ) var mfsId       : String? = null,
    @SerializedName("discount"     ) var discount    : String? = null,
    @SerializedName("charge"       ) var charge      : String? = null,
    @SerializedName("start_slab"   ) var startSlab   : String? = null,
    @SerializedName("end_slab"     ) var endSlab     : String? = null,
    @SerializedName("package_name" ) var packageName : String? = null,
    @SerializedName("amount"       ) var amount      : String? = null,
    @SerializedName("note"         ) var note        : String? = null,
    var mfsDetails       : MfsList? = null,
)

data class CommissionPercent(
    @SerializedName("row_id") var rowId: String? = null,

    @SerializedName("name") var name: String? = null,
    @SerializedName("id") var id: String? = null,
    @SerializedName("commission") var commission: String? = null,
    @SerializedName("charge") var charge: String? = null,
    @SerializedName("value") var value: String? = null,
)

data class StoreCommissionPercent(
    @SerializedName("row_id") var rowId: String? = null,

    @SerializedName("name") var name: String? = null,
    @SerializedName("id") var id: String? = null,
    @SerializedName("commission") var commission: String? = null,
    @SerializedName("charge") var charge: String? = null,
    @SerializedName("value") var value: String? = null,
)

data class StoreInfo (
    @SerializedName("id") var id : String? = null,
    @SerializedName("name") var name : String? = null,
    @SerializedName("store_id"   ) var storeId   : String? = null,
    @SerializedName("store_name" ) var storeName : String? = null
)

data class VendorInfo (
    @SerializedName("id") var id : String? = null,
    @SerializedName("name") var name : String? = null
)
