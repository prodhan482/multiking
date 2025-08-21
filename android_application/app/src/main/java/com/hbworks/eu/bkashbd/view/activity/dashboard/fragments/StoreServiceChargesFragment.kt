package com.hbworks.eu.bkashbd.view.activity.dashboard.fragments

import android.content.Context
import android.content.Intent
import android.text.InputType
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.ViewDataBinding
import androidx.fragment.app.activityViewModels
import androidx.fragment.app.viewModels
import androidx.lifecycle.MutableLiveData
import androidx.recyclerview.widget.LinearLayoutManager
import com.afollestad.materialdialogs.MaterialDialog
import com.google.gson.Gson
import com.hbworks.eu.bkashbd.data.model.EuroServiceChargeList
import com.hbworks.eu.bkashbd.data.model.movie_list.ResultsItem
import com.hbworks.eu.bkashbd.databinding.FragmentStoreServiceChargesBinding
import com.hbworks.eu.bkashbd.databinding.RvServiceChargesBinding
import com.hbworks.eu.bkashbd.util.showToast
import com.hbworks.eu.bkashbd.view.activity.dashboard.DashboardViewModel
import com.hbworks.eu.bkashbd.view.activity.dashboard.StoreDashboard
import com.hbworks.eu.bkashbd.view.activity.login.LoginActivity
import com.hbworks.eu.bkashbd.view.activity.login.UserProfileData
import com.hbworks.eu.bkashbd.view.activity.main.MainViewModel
import com.hbworks.eu.bkashbd.view.adapter.IAdapterListener
import com.hbworks.eu.bkashbd.view.base.BaseFragment
import com.hbworks.eu.bkashbd.view.base.BaseRecyclerAdapter
import com.hbworks.eu.bkashbd.view.base.BaseViewHolder
import com.maxkeppeler.sheets.input.InputSheet
import com.maxkeppeler.sheets.input.type.InputEditText
import dagger.hilt.android.AndroidEntryPoint
import java.util.ArrayList


@AndroidEntryPoint
class StoreServiceChargesFragment : BaseFragment<FragmentStoreServiceChargesBinding>() {

    private val viewModel: DashboardViewModel by activityViewModels()
    private lateinit var userDetails: UserProfileData
    private lateinit var euroServiceChargeListMobile : ArrayList<EuroServiceChargeList>
    private lateinit var euroServiceChargeListMfs : ArrayList<EuroServiceChargeList>
    var euroServiceChargeList : MutableList<EuroServiceChargeList> = mutableListOf()
    var mfsType = ""
    var showCloseWarning = false

