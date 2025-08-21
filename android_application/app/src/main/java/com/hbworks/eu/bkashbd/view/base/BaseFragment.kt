package com.hbworks.eu.bkashbd.view.base

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import androidx.viewbinding.ViewBinding
import com.hbworks.eu.bkashbd.data.DataManager
import com.hbworks.eu.bkashbd.util.ProgressBarHandler
import com.hbworks.eu.bkashbd.util.ViewDialog
import javax.inject.Inject


abstract class BaseFragment<VB: ViewBinding> : Fragment() {
    @Inject
    lateinit var dataManager: DataManager

    private var _binding: ViewBinding? = null
    abstract val bindingInflater: (LayoutInflater, ViewGroup?, Boolean) -> VB
    val progressBarHandler: ProgressBarHandler by lazy {
        ProgressBarHandler(requireActivity())
    }
    val viewDialog by lazy { ViewDialog(requireContext()) }

    @Suppress("UNCHECKED_CAST")
    protected val binding: VB
        get() = _binding as VB

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        viewRelatedTask()
    }


    abstract fun viewRelatedTask()

    override fun onCreateView(inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?): View? {
        _binding = bindingInflater.invoke(inflater, container, false)
        return requireNotNull(_binding).root
    }

}