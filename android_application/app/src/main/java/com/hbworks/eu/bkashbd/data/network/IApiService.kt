package com.hbworks.eu.bkashbd.data.network

import okhttp3.RequestBody
import okhttp3.ResponseBody
import retrofit2.Response
import retrofit2.http.*

interface IApiService {
    @GET("{url}")
    suspend fun getRequest(
        @Path(value = "url", encoded = true) path: String,
        @QueryMap hashMap: Map<String, String>
    ): Response<ResponseBody>

    @FormUrlEncoded
    @POST("{url}")
    suspend fun postRequest(
        @Path(value = "url", encoded = true) path: String,
        @FieldMap hashMap: Map<String, String>
    ): Response<ResponseBody>

    @Multipart
    @Headers("Content-Type:multipart/form-data")
    @POST("{url}")
    suspend fun sendDocuments(
        @Path(value = "url", encoded = true) path: String,
        @PartMap partMap: Map<String, RequestBody>
    ): Response<ResponseBody>

    @FormUrlEncoded
    @POST("{url}")
    suspend fun pagingPostRequest(
        @Path(value = "url", encoded = true) path: String,
        @FieldMap hashMap: Map<String, String>
    ): Response<ResponseBody>

    @Headers("Content-Type: application/json")
    @POST("{url}")
    suspend fun postRequestForRaw(
        @Path(value = "url", encoded = true) path: String,
        @Body requestBody: RequestBody
    ): Response<ResponseBody>

    @Headers("Content-Type: application/json")
    @PUT("{url}")
    suspend fun putRequest(
        @Path(value = "url", encoded = true) path: String,
        @Body requestBody: RequestBody
    ): Response<ResponseBody>

    @Headers("Content-Type: application/json")
    @PATCH("{url}")
    suspend fun patchRequest(
        @Path(value = "url", encoded = true) path: String,
        @Body requestBody: RequestBody
    ): Response<ResponseBody>

    @Headers("Content-Type: application/json")
    @HTTP(hasBody = true, method = "DELETE", path = "{url}")
    suspend fun deleteRequest(
        @Path(value = "url", encoded = true) path: String,
        @Body requestBody: RequestBody
    ): Response<ResponseBody>

    @Headers("Content-Type: application/json")
    @HTTP(hasBody = true, method = "OPTIONS", path = "{url}")
    suspend fun optionsRequest(
        @Path(value = "url", encoded = true) path: String,
        @Body requestBody: RequestBody
    ): Response<ResponseBody>
}
