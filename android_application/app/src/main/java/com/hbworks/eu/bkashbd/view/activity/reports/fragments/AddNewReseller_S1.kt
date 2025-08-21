package com.hbworks.eu.bkashbd.view.activity.reports.fragments

import android.app.Activity
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.activity.addCallback
import androidx.core.widget.doOnTextChanged
import androidx.fragment.app.activityViewModels
import androidx.navigation.fragment.findNavController
import com.afollestad.materialdialogs.MaterialDialog
import com.afollestad.vvalidator.form
import com.google.gson.Gson
import com.hbworks.eu.bkashbd.R
import com.hbworks.eu.bkashbd.databinding.FragmentAddNewResellerS1Binding
import com.hbworks.eu.bkashbd.lib.MultiTaskingDropdownSpinner
import com.hbworks.eu.bkashbd.view.activity.login.UserProfileData
import com.hbworks.eu.bkashbd.view.activity.reports.ReportViewModel
import com.hbworks.eu.bkashbd.view.base.BaseFragment
import com.sslwireless.crm.lib.multi_tasking_dropdown_spinner_form_validation.multiTaskingDropdownSpinnerView
import dagger.hilt.android.AndroidEntryPoint

@AndroidEntryPoint
class AddNewReseller_S1 : BaseFragment<FragmentAddNewResellerS1Binding>()
{
    private val viewModel: ReportViewModel by activityViewModels()
    private lateinit var userDetails: UserProfileData

    var __mfs_list: MutableList<ReportViewModel.ResellerConfigMfsListResponse> = mutableListOf()

    override fun viewRelatedTask()
    {
        binding.info = viewModel.newResellerInfo

        binding.backBtn.setOnClickListener {
            closeMe()
        }

        userDetails = dataManager.preferencesHelper.getUserInfo()
        viewModel.loadAddResellerConfig()
        populateBaseCurrency()
        populateResellerCreationDD()

        viewModel.resellerConf.observe(this, androidx.lifecycle.Observer {
            var bCurr: MutableList<MultiTaskingDropdownSpinner.Items> = mutableListOf()
            __mfs_list.clear()

            it.mfs_list.forEach {
                var LLA: MultiTaskingDropdownSpinner.Items = MultiTaskingDropdownSpinner.Items()
                LLA.id = it.id!!
                LLA.title = it.name!!
                if(LLA.title.contains("Mobile Recharge "))
                {
                    LLA.title = "${LLA.title.replace("Mobile Recharge ", "")}"
                }
                LLA.selcted = true
                bCurr.add(LLA)
            }
            __mfs_list.addAll(it.mfs_list)
            binding.AllowedMFS.addItems(bCurr)
        })

        form {
            input(binding.ResellerStoreName) {
                isNotEmpty()
                onValue { view, value ->
                    viewModel.resellerAddInHashMap["Reseller Store Title"] = value.value.toString()
                    viewModel.newResellerInfo.store_name = value.value.toString()
                }
            }
            input(binding.ResellerCode) {
                isNotEmpty()
                onValue { view, value ->
                    viewModel.resellerAddInHashMap["Reseller Code"] = value.value.toString()
                    viewModel.newResellerInfo.store_code = value.value.toString()
                }
            }
            input(binding.ResellerName) {
                onValue { view, value ->
                    viewModel.resellerAddInHashMap["Name"] = value.value.toString()
                    viewModel.newResellerInfo.store_owner_name = value.value.toString()
                }
            }
            input(binding.ResellerPhone) {
                onValue { view, value ->
                    viewModel.resellerAddInHashMap["Phone"] = value.value.toString()
                    viewModel.newResellerInfo.store_phone_number = value.value.toString()
                }
            }
            input(binding.ResellerAddress) {
                onValue { view, value ->
                    viewModel.resellerAddInHashMap["Address"] = value.value.toString()
                    viewModel.newResellerInfo.store_address = value.value.toString()
                }
            }

            input(binding.ResellerUserName) {
                isNotEmpty()
                onValue { view, value ->
                    viewModel.resellerAddInHashMap["Web User Name"] = value.value.toString()
                    viewModel.newResellerInfo.manager_user_name = value.value.toString()
                }
            }
            input(binding.ResellerPassword) {
                isNotEmpty()
                onValue { view, value ->
                    viewModel.resellerAddInHashMap["Web User Password"] = value.value.toString()
                    viewModel.newResellerInfo.manager_user_password = value.value.toString()
                }
            }
            input(binding.ResellerTransactionPin) {
                isNotEmpty()
                onValue { view, value ->
                    viewModel.resellerAddInHashMap["Web Transaction Pin"] = value.value.toString()
                    viewModel.newResellerInfo.transaction_pin = value.value.toString()
                }
            }

            multiTaskingDropdownSpinnerView(binding.BaseCurrency) {
                cannotBeEmpty()
                onValue { view, value ->
                    viewModel.newResellerInfo.baseCurrency = (if (!value.value.toString().isNullOrEmpty()) value.value.toString() else "bdt")
                    viewModel.resellerAddInHashMap["Base Currency"] = viewModel.newResellerInfo.baseCurrency!!.uppercase()
                }
            }

            input(binding.BaseCommission) {
                isNotEmpty()
                onValue { view, value ->
                    viewModel.newResellerInfo.base_add_balance_commission_rate = value.value.toString()
                    viewModel.newResellerInfo.commission = value.value.toString()
                    viewModel.resellerAddInHashMap["Base Commission"] = value.value.toString()
                }
            }
            multiTaskingDropdownSpinnerView(binding.AllowResellerCreation) {
                onValue { view, value ->
                    viewModel.newResellerInfo.allow_reseller_creation = value.value.toString()
                    viewModel.resellerAddInHashMap["Allow Reseller Creation"] = value.value.toString()
                }
            }
            multiTaskingDropdownSpinnerView(binding.AllowedMFS) {
                onValue { view, value ->
                    viewModel.newResellerInfo.mfsIds = value.value.toString()

                    var MFS_names = mutableListOf<String>()

                    value.value.toString().split(",").forEach {
                        __mfs_list.forEach { mf ->
                            if(mf.id == it) MFS_names.add(mf.name!!)
                        }
                    }

                    viewModel.resellerAddInHashMap["Allowed MFS"] = MFS_names.joinToString(", ")
                }
            }
            submitWith(binding.ConfirmButton) { result ->
                viewModel.newResellerInfo.user_id = userDetails.userId

                //println("========> ${Gson().toJson(viewModel.newResellerInfo)}")
                findNavController().navigate(R.id.move_AddNewReseller_S2)
            }
        }

        requireActivity().onBackPressedDispatcher.addCallback(this@AddNewReseller_S1){
            MaterialDialog(requireContext()).show {
                title(text = "Cancel Reseller Creation")
                message(text = "Are you sure about canceling reseller creation?")
                negativeButton(text = "Skip") { dialog ->
                    dialog.dismiss()
                }
                positiveButton(text = "Cancel") { dialog ->
                    clearAll()
                    closeMe()
                }
            }
        }

        viewModel.resellerCreatedSuccessfully.observe(this, androidx.lifecycle.Observer {
            if(it!!){
                clearAll()
            }
        })
    }

