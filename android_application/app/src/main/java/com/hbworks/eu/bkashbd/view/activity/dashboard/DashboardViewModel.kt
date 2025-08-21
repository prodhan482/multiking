package com.hbworks.eu.bkashbd.view.activity.dashboard

import androidx.lifecycle.LifecycleOwner
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.google.firebase.crashlytics.FirebaseCrashlytics
import com.google.gson.Gson
import com.google.gson.annotations.SerializedName
import com.google.gson.reflect.TypeToken
import com.hbworks.eu.bkashbd.data.model.RechargeActivityResponse
import com.hbworks.eu.bkashbd.data.model.StoreInfo
import com.hbworks.eu.bkashbd.repo.bKashBdEuRepository
import com.hbworks.eu.bkashbd.view.activity.login.LoginViewModel
import com.hbworks.eu.bkashbd.view.activity.login.UserProfileResponse
import com.hbworks.eu.bkashbd.view.base.BaseViewModel
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class DashboardViewModel @Inject constructor(
    var baseRepo: bKashBdEuRepository
) : BaseViewModel() {

    var userUpdated = MutableLiveData<Boolean>(false)
    var reloadList = MutableLiveData<Boolean>()
    var updateUserBalance = MutableLiveData<Boolean>()
    var userProfileResponse = MutableLiveData<UserProfileResponse>()

    fun updateUser(hashMap:HashMap<String, String>) {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiUpdateUser(hashMap)
                // use custom response code from data if needed
                when (response.code()) {
                    200 -> {
                        userUpdated.value = true
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("updateUser------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun getUserProfile() {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiGetMyProfile()

                val type = object : TypeToken<UserProfileResponse>() {}.type

                val baseModel = Gson().fromJson<UserProfileResponse>(
                    response.body()?.string(),
                    type
                )

                // use custom response code from data if needed
                when (response.code()) {
                    200 -> {
                        userProfileResponse.value = baseModel
                    }
                    422 -> {
                        errorHandler(arrayListOf("${baseModel.description}"), false)
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                }
            } catch (exception: Exception) {
                println("getUserProfile------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    var rechargeActivityResponse = MutableLiveData<RechargeActivityResponse>()

    fun getRechargeActivity(hashMap:HashMap<String, String>) {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiGetRechargeActivity(hashMap)

                val baseModel = Gson().fromJson<RechargeActivityResponse>(
                    response.body()?.string(),
                    object : TypeToken<RechargeActivityResponse>() {}.type
                )

                when (response.code()) {
                    200 -> {
                        rechargeActivityResponse.value = baseModel
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                FirebaseCrashlytics.getInstance().recordException(exception)
                println("getRechargeActivity------------------> Error: ${exception.message}")
                println("getRechargeActivity------------------> Error: ${exception.localizedMessage}")
                println("getRechargeActivity------------------> Error: ${exception.stackTrace}")
                println("getRechargeActivity------------------> Error: ${exception.suppressed}")
                println("getRechargeActivity------------------> Error: ${exception.cause}")
                exception.printStackTrace()
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun approveRejectRequest(recharge_id:String, hashMap:HashMap<String, String>) {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiApproveRejectRequest(recharge_id, hashMap)

                when (response.code()) {
                    200 -> {
                        reloadList.value = true
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                FirebaseCrashlytics.getInstance().recordException(exception)
                println("approveRejectRequest------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun lockUnlockRequest(recharge_id:String, lock:Boolean = false, hashMap:HashMap<String, String>) {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiLockUnlockRequest(recharge_id, lock, hashMap)

                when (response.code()) {
                    200 -> {
                        reloadList.value = true
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                FirebaseCrashlytics.getInstance().recordException(exception)
                println("lockUnlockRequest------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun reInitRequest(recharge_id:String, hashMap:HashMap<String, String>) {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiReInitRequest(recharge_id, hashMap)

                when (response.code()) {
                    200 -> {
                        reloadList.value = true
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                FirebaseCrashlytics.getInstance().recordException(exception)
                println("reInitRequest------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun updateNote(recharge_id:String, note:String) {
        viewModelScope.launch {
            onLoading(true)

            try {
                var postData = HashMap<String, String>()
                postData["note"] = note
                val response = baseRepo.apiUpdateRequestNote(recharge_id, postData)

                when (response.code()) {
                    200 -> {
                        reloadList.value = true
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("updateNote------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun updateCurrencyConversionRate(new_rate:String) {
        viewModelScope.launch {
            onLoading(true)

            try {
                var postData = HashMap<String, String>()
                postData["conversion_rate"] = new_rate
                val response = baseRepo.apiUpdateCurrencyConversionRate(postData)

                when (response.code()) {
                    200 -> {
                        reloadList.value = true
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("updateCurrencyConversionRate------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun saveServiceCharges(store_id:String, postData:HashMap<String, String>)
    {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiUpdateServiceCharges(store_id, postData)

                when (response.code()) {
                    200 -> {
                        reloadList.value = true
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("saveServiceCharges------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun submitNewRechargeRequest(postData:SubmitRechargeRequest) {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiSubmitNewRechargeRequest(Gson().fromJson(
                    Gson().toJson(postData),
                    object : TypeToken<HashMap<String, String>>() {}.type
                ))

                when (response.code()) {
                    200 -> {
                        updateUserBalance.value = true
                        reloadList.value = true
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("submitNewRechargeRequest------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun updateFCM(fcm_token:String) {
        viewModelScope.launch {
            onLoading(true)

            try {
                var postData = HashMap<String, String>()
                postData["fcm_token"] = fcm_token
                val response = baseRepo.apiUpdateFcm(postData)

                when (response.code()) {
                    200 -> {}
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("FCM------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    data class CommonResponse(
        @SerializedName("rightNow") var rightNow: String? = null,
        @SerializedName("timestamp") var timestamp: String? = null,
        @SerializedName("success") var success: String? = null
    )

    data class SubmitRechargeRequest(
        @SerializedName("transaction_pin") var transaction_pin: String? = null,
        @SerializedName("receive_money") var receive_money: String? = null,
        @SerializedName("sending_currency") var sending_currency: String? = null,
        @SerializedName("visualSendMoney") var visualSendMoney: String = "0",
        @SerializedName("visualCharge") var visualCharge: String = "0",
        @SerializedName("mfs_id") var mfs_id: String? = null,
        var mfs_name: String? = null,
        @SerializedName("mobile_number") var mobile_number: String? = null,
        @SerializedName("recharge_amount") var recharge_amount: String? = null,
        @SerializedName("mfs_type") var mfs_type: String? = null,
        @SerializedName("note") var note: String? = null,
        @SerializedName("send_money") var send_money: String? = null,
        @SerializedName("selected_mfs_package") var selected_mfs_package: String? = null,
        @SerializedName("selected_mfs_package_name") var selected_mfs_package_name: String? = null,
        @SerializedName("send_money_type") var send_money_type: String? = null,
        var charge: String = "0",
        var commission: String = "0",
        var service_charge: String = "0",
        var conversion_rate: String = "1",
        var charge_c: String = "0",
        var commission_c: String = "0"
    )

}
