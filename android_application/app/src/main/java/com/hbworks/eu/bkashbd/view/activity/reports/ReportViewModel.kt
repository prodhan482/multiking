package com.hbworks.eu.bkashbd.view.activity.reports

import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.google.gson.Gson
import com.google.gson.annotations.SerializedName
import com.google.gson.reflect.TypeToken
import com.hbworks.eu.bkashbd.repo.bKashBdEuRepository
import com.hbworks.eu.bkashbd.view.base.BaseViewModel
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class ReportViewModel @Inject constructor(var baseRepo: bKashBdEuRepository) : BaseViewModel()
{
    var reloadList = MutableLiveData<Boolean>()

    var mfsSummeryReportResponse = MutableLiveData<MfsSummeryReportResponse>()
    var resellerListResponse = MutableLiveData<ResellersList.ResellerListResponse>()
    var paymentReportResponse = MutableLiveData<PaymentReportResponse>()

    var newResellerInfo = AddResellerRequest()
    var resellerConf= MutableLiveData<ResellerConfigResponse>()
    var resellerAddInHashMap = LinkedHashMap<String, String>()
    var resellerCreatedSuccessfully = MutableLiveData<Boolean>()

    fun getPaymentReport(hashMap:HashMap<String, String>) {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiStorePaymentReport(hashMap)

                val baseModel = Gson().fromJson<PaymentReportResponse>(
                    response.body()?.string(),
                    object : TypeToken<PaymentReportResponse>() {}.type
                )

                when (response.code()) {
                    200 -> {
                        paymentReportResponse.value = baseModel
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    408, 410 -> {
                        errorHandler(arrayListOf("Server Responds too slow. Please try again later."), false)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("*------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun getMfsWiseRechargeSummeryReport(hashMap:HashMap<String, String>) {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiMfsWiseRechargeSummeryReport(hashMap)

                val baseModel = Gson().fromJson<MfsSummeryReportResponse>(
                    response.body()?.string(),
                    object : TypeToken<MfsSummeryReportResponse>() {}.type
                )

                when (response.code()) {
                    200 -> {
                        mfsSummeryReportResponse.value = baseModel
                    }
                    408, 410 -> {
                        errorHandler(arrayListOf("Server Responds too slow. Please try again later."), false)
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("*------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun getAllResellerList(searchTags:HashMap<String, String>) {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiListAllReseller(searchTags)

                val baseModel = Gson().fromJson<ResellersList.ResellerListResponse>(
                    response.body()?.string(),
                    object : TypeToken<ResellersList.ResellerListResponse>() {}.type
                )

                when (response.code()) {
                    200 -> {
                        resellerListResponse.value = baseModel
                    }
                    408, 410 -> {
                        errorHandler(arrayListOf("Server Responds too slow. Please try again later."), false)
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("getAllResellerList------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun addResellerBalance(hashMap:HashMap<String, String>) {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiAddBalanceToReseller(hashMap)
                when (response.code()) {
                    200 -> {
                        reloadList.value = true
                    }
                    408, 410 -> {
                        errorHandler(arrayListOf("Server Responds too slow. Please try again later."), false)
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("addResellerBalance------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun updateResellerStatus(hashMap:HashMap<String, String>) {
        viewModelScope.launch {
            onLoading(true)
            try {
                val response = baseRepo.apiUpdateReseller(hashMap)
                when (response.code()) {
                    200 -> {
                        reloadList.value = true
                    }
                    408, 410 -> {
                        errorHandler(arrayListOf("Server Responds too slow. Please try again later."), false)
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("updateResellerStatus------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun addResellerPaymentReceived(hashMap:HashMap<String, String>) {
        viewModelScope.launch {
            onLoading(true)
            try {
                val response = baseRepo.apiPaymentReceivedFromReseller(hashMap)
                when (response.code()) {
                    200 -> {
                        reloadList.value = true
                    }
                    408, 410 -> {
                        errorHandler(arrayListOf("Server Responds too slow. Please try again later."), false)
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("updateResellerStatus------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun addResellerPaymentReturned(hashMap:HashMap<String, String>) {
        viewModelScope.launch {
            onLoading(true)
            try {
                val response = baseRepo.apiPaymentReturnFromReseller(hashMap)
                when (response.code()) {
                    200 -> {
                        reloadList.value = true
                    }
                    408, 410 -> {
                        errorHandler(arrayListOf("Server Responds too slow. Please try again later."), false)
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("updateResellerStatus------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun loadAddResellerConfig() {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiLoadAddResellerConf()

                val baseModel = Gson().fromJson<ResellerConfigResponse>(
                    response.body()?.string(),
                    object : TypeToken<ResellerConfigResponse>() {}.type
                )

                when (response.code()) {
                    200 -> {
                        resellerConf.value = baseModel
                    }
                    408, 410 -> {
                        errorHandler(arrayListOf("Server Responds too slow. Please try again later."), false)
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("loadAddResellerConfig------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun createNewReseller() {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiCreateReseller(Gson().fromJson(
                    Gson().toJson(newResellerInfo),
                    object : TypeToken<HashMap<String, String>>() {}.type
                ))

                when (response.code()) {
                    200 -> {
                        var baseModel1 = Gson().fromJson<CommonResponse>(
                            response.body()?.string(),
                            object : TypeToken<CommonResponse>() {}.type
                        )
                        resellerCreatedSuccessfully.postValue(true)
                    }
                    406 -> {
                        var baseModel2 = Gson().fromJson<CommonResponse>(
                            response.errorBody()?.string(),
                            object : TypeToken<CommonResponse>() {}.type
                        )
                        errorHandler(baseModel2.message!!, false)
                    }
                    408, 410 -> {
                        errorHandler(arrayListOf("Server Responds too slow. Please try again later."), false)
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    else -> {
                        errorHandler(arrayListOf("1"), true)
                    }
                }
            } catch (exception: Exception) {
                println("createNewReseller------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    data class ResellerConfigResponse(
        @SerializedName("rightNow") var rightNow: String? = null,
        @SerializedName("timestamp") var timestamp: String? = null,
        @SerializedName("success") var success: String? = null,
        @SerializedName("mfs_list") var mfs_list: List<ResellerConfigMfsListResponse>
    )

    data class ResellerConfigMfsListResponse(
        @SerializedName("name") var name: String? = null,
        @SerializedName("id") var id: String? = null,
        @SerializedName("commission") var commission: String? = null,
        @SerializedName("charge") var charge: String? = null
    )

    data class MfsSummeryReportResponse (
        @SerializedName("data") var data      : ArrayList<ArrayList<String>> = arrayListOf()
    )

    data class PaymentReportResponse (
        @SerializedName("data") var data      : ArrayList<ArrayList<String>> = arrayListOf()
    )

    class AddResellerRequest {
        var store_name: String? = null
        var store_owner_name: String? = null
        var manager_user_name: String? = null
        var manager_user_password: String? = null
        var commission: String = "2"
        var base_add_balance_commission_rate: String = "2"
        var conversion_rate: String = "1"
        var store_code: String? = null
        var transaction_pin: String? = null
        var baseCurrency: String? = null
        var store_phone_number: String? = null
        var store_address: String? = null
        var allow_reseller_creation: String? = null
        var mfsIds: String? = null
        var mfsList: String? = null
        var user_id: String? = null
        var loan_slab: String = "0.0"
        var mfsSlab: String = "[]"
        var allowed_products = "[]"
    }

    data class CommonResponse(
        @SerializedName("right_now") var right_now: String? = null,
        @SerializedName("timestamp") var timestamp: String? = null,
        @SerializedName("success") var success: String? = null,
        @SerializedName("message") var message: ArrayList<String>? = null,
    )
}