    fun clearAll()
    {
        binding.ResellerStoreName.setText("")
        binding.ResellerCode.setText("")
        binding.ResellerName.setText("")
        binding.ResellerPhone.setText("")
        binding.ResellerAddress.setText("")
        binding.ResellerUserName.setText("")
        binding.ResellerPassword.setText("")
        binding.ResellerTransactionPin.setText("")
        binding.BaseCommission.setText("")

        binding.ResellerStoreName.error = null
        binding.ResellerCode.error = null
        binding.ResellerName.error = null
        binding.ResellerPhone.error = null
        binding.ResellerAddress.error = null
        binding.ResellerUserName.error = null
        binding.ResellerPassword.error = null
        binding.ResellerTransactionPin.error = null
        binding.BaseCommission.error = null
    }

    fun populateBaseCurrency()
    {
        var bCurr: MutableList<MultiTaskingDropdownSpinner.Items> =
            ArrayList<MultiTaskingDropdownSpinner.Items>()

        var i = listOf<String>("bdt", "euro", "gbp", "usd", "cfa_franc")
        var ii = listOf<String>("BDT (Bangladesh Taka)", "EURO (European Union Currency)", "GBP (Great Britain Pound)", "USD (United States Dollar)", "CFA Franc (Central African CFA franc)")

        for (index in 0..4) {
            var LLA: MultiTaskingDropdownSpinner.Items = MultiTaskingDropdownSpinner.Items()
            LLA.id = i[index]
            LLA.title = ii[index]
            LLA.selcted = (index == 0)
            bCurr.add(LLA)
        }

        binding.BaseCurrency.addItems(bCurr)
    }

    fun populateResellerCreationDD()
    {
        var bCurr: MutableList<MultiTaskingDropdownSpinner.Items> =
            ArrayList<MultiTaskingDropdownSpinner.Items>()

        var i = listOf<String>("No", "Yes")
        var ii = listOf<String>("Don't Allow Reseller Creation", "Allow Reseller Creation")

        for (index in 0..1) {
            var LLA: MultiTaskingDropdownSpinner.Items = MultiTaskingDropdownSpinner.Items()
            LLA.id = i[index]
            LLA.title = ii[index]
            LLA.selcted = (index == 0)
            bCurr.add(LLA)
        }

        binding.AllowResellerCreation.addItems(bCurr)
    }

    fun closeMe(canceled:Boolean = true)
    {
        if(canceled) requireActivity().setResult(Activity.RESULT_CANCELED, null)
        requireActivity().finish()
    }

    override val bindingInflater: (LayoutInflater, ViewGroup?, Boolean) -> FragmentAddNewResellerS1Binding
        get() = FragmentAddNewResellerS1Binding::inflate
}
