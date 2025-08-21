package com.hbworks.eu.bkashbd.view.fragment

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.viewModels
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import com.hbworks.eu.bkashbd.R
import com.hbworks.eu.bkashbd.data.model.movie_list.ResultsItem
import com.hbworks.eu.bkashbd.databinding.CustomMovieListRecyclerBinding
import com.hbworks.eu.bkashbd.databinding.FragmentStartBinding
import com.hbworks.eu.bkashbd.util.showToast
import com.hbworks.eu.bkashbd.view.activity.main.MainViewModel
import com.hbworks.eu.bkashbd.view.adapter.IAdapterListener
import com.hbworks.eu.bkashbd.view.base.BaseFragment
import com.hbworks.eu.bkashbd.view.base.BaseRecyclerAdapter
import com.hbworks.eu.bkashbd.view.base.BaseViewHolder
import com.hbworks.eu.bkashbd.view.viewholder.MovieListViewHolder
import dagger.hilt.android.AndroidEntryPoint
import java.util.*

@AndroidEntryPoint
class StartFragment : BaseFragment<FragmentStartBinding>() {
    private val viewModel: MainViewModel by viewModels()

    override fun viewRelatedTask() {
        viewModel.apiResponseInit()

        viewModel.movieListResponse.observe(viewLifecycleOwner) {
            initRecycler(it.results)
        }

        viewModel.isLoading.observe(viewLifecycleOwner) { showLoader ->
            if (showLoader) {
                progressBarHandler.show()
            } else {
                progressBarHandler.hide()
            }
        }

        viewModel.forceLogOut.observe(viewLifecycleOwner) { logOut ->
            if (logOut) {
                findNavController().navigate(R.id.action_startFragment_to_firstFragment)
            }
        }

        viewModel.errorMessage.observe(viewLifecycleOwner) {
            requireContext().showToast(it)
        }
    }

    private fun initRecycler(results: List<ResultsItem?>?) {
        binding.movieList.layoutManager =
            LinearLayoutManager(
                requireContext()
            )

        binding.movieList.adapter =
            BaseRecyclerAdapter(requireContext(), object : IAdapterListener {

                override fun <T> clickListener(position: Int, model: T, view: View) {
                }

                override fun getViewHolder(parent: ViewGroup, viewType: Int): BaseViewHolder {
                    return MovieListViewHolder(
                        CustomMovieListRecyclerBinding.inflate(
                            LayoutInflater.from(parent.context),
                            parent,
                            false
                        ), requireContext()
                    )
                }
            }, results as ArrayList<ResultsItem?>)
    }

    override val bindingInflater: (LayoutInflater, ViewGroup?, Boolean) -> FragmentStartBinding
        get() = FragmentStartBinding::inflate
}