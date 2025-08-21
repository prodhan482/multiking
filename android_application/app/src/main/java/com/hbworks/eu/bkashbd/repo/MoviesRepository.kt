package com.hbworks.eu.bkashbd.repo

import com.hbworks.eu.bkashbd.data.DataManager
import com.hbworks.eu.bkashbd.data.network.ApiHelper
import okhttp3.ResponseBody
import retrofit2.Response
import javax.inject.Inject


class MoviesRepository @Inject constructor(val dataManager: DataManager) {
    suspend fun apiResponses(): Response<ResponseBody> {
        val hashMap = HashMap<String, String>()
        hashMap["api_key"] = "ff828a72b45f8a8bc8835e4999ee3f6a"
        return dataManager.apiHelper.getApiCallObservable(
            ApiHelper.CALL_TYPE_GET,
            "",
            hashMap
        )
    }
}
