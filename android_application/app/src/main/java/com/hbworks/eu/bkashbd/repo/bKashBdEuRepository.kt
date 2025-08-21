package com.hbworks.eu.bkashbd.repo

import com.hbworks.eu.bkashbd.data.DataManager
import com.hbworks.eu.bkashbd.data.network.ApiHelper
import okhttp3.ResponseBody
import retrofit2.Response
import javax.inject.Inject


class bKashBdEuRepository @Inject constructor(val dataManager: DataManager) {

    suspend fun apiVersionCheck(version: String): Response<ResponseBody> {
        val hashMap = HashMap<String, String>()
        hashMap["version"] = version
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_GET,
            "app_welcome",
            hashMap
        )
    }

    suspend fun apiDoLogin(user_name:String, user_password:String, fcm_token:String): Response<ResponseBody> {
        val hashMap = HashMap<String, String>()
        hashMap["user_name"] = user_name
        hashMap["user_password"] = user_password
        hashMap["fcm_token"] = fcm_token
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_JSON_POST,
            "login",
            hashMap
        )
    }

    suspend fun apiUpdateUser(hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_PATCH,
            "me/update",
            hashMap
        )
    }

    suspend fun apiGetRechargeActivity(hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_POST,
            "recharge/activity",
            hashMap
        )
    }

    suspend fun apiApproveRejectRequest(recharge_id:String, hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_JSON_POST,
            "recharge/approve_reject/${recharge_id}",
            hashMap
        )
    }

    suspend fun apiLockUnlockRequest(recharge_id:String, lock:Boolean, hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_JSON_POST,
            "recharge/${if(lock) "lock" else "unlock"}/${recharge_id}",
            hashMap
        )
    }

    suspend fun apiReInitRequest(recharge_id:String, hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_JSON_POST,
            "recharge/reinit/${recharge_id}",
            hashMap
        )
    }

    suspend fun apiUpdateRequestNote(recharge_id:String, hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_JSON_POST,
            "recharge/update_note/${recharge_id}",
            hashMap
        )
    }

    suspend fun apiUpdateCurrencyConversionRate(hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_JSON_POST,
            "stores/save_store_conversion_rate",
            hashMap
        )
    }

    suspend fun apiSubmitNewRechargeRequest(hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_JSON_POST,
            "recharge/create",
            hashMap
        )
    }

    suspend fun apiUpdateServiceCharges(store_id:String, hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_JSON_POST,
            "store/${store_id}",
            hashMap
        )
    }

    suspend fun apiGetMyProfile(): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_GET,
            "me",
            HashMap<String, String>()
        )
    }

    suspend fun apiValidateTransactionPIN(pin:String): Response<ResponseBody> {
        val hashMap = HashMap<String, String>()
        hashMap["transaction_pin"] = pin
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_JSON_POST,
            "validate_transaction_pin",
            hashMap
        )
    }

    suspend fun apiUpdateFcm(hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_JSON_POST,
            "update_fcm",
            hashMap
        )
    }

    suspend fun apiMfsWiseRechargeSummeryReport(hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_POST,
            "report/recharge_by_mfs",
            hashMap
        )
    }

    suspend fun apiStorePaymentReport(hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_POST,
            "report/adjustment_history/store",
            hashMap
        )
    }

    suspend fun apiLoadAddResellerConf(): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_GET,
            "stores/load_conf",
            HashMap<String, String>()
        )
    }

    suspend fun apiListAllReseller(hashMap:HashMap<String, String>): Response<ResponseBody> {

        hashMap.put("start", "0")
        hashMap.put("length", "50")
        hashMap.put("simcard_view", "0")
        hashMap.put("draw", "${System.currentTimeMillis()}")

        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_POST,
            "store",
            hashMap
        )
    }

    suspend fun apiAddBalanceToReseller(hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_PUT,
            "store/${hashMap["store_id"]}",
            hashMap
        )
    }

    suspend fun apiPaymentReceivedFromReseller(hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_PATCH,
            "store/${hashMap["store_id"]}",
            hashMap
        )
    }

    suspend fun apiPaymentReturnFromReseller(hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_OPTIONS,
            "store/${hashMap["store_id"]}",
            hashMap
        )
    }

    suspend fun apiUpdateReseller(hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_JSON_POST,
            "store/${hashMap["store_id"]}",
            hashMap
        )
    }


    suspend fun apiCreateReseller(hashMap:HashMap<String, String>): Response<ResponseBody> {
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_POST,
            "store_c",
            hashMap
        )
    }
}
