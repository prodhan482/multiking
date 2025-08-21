package com.hbworks.eu.bkashbd.di

import android.content.Context
import com.google.gson.Gson
import com.hbworks.eu.bkashbd.BuildConfig
import com.hbworks.eu.bkashbd.data.local_db.RoomHelper
import com.hbworks.eu.bkashbd.data.network.ApiHelper
import com.hbworks.eu.bkashbd.data.network.IApiService
import com.hbworks.eu.bkashbd.data.prefence.PreferencesHelper
import com.hbworks.eu.bkashbd.view.base.BaseModel
import dagger.Module
import dagger.Provides
import dagger.hilt.InstallIn
import dagger.hilt.android.qualifiers.ApplicationContext
import dagger.hilt.components.SingletonComponent
import io.reactivex.schedulers.Schedulers
import okhttp3.*
import okhttp3.MediaType.Companion.toMediaTypeOrNull
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.adapter.rxjava2.RxJava2CallAdapterFactory
import retrofit2.converter.gson.GsonConverterFactory
import java.net.SocketTimeoutException
import java.util.concurrent.TimeUnit
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
object AppModule {

    @Provides
    @Singleton
    fun provideOkHttpClient(
        preferencesHelper: PreferencesHelper,
        httpLoggingInterceptor: HttpLoggingInterceptor
    ): OkHttpClient {
        return OkHttpClient.Builder()
            .connectTimeout(10, TimeUnit.SECONDS)
            .writeTimeout(10, TimeUnit.SECONDS)
            .readTimeout(10, TimeUnit.SECONDS)
            .followRedirects(true)
            .followSslRedirects(true)
            .retryOnConnectionFailure(true)
            .addInterceptor { chain ->
                val request = chain.request().newBuilder()
                    .addHeader("Content-Type", "application/json")
                    .addHeader("mobile-app", "yes")
                    //.addHeader("Authorization", "Bearer "+preferencesHelper.prefGetToken())

                if(!preferencesHelper.prefGetToken().isNullOrEmpty()) request.addHeader("Authorization", "Bearer "+preferencesHelper.prefGetToken())

                chain.proceed(request.build())
            }.addInterceptor(httpLoggingInterceptor)
            .addInterceptor(ErrorInterceptor()).build()
    }

    @Provides
    @Singleton
    fun provideHttpLoggingInterceptor(): HttpLoggingInterceptor {
        val interceptor = HttpLoggingInterceptor()
        if (BuildConfig.DEBUG)
            interceptor.level = HttpLoggingInterceptor.Level.BODY
        else
            interceptor.level = HttpLoggingInterceptor.Level.NONE
        return interceptor
    }

    @Provides
    @Singleton
    fun provideRetrofit(okHttpClient: OkHttpClient): Retrofit {
        return Retrofit.Builder()
            .baseUrl(BuildConfig.SERVER_URL)
            .addConverterFactory(GsonConverterFactory.create())
            .addCallAdapterFactory(RxJava2CallAdapterFactory.createWithScheduler(Schedulers.io()))
            .client(okHttpClient)
            .build()
    }

    @Provides
    @Singleton
    fun provideApiService(retrofit: Retrofit): IApiService {
        return retrofit.create(IApiService::class.java)
    }

    @Provides
    @Singleton
    fun providePreference(@ApplicationContext context: Context): PreferencesHelper {
        return PreferencesHelper(context)
    }

    @Provides
    @Singleton
    fun provideRoomHelper(@ApplicationContext context: Context): RoomHelper {
        return RoomHelper(context)
    }

    @Provides
    @Singleton
    fun provideApiHelper(apiService: IApiService): ApiHelper {
        return ApiHelper(apiService)
    }

    class ErrorInterceptor : okhttp3.Interceptor {
        override fun intercept(chain: okhttp3.Interceptor.Chain): okhttp3.Response {
            val request = chain.request()
            val response: Response
            try {
                response = chain.proceed(request)
                return response.newBuilder().body(response.body).build()
            } catch (e: Exception) {
                var msg = "Server Error. Please contact with technical team."
                var interceptorCode = 410
                var resp = BaseModel<String>()

                when (e) {
                    is SocketTimeoutException -> {
                        msg = "Server Responds too slow. Please try again later."
                        interceptorCode = 408
                        resp.code = "${interceptorCode}"
                        resp.message = "${e.message}"
                        resp.data = null
                    }
                    else -> {
                        resp.code = "${interceptorCode}"
                        resp.message = "Server Error: ${e.message}"
                        resp.data = null
                    }
                }

                return Response.Builder()
                    .code(200)
                    .message("${msg}")
                    .protocol(Protocol.HTTP_1_1)
                    .body(ResponseBody.create("application/json".toMediaTypeOrNull(), Gson().toJson(resp)))
                    .request(Request.Builder().url(BuildConfig.SERVER_URL).build())
                    .build()
            }
        }
    }
}