    override fun viewRelatedTask() {
        userDetails = dataManager.preferencesHelper.getUserInfo()

        binding.TheCloseButton.setOnClickListener {
            if(showCloseWarning)
            {
                MaterialDialog(requireContext()).show {
                    title(text = "Close Window")
                    message(text = "Are you sure about this? Your Changes will be discarded.")
                    negativeButton(text = "Cancel") { dialog ->
                        dialog.dismiss()
                    }
                    positiveButton(text = "Close") { dialog ->
                        showCloseWarning = false
                        when(mfsType)
                        {
                            "mobile_recharge" -> {
                                euroServiceChargeList = euroServiceChargeListMobile
                            }
                            else -> {
                                euroServiceChargeList = euroServiceChargeListMfs
                            }
                        }
                        (activity as StoreDashboard).hideServiceChargeScreen()
                    }
                }
            }
            else
            {
                (activity as StoreDashboard).hideServiceChargeScreen()
            }

        }

        viewModel.rechargeActivityResponse.observe(this, androidx.lifecycle.Observer {
            euroServiceChargeListMobile = it.euroServiceChargeListMobile
            euroServiceChargeListMfs = it.euroServiceChargeListMfs
        })

        (activity as StoreDashboard).selectedMfs.observe(this, androidx.lifecycle.Observer {
            if(it != null)
            {
                euroServiceChargeList = when(it!!.mfsType) {
                    "mobile_recharge" -> {
                        euroServiceChargeListMobile
                    }
                    else -> {
                        euroServiceChargeListMfs
                    }
                }
                mfsType = it!!.mfsType!!
                initRecycler(euroServiceChargeList)
            }
        })

        viewModel.isLoading.observe(viewLifecycleOwner) { showLoader ->
            if (showLoader) {
                progressBarHandler.show()
            } else {
                progressBarHandler.hide()
            }
        }

        viewModel.forceLogOut.observe(viewLifecycleOwner) { logOut ->
            if (logOut) {
                dataManager.preferencesHelper.prefLogout()
                startActivity(Intent(requireContext(), LoginActivity::class.java))
                requireActivity().finish()
                //findNavController().navigate(R.id.action_StoreServiceChargesFragment_to_firstFragment)
            }
        }

        viewModel.errorMessage.observe(viewLifecycleOwner) {
            requireContext().showToast(it)
        }

        binding.doAddNewRow.setOnClickListener {
            InputSheet().show(requireActivity()) {
                title("Add new Service Charge Slab")
                with(InputEditText("from") {
                    required()
                    inputType(android.text.InputType.TYPE_CLASS_NUMBER)
                    label("From")
                    hint("Put starting 'From' Slab")
                })
                with(InputEditText("to") {
                    required()
                    inputType(InputType.TYPE_CLASS_NUMBER)
                    label("To")
                    hint("Put ending 'To' Slab")
                })
                with(InputEditText("charge") {
                    required()
                    inputType(InputType.TYPE_CLASS_NUMBER)
                    label("Charge")
                    hint("Put the range charge")
                })
                onNegative {}
                onPositive { result ->

                    euroServiceChargeList.add(EuroServiceChargeList(result.getString("from"), result.getString("to"), result.getString("charge")))
                    showCloseWarning = true
                    initRecycler(euroServiceChargeList)
                }
            }
        }

        binding.doUpdateRequest.setOnClickListener {
            MaterialDialog(requireContext()).show {
                title(text = "Update Service Charges")
                message(text = "Are you sure about this?")
                negativeButton(text = "Cancel") { dialog ->
                    dialog.dismiss()
                }
                positiveButton(text = "Save") { dialog ->
                    var postData = HashMap<String, String>()
                    when(mfsType)
                    {
                        "mobile_recharge" -> {
                            postData["service_charge_slabs_t2"] = Gson().toJson(euroServiceChargeList)
                        }
                        else -> {
                            postData["service_charge_slabs"] = Gson().toJson(euroServiceChargeList)
                        }
                    }
                    viewModel.saveServiceCharges(userDetails.storeVendorId!!, postData)
                }
            }
        }
    }

    private fun initRecycler(results: List<EuroServiceChargeList?>?) {
        binding.theList.layoutManager =
            LinearLayoutManager(
                requireContext()
            )

        binding.theList.adapter =
            BaseRecyclerAdapter(requireContext(), object : IAdapterListener {
                override fun <T> clickListener(position: Int, model: T, view: View) {
                    euroServiceChargeList.removeAt(position)
                    showCloseWarning = true
                    initRecycler(euroServiceChargeList)
                }

                override fun getViewHolder(parent: ViewGroup, viewType: Int): BaseViewHolder {
                    return ServiceChargesViewHolder(
                        RvServiceChargesBinding.inflate(
                            LayoutInflater.from(parent.context),
                            parent,
                            false
                        ), requireContext()
                    )
                }
            }, results as ArrayList<EuroServiceChargeList?>)
    }

    class ServiceChargesViewHolder(itemView: ViewDataBinding, context: Context) :
        BaseViewHolder(itemView.root) {

        var binding = itemView as RvServiceChargesBinding
        var mContext: Context = context

        override fun <T> onBind(position: Int, model: T, mCallback: IAdapterListener) {
            (model as EuroServiceChargeList)
            binding.TheCloseButton.setOnClickListener {
                mCallback.clickListener(position, model, it!!)
            }
            binding.col1.setText("${position + 1}")
            binding.col2.setText("${model.from}")
            binding.col3.setText("${model.to}")
            binding.col4.setText("${model.charge}")
            binding.TheCloseButton.visibility = View.VISIBLE
        }
    }

    override val bindingInflater: (LayoutInflater, ViewGroup?, Boolean) -> FragmentStoreServiceChargesBinding
        get() = FragmentStoreServiceChargesBinding::inflate
}
