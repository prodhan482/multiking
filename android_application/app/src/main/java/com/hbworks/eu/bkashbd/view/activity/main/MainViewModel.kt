package com.hbworks.eu.bkashbd.view.activity.main

import android.widget.Toast
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.viewModelScope
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken
import com.hbworks.eu.bkashbd.data.model.movie_list.MovieListRespone
import com.hbworks.eu.bkashbd.repo.MoviesRepository
import com.hbworks.eu.bkashbd.view.base.BaseViewModel
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class MainViewModel @Inject constructor(
    var moviesRepo: MoviesRepository
) : BaseViewModel() {

    var movieListResponse = MutableLiveData<MovieListRespone>()

    // with live data
    fun apiResponseInit() {
        viewModelScope.launch {
            onLoading(true)

            try {
                val response = moviesRepo.apiResponses()

                val type = object : TypeToken<MovieListRespone>() {}.type

                val baseModel = Gson().fromJson<MovieListRespone>(
                    response.body()?.string(),
                    type
                )

                // use custom response code from data if needed
                when (response.code()) {
                    200 -> {
                        movieListResponse.value = baseModel
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
}
