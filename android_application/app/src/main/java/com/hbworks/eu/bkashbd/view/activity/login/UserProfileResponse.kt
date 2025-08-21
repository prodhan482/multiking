package com.hbworks.eu.bkashbd.view.activity.login

import com.google.gson.annotations.SerializedName

data class UserProfileResponse(
    @SerializedName("description"       ) var description       : String?         = null,
    @SerializedName("right_now"       ) var rightNow       : String?         = null,
    @SerializedName("timestamp"       ) var timestamp      : String?         = null,
    @SerializedName("success"         ) var success        : Boolean?        = null,
    @SerializedName("current_balance" ) var currentBalance : UserCurrentBalance? = UserCurrentBalance(),
    @SerializedName("data"            ) var data           : UserProfileData?           = UserProfileData()
)

data class UserCurrentBalance (

    @SerializedName("currency" ) var currency : String? = null,
    @SerializedName("amount"   ) var amount   : String? = null,
    @SerializedName("due_euro" ) var dueEuro  : String? = null

)

data class CurrencyConversionsList (

    @SerializedName("type"        ) var type       : String? = null,
    @SerializedName("name"        ) var name       : String? = null,
    @SerializedName("conv_amount" ) var convAmount : String?    = null

)

data class UserProfileData (

    @SerializedName("user_id"                   ) var userId                  : String?                            = null,
    @SerializedName("store_vendor_id"           ) var storeVendorId           : String?                            = null,
    @SerializedName("store_vendor_admin"        ) var storeVendorAdmin        : String?                            = null,
    @SerializedName("user_type"                 ) var userType                : String?                            = null,
    @SerializedName("username"                  ) var username                : String?                            = null,
    @SerializedName("permission_lists"          ) var permissionLists         : ArrayList<String>                  = arrayListOf(),
    @SerializedName("allowed_mfs_ids"           ) var allowedMfsIds           : ArrayList<String>                  = arrayListOf(),
    @SerializedName("storeName"                 ) var storeName               : String?                            = null,
    @SerializedName("storeOwnerName"            ) var storeOwnerName          : String?                            = null,
    @SerializedName("storePhoneNumber"          ) var storePhoneNumber        : String?                            = null,
    @SerializedName("storeBaseCurrency"         ) var storeBaseCurrency       : String?                            = null,
    @SerializedName("storeAddress"              ) var storeAddress            : String?                            = null,
    @SerializedName("logo"                      ) var logo                    : String?                            = null,
    @SerializedName("currency_conversions_list" ) var currencyConversionsList : ArrayList<CurrencyConversionsList> = arrayListOf(),
    @SerializedName("parent_store_id"           ) var parentStoreId           : String?                            = null,
    @SerializedName("notice_meta"           ) var notice_meta           : NoticeMeta?                            = null

)
data class NoticeMeta(
    @SerializedName("hotline_number") var hotline_number: String? = null,
    @SerializedName("site_notice") var site_notice: String? = null,
)
