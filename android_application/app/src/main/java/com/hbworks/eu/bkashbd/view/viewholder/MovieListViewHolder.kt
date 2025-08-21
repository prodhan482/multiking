package com.hbworks.eu.bkashbd.view.viewholder

import android.content.Context
import android.text.Editable
import android.text.InputType
import android.text.TextWatcher
import android.view.View
import androidx.core.content.ContextCompat
import androidx.viewbinding.ViewBinding
import com.hbworks.eu.bkashbd.data.model.movie_list.ResultsItem
import com.hbworks.eu.bkashbd.databinding.CustomMovieListRecyclerBinding
import com.hbworks.eu.bkashbd.view.adapter.IAdapterListener
import com.hbworks.eu.bkashbd.view.base.BaseViewHolder

class MovieListViewHolder(
    itemView: ViewBinding,
    context: Context
) :
    BaseViewHolder(itemView.root) {

    var binding = itemView as CustomMovieListRecyclerBinding
    var mContext: Context = context

    override fun <T> onBind(position: Int, itemModel: T, listener: IAdapterListener) {
        itemModel as ResultsItem

        binding.name.text = itemModel.title
        binding.name2.text = itemModel.releaseDate

        binding.root.setOnClickListener {
            listener.clickListener(position, itemModel, binding.root)
        }
    }
}