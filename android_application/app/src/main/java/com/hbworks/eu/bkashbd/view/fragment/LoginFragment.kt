package com.hbworks.eu.bkashbd.view.fragment

import android.view.LayoutInflater
import android.view.ViewGroup
import com.hbworks.eu.bkashbd.databinding.FragmentFirstBinding
import com.hbworks.eu.bkashbd.view.base.BaseFragment
import dagger.hilt.android.AndroidEntryPoint

@AndroidEntryPoint
class LoginFragment : BaseFragment<FragmentFirstBinding>() {

    override fun viewRelatedTask() {

    }

    override val bindingInflater: (LayoutInflater, ViewGroup?, Boolean) -> FragmentFirstBinding
        get() = FragmentFirstBinding::inflate

}