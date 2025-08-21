package com.hbworks.eu.bkashbd.view.common

import android.content.Context
import androidx.databinding.ViewDataBinding
import com.hbworks.eu.bkashbd.databinding.EmptyPageBinding
import com.hbworks.eu.bkashbd.view.adapter.IAdapterListener
import com.hbworks.eu.bkashbd.view.base.BaseViewHolder

class EmptyViewHolder (itemView: ViewDataBinding, context: Context) :
    BaseViewHolder(itemView.root) {
    var binding = itemView as EmptyPageBinding

    override fun <T> onBind(position: Int, itemModel: T, listener: IAdapterListener) {

    }
}
