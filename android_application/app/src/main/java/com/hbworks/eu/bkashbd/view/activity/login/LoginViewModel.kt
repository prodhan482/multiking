package com.hbworks.eu.bkashbd.view.activity.login

import android.app.Application
import android.widget.Toast
import androidx.lifecycle.LifecycleOwner
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.google.gson.Gson
import com.google.gson.annotations.SerializedName
import com.google.gson.reflect.TypeToken
import com.hbworks.eu.bkashbd.data.prefence.PreferencesHelper
import com.hbworks.eu.bkashbd.repo.bKashBdEuRepository
import com.hbworks.eu.bkashbd.view.base.BaseViewModel
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class LoginViewModel @Inject constructor(
    var baseRepo: bKashBdEuRepository
) : BaseViewModel() {

    var loginResponse = MutableLiveData<theLoginResponse>()
    var userProfileResponse = MutableLiveData<UserProfileResponse>()
    var transactionPinVerificationDone = MutableLiveData<Boolean>()

    fun doLogin(user_name:String, user_password:String, fcm_token:String, lifecycleOwner: LifecycleOwner) {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiDoLogin(user_name, user_password, fcm_token)

                val type = object : TypeToken<theLoginResponse>() {}.type

                val baseModel = Gson().fromJson<theLoginResponse>(
                    response.body()?.string(),
                    type
                )

                // use custom response code from data if needed
                when (response.code()) {
                    200 -> {
                        loginResponse.value = baseModel
                    }
                    else -> {
                        errorHandler(arrayListOf("User name or password is wrong or you have been deactivated."), false)
                    }
                }
            } catch (exception: Exception) {
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun getUserProfile(lifecycleOwner: LifecycleOwner) {
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
                println("*------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    fun verifyTransactionPin(pin:String) {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiValidateTransactionPIN(pin)

                // use custom response code from data if needed
                when (response.code()) {
                    200 -> {
                        transactionPinVerificationDone.value = true
                    }
                    422 -> {
                        errorHandler(arrayListOf("Invalid Transaction PIN Number"), false)
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                }
            } catch (exception: Exception) {
                println("*------------------> Error: ${exception.message}")
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    data class theLoginResponse(
        @SerializedName("right_now") var rightNow: String?  = null,
        @SerializedName("timestamp") var timestamp: String?  = null,
        @SerializedName("success") var success: Boolean? = null,
        @SerializedName("token" ) var token: String?  = null
    )
}
