package com.hbworks.eu.bkashbd.view.activity.reports

import android.content.Context
import android.content.Intent
import android.os.Build
import android.os.Bundle
import android.text.Html
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.databinding.DataBindingUtil
import androidx.databinding.ViewDataBinding
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import com.afollestad.vvalidator.form
import com.google.android.material.chip.Chip
import com.google.gson.Gson
import com.google.gson.annotations.SerializedName
import com.google.gson.reflect.TypeToken
import com.hbworks.eu.bkashbd.R
import com.hbworks.eu.bkashbd.data.model.StoreInfo
import com.hbworks.eu.bkashbd.databinding.ActivityPaymentReportBinding
import com.hbworks.eu.bkashbd.databinding.RvTable3cT1Binding
import com.hbworks.eu.bkashbd.databinding.RvTable4cT1Binding
import com.hbworks.eu.bkashbd.lib.MultiTaskingDropdownSpinner
import com.hbworks.eu.bkashbd.util.Constants
import com.hbworks.eu.bkashbd.util.showToast
import com.hbworks.eu.bkashbd.view.activity.login.LoginActivity
import com.hbworks.eu.bkashbd.view.activity.login.UserProfileData
import com.hbworks.eu.bkashbd.view.adapter.IAdapterListener
import com.hbworks.eu.bkashbd.view.base.BaseActivity
import com.hbworks.eu.bkashbd.view.base.BaseRecyclerAdapter
import com.hbworks.eu.bkashbd.view.base.BaseViewHolder
import com.hbworks.eu.bkashbd.view.common.EmptyViewHolder
import com.maxkeppeler.sheets.calendar.CalendarSheet
import com.maxkeppeler.sheets.calendar.SelectionMode
import com.soywiz.klock.DateFormat
import com.soywiz.klock.DateTime
import com.soywiz.klock.days
import com.sslwireless.crm.lib.multi_tasking_dropdown_spinner_form_validation.multiTaskingDropdownSpinnerView
import java.util.*
import kotlin.collections.ArrayList
import kotlin.collections.HashMap

class PaymentReport : BaseActivity() {

    private lateinit var userDetails: UserProfileData
    private lateinit var viewModel: ReportViewModel
    private var FilterCriteria: LinkedHashMap<String, String> = LinkedHashMap<String, String>()

    private var paymentReport:MutableList<ArrayList<String>> =  mutableListOf()

    private val binding: ActivityPaymentReportBinding by lazy {
        ActivityPaymentReportBinding.inflate(layoutInflater)
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(binding.root)

        userDetails = dataManager.preferencesHelper.getUserInfo()
        this.viewModel = ViewModelProvider(this).get(ReportViewModel::class.java)
    }

