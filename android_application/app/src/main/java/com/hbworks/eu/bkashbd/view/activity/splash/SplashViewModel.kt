package com.hbworks.eu.bkashbd.view.activity.splash

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
class SplashViewModel @Inject constructor(
    var baseRepo: bKashBdEuRepository
) : BaseViewModel() {

    var qResponse = MutableLiveData<theResponse>()

    fun fetchVersionCheck(version: String, lifecycleOwner: LifecycleOwner) {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = baseRepo.apiVersionCheck(version)

                val type = object : TypeToken<theResponse>() {}.type

                val baseModel = Gson().fromJson<theResponse>(
                    response.body()?.string(),
                    type
                )

                // use custom response code from data if needed
                when (response.code()) {
                    200 -> {
                        qResponse.value = baseModel
                    }
                    422 -> {
                        errorHandler(arrayListOf("1"), false)
                    }
                    401 -> {
                        forceLogOut(true)
                    }
                    408, 410 -> {
                        errorHandler(arrayListOf("Server Responds too slow. Please try again later."), false)
                    }
                }
            } catch (exception: Exception) {
                errorHandler(arrayListOf("1"), true)
            }

            onLoading(false)
        }
    }

    data class theResponse(
        @SerializedName("right_now"           ) var rightNow          : String?  = null,
        @SerializedName("timestamp"           ) var timestamp         : String?  = null,
        @SerializedName("success"             ) var success           : Boolean? = null,
        @SerializedName("allow_version_up_to" ) var allowVersionUpTo  : String?  = null,
        @SerializedName("mandatory_update_to" ) var mandatoryUpdateTo : String?  = null,
        @SerializedName("download_url"        ) var downloadUrl       : String?  = null
    )
}
