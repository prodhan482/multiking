package com.hbworks.eu.bkashbd.data.network

import com.google.gson.Gson
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import okhttp3.MediaType
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.RequestBody
import okhttp3.RequestBody.Companion.toRequestBody
import okhttp3.ResponseBody
import retrofit2.Response


class ApiHelper(val apiService: IApiService) {
    //call type
    companion object {
        const val CALL_TYPE_GET = "get"
        const val CALL_TYPE_POST = "post"
        const val CALL_TYPE_PATCH = "patch"
        const val CALL_TYPE_OPTIONS = "options"
        const val CALL_TYPE_PUT = "put"
        const val CALL_TYPE_DELETE = "delete"
        const val CALL_TYPE_JSON_POST = "post_json"
        const val CALL_TYPE_POST_WITH_DOCUMENT = "post with document"
        const val CALL_TYPE_PAGING = "paging"
    }

    suspend fun <T> getApiCallObservable(
        callType: String,
        path: String,
        hashMap: HashMap<String, T>
    ): Response<ResponseBody> {
        return withContext(Dispatchers.IO) {
            when (callType) {
                CALL_TYPE_GET -> {
                    apiService.getRequest(path, hashMap as HashMap<String, String>)
                }
                CALL_TYPE_POST -> {
                    apiService.postRequest(path, hashMap as HashMap<String, String>)
                }
                CALL_TYPE_JSON_POST -> {
                    apiService.postRequestForRaw(path, (Gson().toJson(hashMap)).toRequestBody("text/plain".toMediaTypeOrNull()))
                }
                CALL_TYPE_PATCH -> {
                    apiService.patchRequest(path, (Gson().toJson(hashMap)).toRequestBody("text/plain".toMediaTypeOrNull()))
                }
                CALL_TYPE_PUT -> {
                    apiService.putRequest(path, (Gson().toJson(hashMap)).toRequestBody("text/plain".toMediaTypeOrNull()))
                }
                CALL_TYPE_DELETE -> {
                    apiService.deleteRequest(path, (Gson().toJson(hashMap)).toRequestBody("text/plain".toMediaTypeOrNull()))
                }
                CALL_TYPE_PAGING -> {
                    apiService.pagingPostRequest(path, hashMap as HashMap<String, String>)
                }
                CALL_TYPE_OPTIONS -> {
                    apiService.optionsRequest(path, (Gson().toJson(hashMap)).toRequestBody("text/plain".toMediaTypeOrNull()))
                }
                else -> {
                    apiService.sendDocuments(path, hashMap as HashMap<String, RequestBody>)
                }
            }
        }
    }
}