    override fun viewRelatedTask() {

        FilterCriteria["date_from"] = "From: ${(DateTime.now() - 30.days).format(DateFormat("yyyy-MM-dd"))}||${(DateTime.now() - 30.days).format(DateFormat("yyyy-MM-dd"))}"
        FilterCriteria["date_to"] = "To: ${DateTime.now().format(DateFormat("yyyy-MM-dd"))}||${DateTime.now().format(DateFormat("yyyy-MM-dd"))}"

        binding.FromDate.setText((DateTime.now() - 30.days).format(DateFormat("yyyy-MM-dd")))
        binding.ToDate.setText((DateTime.now()).format(DateFormat("yyyy-MM-dd")))

        if(Constants.newInstance().USER_TYPE_STORE == userDetails.userType)
        {
            FilterCriteria["store_id"] = "Reseller: Self||${userDetails.storeVendorId}"
        }

        binding.filterGroup.visibility = View.VISIBLE
        binding.clearAllBtn.visibility = View.GONE

        if(userDetails.userType == Constants.newInstance().USER_TYPE_SUPER_ADMIN || userDetails.permissionLists.contains("StoreController::list"))
        {

            binding.ResellerList.visibility = View.VISIBLE

            val basic_store_list = Gson().fromJson<List<StoreInfo>>(
                dataManager.preferencesHelper.get("basic_store_list", "[]"),
                object : TypeToken<List<StoreInfo>>() {}.type
            )

            val items: MutableList<MultiTaskingDropdownSpinner.Items> = ArrayList()
            basic_store_list.forEach {
                val item: MultiTaskingDropdownSpinner.Items = MultiTaskingDropdownSpinner.Items()
                item.id = "${it.storeId}"
                item.title = "${it.storeName}"
                items.add(item)
            }

            binding.ResellerList.addItems(items)
        }

        binding.filterBtt.setOnClickListener {
            toggleFilterPanel()
        }

        binding.clearAllBtn.setOnClickListener {
            clearAllFilter()
        }

        viewModel.errorMessage.observe(this, androidx.lifecycle.Observer {
            showToast(it!!)
        })

        viewModel.forceLogOut.observe(this, androidx.lifecycle.Observer {
            dataManager.preferencesHelper.prefLogout()
            startActivity(Intent(this@PaymentReport, LoginActivity::class.java))
            finish()
        })

        binding.drawerNavigationIcon.setOnClickListener {
            finish()
        }

        viewModel.isLoading.observe(this, androidx.lifecycle.Observer {
            if(it!!) progressBarHandler!!.show()
            if(!it) progressBarHandler!!.hide()
        })

        viewModel.paymentReportResponse.observe(this, androidx.lifecycle.Observer {
            paymentReport.clear()
            paymentReport.addAll(it!!.data!!.toMutableList())
            binding.theList.adapter!!.notifyDataSetChanged()
        })

        binding.FromDate.setOnClickListener {
            CalendarSheet().show(this) {
                title("Select From Date")
                selectionMode(SelectionMode.DATE)
                onPositive { dateStart, dateEnd ->
                    binding.FromDate.setText("${dateStart.get(Calendar.YEAR)}-${(dateStart.get(Calendar.MONTH) + 1).toString().padStart(2, '0')}-${dateStart.get(Calendar.DATE).toString().padStart(2, '0')}")
                }
            }
        }

        binding.ToDate.setOnClickListener {
            CalendarSheet().show(this) {
                title("Select To Date")
                selectionMode(SelectionMode.DATE)
                onPositive { dateStart, dateEnd ->
                    binding.ToDate.setText("${dateStart.get(Calendar.YEAR)}-${(dateStart.get(Calendar.MONTH) + 1).toString().padStart(2, '0')}-${dateStart.get(Calendar.DATE).toString().padStart(2, '0')}")
                }
            }
        }

        binding.theList.layoutManager = LinearLayoutManager(window.context)
        binding.theList.adapter = BaseRecyclerAdapter(window.context, object : IAdapterListener {
            override fun <T> clickListener(position: Int, model: T, view: View) {
                model as ArrayList<String>
            }

            override fun getViewHolder(parent: ViewGroup, viewType: Int): BaseViewHolder {
                if(viewType>-1 ){
                    return TheRecyclerAdapter(
                        DataBindingUtil.inflate(
                            LayoutInflater.from(parent.context),
                            R.layout.rv_table_4c_t1,
                            parent,
                            false
                        ), window.context, userDetails
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

        }, paymentReport as ArrayList)

        reload()
        binding.swipeToRefresh.setOnRefreshListener {
            binding.swipeToRefresh.isRefreshing = false
            reload()
        }
        updateFilterChipsView("")

        form {
            input(binding.FromDate) {
                conditional({ !binding.ToDate.text.isNullOrBlank() }) {
                    isNotEmpty()
                }
                onValue { view, value ->
                    if(value.value != null && !value.value.isNullOrEmpty() && value.value.toString().length > 1) FilterCriteria["date_from"] = "From: ${value.value.toString()}||${value.value.toString()}"
                }
            }
            input(binding.ToDate) {
                conditional({ !binding.FromDate.text.isNullOrBlank() }) {
                    isNotEmpty()
                }
                onValue { view, value ->
                    if(value.value != null && !value.value.isNullOrEmpty() && value.value.toString().length > 1) FilterCriteria["date_to"] = "To: ${value.value.toString()}||${value.value.toString()}"
                }
            }
            multiTaskingDropdownSpinnerView(binding.ResellerList) {
                onValue { view, value ->
                    if(value.value != null && !value.value.isNullOrEmpty()){
                        var mfsName = ""
                        val basic_store_list = Gson().fromJson<List<StoreInfo>>(
                            dataManager.preferencesHelper.get("basic_store_list", "[]"),
                            object : TypeToken<List<StoreInfo>>() {}.type
                        )

                        basic_store_list.forEach {
                            if(value.value.toString().equals(it.storeId))
                            {
                                mfsName = "${it.storeName}"
                            }
                        }

                        FilterCriteria["store_id"] = "Reseller: ${mfsName}||${value.value.toString()}"
                    }
                }
            }
            submitWith(binding.SearchButton) { result ->
                //hideKeyboard2()
                updateFilterChipsView("")
                binding.filterPanel.visibility = View.GONE
                //binding.clearAllBtn.visibility = if(!(FilterCriteria.size > 0)) View.GONE else View.VISIBLE
                reload()
            }
        }
    }

    fun toggleFilterPanel()
    {
        if(FilterCriteria.size > 0)
        {
            binding.filterGroup.visibility = View.VISIBLE
            binding.clearAllBtn.visibility = View.GONE
            binding.filterPanel.visibility = if(binding.filterPanel.visibility == View.VISIBLE) View.GONE else View.VISIBLE
        }
        else
        {
            binding.filterGroup.visibility = if(binding.filterGroup.visibility == View.VISIBLE) View.GONE else View.VISIBLE
            binding.filterPanel.visibility = binding.filterGroup.visibility
            //binding.clearAllBtn.visibility = if(!(FilterCriteria.size > 0)) View.GONE else View.VISIBLE
        }
    }

    fun updateFilterChipsView(removeKey:String)
    {
        binding.filterChips.removeAllViews()
        if(removeKey.length > 0) FilterCriteria.remove(removeKey)
        if(FilterCriteria.size == 0) clearAllFilter()
        FilterCriteria.forEach { (key, value) ->
            var chip = Chip(this)
            chip.setText(value.split("||")[0])
            chip.setCloseIconVisible((!value.split("||")[0].contains("From:") && !value.split("||")[0].contains("To:")))
            chip.setOnCloseIconClickListener {
                updateFilterChipsView(key)
                reload()
            }
            chip.setOnClickListener {
                toggleFilterPanel()
            }
            binding.filterChips.addView(chip)
        }
    }

    fun clearAllFilter(){
        FilterCriteria.clear()
        binding.filterChips.removeAllViews()
        binding.filterBtt.visibility = View.VISIBLE
        binding.filterPanel.visibility = View.GONE
        binding.filterGroup.visibility = View.GONE
        binding.FromDate.setText("")
        binding.FromDate.error = ""
        binding.ToDate.setText("")
        binding.ToDate.error = ""
        binding.ResellerList.clear()
        reload()
    }

    fun reload()
    {
        if(Constants.newInstance().USER_TYPE_STORE == userDetails.userType && !userDetails.permissionLists.contains("StoreController::list"))
        {
            FilterCriteria["store_id"] = "Reseller: Self||${userDetails.storeVendorId}"
        }

        var searchParams = HashMap<String, String>()
        FilterCriteria.forEach { (key, value) ->
            if(value.split("||").size > 1) searchParams[key] = value.split("||")[1]
        }
        searchParams["trans_type"] = "received_payment"
        viewModel.getPaymentReport(searchParams)
    }

    class TheRecyclerAdapter(itemView: ViewDataBinding, context: Context, val userDetails:UserProfileData) : BaseViewHolder(itemView.root) {

        var binding = itemView as RvTable4cT1Binding
        var mContext: Context = context

        override fun <T> onBind(position: Int, model: T, mCallback: IAdapterListener) {
            model as ArrayList<String>
            binding.col1.text = "${model[0]}"

            if(!model[1].isNullOrEmpty()) {
                if(Constants.newInstance().USER_TYPE_SUPER_ADMIN == userDetails.userType || Constants.newInstance().USER_TYPE_MANAGER == userDetails.userType)
                {
                    binding.col2.text = if(!model[1].contains("Total:")) "${model[1]}\nReseller: ${model[2]}\nParent: ${model[3]}\nNote: ${model[6]}" else "Total:"
                }
                else
                {
                    if(userDetails.permissionLists.contains("StoreController::list"))
                    {
                        binding.col2.text = if(!model[1].contains("Total:")) "${model[1]}\nReseller: ${model[2]}${if(!model[3].isNullOrEmpty()) "\nParent: ${model[3]}" else ""}\nNote:${model[6]}" else "Total:"
                    }
                    else
                    {
                        binding.col2.text = "${model[1]}\nNote:${model[6]}"
                    }
                }
            }else{
                binding.col2.text = ""
            }


            binding.col3.setText("${if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                Html.fromHtml("${model[4]}", Html.FROM_HTML_MODE_COMPACT)
            } else {
                Html.fromHtml("${model[4]}")
            }}")

            binding.col4.setText("${if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                Html.fromHtml("${model[5]}", Html.FROM_HTML_MODE_COMPACT)
            } else {
                Html.fromHtml("${model[5]}")
            }}")
        }
    }
}
