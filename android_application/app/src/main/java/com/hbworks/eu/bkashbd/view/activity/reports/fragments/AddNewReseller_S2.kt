package com.hbworks.eu.bkashbd.view.activity.reports.fragments

import android.app.Activity
import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Toast
import androidx.activity.addCallback
import androidx.databinding.DataBindingUtil
import androidx.databinding.ViewDataBinding
import androidx.fragment.app.activityViewModels
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import com.afollestad.materialdialogs.MaterialDialog
import com.google.gson.Gson
import com.hbworks.eu.bkashbd.R
import com.hbworks.eu.bkashbd.databinding.FragmentAddNewResellerS2Binding
import com.hbworks.eu.bkashbd.databinding.RvTable3cT2Binding
import com.hbworks.eu.bkashbd.view.activity.login.UserProfileData
import com.hbworks.eu.bkashbd.view.activity.reports.ReportViewModel
import com.hbworks.eu.bkashbd.view.adapter.IAdapterListener
import com.hbworks.eu.bkashbd.view.base.BaseFragment
import com.hbworks.eu.bkashbd.view.base.BaseRecyclerAdapter
import com.hbworks.eu.bkashbd.view.base.BaseViewHolder
import com.hbworks.eu.bkashbd.view.common.EmptyViewHolder
import dagger.hilt.android.AndroidEntryPoint

@AndroidEntryPoint
class AddNewReseller_S2 : BaseFragment<FragmentAddNewResellerS2Binding>()
{
    private val viewModel: ReportViewModel by activityViewModels()
    private lateinit var userDetails: UserProfileData

    override fun viewRelatedTask()
    {
        userDetails = dataManager.preferencesHelper.getUserInfo()

        binding.backBtt.setOnClickListener {
            findNavController().navigate(R.id.move_AddNewReseller_S1)
        }

        requireActivity().onBackPressedDispatcher.addCallback(this@AddNewReseller_S2){
            findNavController().navigate(R.id.move_AddNewReseller_S1)
        }

        var theList = mutableListOf<MutableList<String>>()

        var pos = 1
        viewModel.resellerAddInHashMap.forEach {
            theList.add(mutableListOf(pos.toString(), it.key, it.value))
            pos = pos + 1
        }

        binding.ConfirmButton.setOnClickListener {
            MaterialDialog(requireContext()).show {
                title(text = "Reseller Creation")
                message(text = "Are you sure about submit this informations?")
                negativeButton(text = "Cancel") { dialog ->
                    dialog.dismiss()
                }
                positiveButton(text = "Yes") { dialog ->

                    var finalMFSJson = mutableListOf<ReportViewModel.ResellerConfigMfsListResponse>()
                    finalMFSJson.clear()

                    viewModel.newResellerInfo.mfsIds!!.split(",").forEach {
                        viewModel.resellerConf.value!!.mfs_list.forEach { mfsInfo ->
                            if(it.equals(mfsInfo.id)) finalMFSJson.add(mfsInfo)
                        }
                    }

                    viewModel.newResellerInfo.mfsList = Gson().toJson(finalMFSJson)

                    viewModel.createNewReseller()
                }
            }
        }

        viewModel.resellerCreatedSuccessfully.observe(this, androidx.lifecycle.Observer {
            if(it!!){
                Toast.makeText(context, "Reseller Created Successfully", Toast.LENGTH_SHORT).show()
                requireActivity().setResult(Activity.RESULT_OK, null)
                requireActivity().finish()
            }
        })

        binding.theList.layoutManager = LinearLayoutManager(requireContext())
        binding.theList.adapter = BaseRecyclerAdapter(requireContext(), object : IAdapterListener {
            override fun <T> clickListener(position: Int, model: T, view: View) {
                model as ArrayList<String>
            }

            override fun getViewHolder(parent: ViewGroup, viewType: Int): BaseViewHolder {
                if(viewType>-1 ){
                    return TheRecyclerAdapter(
                        DataBindingUtil.inflate(
                            LayoutInflater.from(parent.context),
                            R.layout.rv_table_3c_t2,
                            parent,
                            false
                        ), requireContext()
                    )
                }
                else
                {
                    return EmptyViewHolder(
                        DataBindingUtil.inflate(
                            LayoutInflater.from(parent.context)
                            , R.layout.empty_page
                            , parent, false
                        )
                        , parent.context
                    )
                }
            }

        }, theList as ArrayList)
    }


    class TheRecyclerAdapter(itemView: ViewDataBinding, context: Context) :
        BaseViewHolder(itemView.root) {

        var binding = itemView as RvTable3cT2Binding
        var mContext: Context = context

        override fun <T> onBind(position: Int, model: T, mCallback: IAdapterListener) {
            model as ArrayList<String>

            binding.col1.text = "${model[0]}"
            binding.col2.text = "${model[1]}"
            binding.col3.text = "${model[2]}"
        }
    }

    override val bindingInflater: (LayoutInflater, ViewGroup?, Boolean) -> FragmentAddNewResellerS2Binding
        get() = FragmentAddNewResellerS2Binding::inflate
}
