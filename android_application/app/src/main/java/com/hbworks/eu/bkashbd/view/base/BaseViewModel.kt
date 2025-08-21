package com.hbworks.eu.bkashbd.view.base

import androidx.lifecycle.LiveData
import androidx.lifecycle.ViewModel
import com.hbworks.eu.bkashbd.util.SingleLiveEvent
import okhttp3.ResponseBody
import retrofit2.Response

abstract class BaseViewModel : ViewModel() {
    private var response: Response<ResponseBody>? = null

    private val _isLoading = SingleLiveEvent<Boolean>()
    val isLoading: LiveData<Boolean> get() = _isLoading

    var forceLogOut = SingleLiveEvent<Boolean>()
    var errorMessage = SingleLiveEvent<String>()

    private val listString = StringBuilder()

    fun onLoading(isLoader: Boolean) {
        _isLoading.value = isLoader
    }

    fun forceLogOut(flag: Boolean) {
        forceLogOut.value = flag
    }

    fun errorHandler(message: ArrayList<String>, isServerError: Boolean) {
        if (isServerError) {
            errorMessage.value = "Something went wrong! Please try again."
        } else {
            listString.clear()

            for (item in message.iterator()) {
                listString.append(item)
            }

            errorMessage.value = listString.toString()
        }

//        fun <T : Any> convertData(classType: KClass<T>, response: Response<ResponseBody>): Any {
//            val body = if (response.isSuccessful) {
//                response.body()?.string()
//            } else {
//                response.errorBody()?.string()
//            }
//
//            return Gson().fromJson(
//                body,
//                classType.java
//            )
//        }
    }
}