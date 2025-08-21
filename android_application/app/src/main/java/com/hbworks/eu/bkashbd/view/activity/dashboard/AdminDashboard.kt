package com.hbworks.eu.bkashbd.view.activity.dashboard

import android.content.Context
import android.content.Intent
import android.content.res.ColorStateList
import android.os.Build
import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.text.Html
import android.text.InputType
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.activity.viewModels
import androidx.core.content.ContextCompat
import androidx.core.text.HtmlCompat
import androidx.core.view.GravityCompat
import androidx.core.widget.doOnTextChanged
import androidx.databinding.DataBindingUtil
import androidx.databinding.ViewDataBinding
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import coil.load
import com.afollestad.materialdialogs.MaterialDialog
import com.afollestad.vvalidator.form
import com.github.florent37.inlineactivityresult.kotlin.startForResult
import com.google.android.material.chip.Chip
import com.google.gson.Gson
import com.google.gson.annotations.SerializedName
import com.google.gson.reflect.TypeToken
import com.hbworks.eu.bkashbd.BuildConfig
import com.hbworks.eu.bkashbd.R
import com.hbworks.eu.bkashbd.data.model.StoreInfo
import com.hbworks.eu.bkashbd.databinding.ActivityAdminDashboardBinding
import com.hbworks.eu.bkashbd.databinding.ActivityResellersListBinding
import com.hbworks.eu.bkashbd.databinding.RvResellerListBinding
import com.hbworks.eu.bkashbd.lib.MultiTaskingDropdownSpinner
import com.hbworks.eu.bkashbd.util.Constants
import com.hbworks.eu.bkashbd.util.showToast
import com.hbworks.eu.bkashbd.view.activity.login.LoginActivity
import com.hbworks.eu.bkashbd.view.activity.login.UserProfileData
import com.hbworks.eu.bkashbd.view.activity.reports.*
import com.hbworks.eu.bkashbd.view.adapter.IAdapterListener
import com.hbworks.eu.bkashbd.view.base.BaseActivity
import com.hbworks.eu.bkashbd.view.base.BaseRecyclerAdapter
import com.hbworks.eu.bkashbd.view.base.BaseViewHolder
import com.hbworks.eu.bkashbd.view.common.EmptyViewHolder
import com.maxkeppeler.sheets.input.InputSheet
import com.maxkeppeler.sheets.input.type.InputEditText
import com.maxkeppeler.sheets.input.type.InputRadioButtons
import com.sslwireless.crm.lib.multi_tasking_dropdown_spinner_form_validation.multiTaskingDropdownSpinnerView
import java.text.DecimalFormat
import java.util.LinkedHashMap

class AdminDashboard : BaseActivity() {
    lateinit var userDetails: UserProfileData
    private lateinit var viewModel: ReportViewModel
    private val viewModelDashboard: DashboardViewModel by viewModels()
    private var FilterCriteria: LinkedHashMap<String, String> = LinkedHashMap<String, String>()
    private var storeByID: HashMap<String, String> = HashMap<String, String>()

    private var resellerList: ArrayList<ArrayList<String>> = ArrayList<ArrayList<String>>()

    private val binding: ActivityAdminDashboardBinding by lazy {
        ActivityAdminDashboardBinding.inflate(layoutInflater)
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(binding.root)

        userDetails = dataManager.preferencesHelper.getUserInfo()
        this.viewModel = ViewModelProvider(this).get(ReportViewModel::class.java)
    }

    override fun viewRelatedTask() {

        binding.drawerNavigationIcon.setOnClickListener {
            binding.navDrawerLayout.openDrawer(GravityCompat.START)
        }

        binding.addNewBtt.setOnClickListener {
            startForResult(Intent(this@AdminDashboard, AddNewReseller::class.java)){
                reload()
            }.onFailed { result ->

            }
        }

        binding.dashboardDrawerNavigation.logoutButton.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            binding.filterPanel.visibility = View.GONE
            binding.filterGroup.visibility = binding.filterPanel.visibility
            logout()
        }

        binding.dashboardDrawerNavigation.mfsSummeryBtn.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            binding.filterPanel.visibility = View.GONE
            binding.filterGroup.visibility = binding.filterPanel.visibility
            startActivity(Intent(this@AdminDashboard, MfsSummeryReport::class.java))
        }

        binding.dashboardDrawerNavigation.rechargeGroupBtn.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            binding.filterPanel.visibility = View.GONE
            binding.filterGroup.visibility = binding.filterPanel.visibility
            startActivity(Intent(this@AdminDashboard, RechargeReport::class.java))
        }

        binding.dashboardDrawerNavigation.PaymentReportBtn.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            binding.filterPanel.visibility = View.GONE
            binding.filterGroup.visibility = binding.filterPanel.visibility
            startActivity(Intent(this@AdminDashboard, PaymentReport::class.java))
        }

        binding.dashboardDrawerNavigation.AddBalanceReportBtn.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            binding.filterPanel.visibility = View.GONE
            binding.filterGroup.visibility = binding.filterPanel.visibility
            startActivity(Intent(this@AdminDashboard, AddBalanceReport::class.java))
        }

        binding.dashboardDrawerNavigation.ChangePasswordButton.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            binding.filterPanel.visibility = View.GONE
            binding.filterGroup.visibility = binding.filterPanel.visibility
            InputSheet().show(this@AdminDashboard) {
                title("Change Your Password")
                with(InputEditText("old_password") {
                    required()
                    label("Old Password")
                    hint("Put your old password")
                })
                with(InputEditText("new_password") {
                    required()
                    label("New Password")
                    hint("Put your new password")
                })
                with(InputEditText("confirm_new_password") {
                    required()
                    label("Confirm New Password")
                    hint("Put your confirmed new password")
                })
                onNegative {}
                onPositive { result ->
                    val old_password = result.getString("old_password")
                    val new_password = result.getString("new_password")
                    val confirm_new_password = result.getString("confirm_new_password")

                    if(
                        (old_password != null && old_password!!.length > 2) &&
                        (new_password != null && new_password!!.length > 2) &&
                        (confirm_new_password != null && confirm_new_password!!.length > 2) &&
                        (new_password == confirm_new_password)
                    )
                    {
                        var data = java.util.HashMap<String, String>();
                        data["new_password"] = confirm_new_password
                        viewModelDashboard.updateUser(data)
                    }
                    else
                    {
                        showToast("Invalid Input. Please try again.")
                    }
                }
            }
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
            startActivity(Intent(this@AdminDashboard, LoginActivity::class.java))
            finish()
        })

        viewModel.isLoading.observe(this, androidx.lifecycle.Observer {
            if(it!!) progressBarHandler!!.show()
            if(!it) progressBarHandler!!.hide()
        })


        viewModel.resellerListResponse.observe(this, androidx.lifecycle.Observer {
            resellerList.clear()
            resellerList.addAll(it.data)
            rv()

            val items: MutableList<MultiTaskingDropdownSpinner.Items> = java.util.ArrayList()
            it.allStoreList.forEach {
                val item: MultiTaskingDropdownSpinner.Items = MultiTaskingDropdownSpinner.Items()
                item.id = "${it.storeId}"
                item.title = "${it.storeName}"
                items.add(item)
                storeByID["${it.storeId}"] = "${it.storeName}"
            }

            binding.ResellerList.addItems(items)

            dataManager.preferencesHelper.put("basic_store_list", Gson().toJson(it.allStoreList))
        })

        rv()

        reload()
        binding.swipeToRefresh.setOnRefreshListener {
            binding.swipeToRefresh.isRefreshing = false
            reload()
        }

        viewModel.reloadList.observe(this, androidx.lifecycle.Observer {
            reload()
        })

        binding.ResellerList.visibility = View.VISIBLE

        addSearchValidation()

        binding.dashboardDrawerNavigation.UserName.text = "Hello, ${userDetails.username}"
        binding.dashboardDrawerNavigation.UserEmail.text = "${userDetails.userType!!.split("_").joinToString(separator = " ").capitalizeWords().trim()}"
    }

    fun rv()
    {
        binding.theList.layoutManager = LinearLayoutManager(window.context)

        binding.theList.layoutManager = LinearLayoutManager(window.context)
        binding.theList.adapter = BaseRecyclerAdapter(window.context, object : IAdapterListener {
            override fun <T> clickListener(position: Int, model: T, view: View) {
                model as ArrayList<String>
                var __resellerBaseInfo = model[7].split("|")
                when(position)
                {
                    0->{
                        var rate = "0"
                        userDetails.currencyConversionsList.forEach {
                            if(__resellerBaseInfo[3].equals(it.type))
                            {
                                rate = "${it.convAmount}"
                            }
                        }

                        // Add New balance
                        var bs = AddBalanceBottomSheet()
                        bs.setup("Add Balance ${model[2].split("\n")[0]}", rate, __resellerBaseInfo[4], userDetails.storeBaseCurrency!!).
                        setBottomDialogListener(object :
                            AddBalanceBottomSheet.IBottomSheetDialogClicked{
                            override fun onYesPressed(info: HashMap<String, String>) {
                                info["store_id"] = "${__resellerBaseInfo[0]}"
                                if(!info.containsKey("note")) info["note"] = ""
                                MaterialDialog(this@AdminDashboard).show {
                                    title(text = "Confirm add balance")
                                    message(text = "Are you sure to add balance ${userDetails.storeBaseCurrency!!.uppercase()} ${
                                        DecimalFormat("0.00").format(info["master_add_balance"]!!.toDouble())}/= [Euro ${info["euro_amount"]}]")
                                    negativeButton(text = "Cancel") { dialog ->
                                        dialog.dismiss()
                                    }
                                    positiveButton(text = "Add Balance Now") { dialog ->
                                        viewModel.addResellerBalance(info)
                                    }
                                }
                            }
                            override fun onNoPressed() {}
                        }).show(supportFragmentManager, bs.tag)
                    }
                    1->{
                        // Change Status
                        InputSheet().show(this@AdminDashboard) {
                            title("Change status ${model[2].split("\n")[0]}")
                            with(InputRadioButtons("new_status") {
                                required()
                                label("New Status")
                                selected((if(__resellerBaseInfo[1] == "enabled") 0 else 1))
                                options(mutableListOf("Enable", "Disable"))
                            })
                            onNegative {}
                            onPositive { result ->
                                var info = HashMap<String, String>()
                                info["store_id"] = "${__resellerBaseInfo[0]}"
                                info["status"] = (if(result.getInt("new_status") == 0) "enabled" else "disabled")
                                MaterialDialog(this@AdminDashboard).show {
                                    title(text = "Change status ${model[2].split("\n")[0]}")
                                    message(text = "Are you sure?")
                                    negativeButton(text = "Cancel") { dialog ->
                                        dialog.dismiss()
                                    }
                                    positiveButton(text = "Yes") { dialog ->
                                        viewModel.updateResellerStatus(info)
                                    }
                                }
                            }
                        }
                    }
                    2->{
                        // Add Return
                        InputSheet().show(this@AdminDashboard) {
                            title("Payment Returned from ${model[2].split("\n")[0]}")
                            with(InputEditText("new_balance") {
                                required()
                                inputType(InputType.TYPE_CLASS_NUMBER)
                                label("Amount")
                                hint("Put Returned Amount")
                            })
                            with(InputEditText("note") {
                                inputType(InputType.TYPE_CLASS_TEXT)
                                label("Note")
                                hint("Put note here")
                            })
                            onNegative {}
                            onPositive { result ->
                                var info = HashMap<String, String>()
                                info["store_id"] = "${__resellerBaseInfo[0]}"
                                if(result.containsKey("note") && result.getString("note") != null){
                                    info["note"] = result.getString("note")!!
                                } else info["note"] = ""
                                info["new_balance"] = result.getString("new_balance")!!

                                MaterialDialog(this@AdminDashboard).show {
                                    title(text = "Payment Returned from ${model[2].split("\n")[0]}")
                                    message(text = "Are you sure?")
                                    negativeButton(text = "Cancel") { dialog ->
                                        dialog.dismiss()
                                    }
                                    positiveButton(text = "Yes") { dialog ->
                                        viewModel.addResellerPaymentReturned(info)
                                    }
                                }
                            }
                        }
                    }
                    3->{
                        // Add Payment Received
                        InputSheet().show(this@AdminDashboard) {
                            title("Add Payment Received from ${model[2].split("\n")[0]}")
                            with(InputEditText("euro_amount") {
                                required()
                                inputType(InputType.TYPE_CLASS_NUMBER)
                                label("Euro Amount (€)")
                                hint("Put Euro Amount")
                            })
                            with(InputEditText("note") {
                                inputType(InputType.TYPE_CLASS_TEXT)
                                label("Note")
                                hint("Put note here")
                            })
                            onNegative {}
                            onPositive { result ->
                                var info = HashMap<String, String>()
                                info["store_id"] = "${__resellerBaseInfo[0]}"
                                if(result.containsKey("note") && result.getString("note") != null){
                                    info["note"] = result.getString("note")!!
                                } else info["note"] = ""
                                info["euro_amount"] = result.getString("euro_amount")!!

                                MaterialDialog(this@AdminDashboard).show {
                                    title(text = "Payment Received from ${model[2].split("\n")[0]}")
                                    message(text = "Are you sure?")
                                    negativeButton(text = "Cancel") { dialog ->
                                        dialog.dismiss()
                                    }
                                    positiveButton(text = "Yes") { dialog ->
                                        viewModel.addResellerPaymentReceived(info)
                                    }
                                }
                            }
                        }
                    }
                    4->{
                        // Change Password
                        InputSheet().show(this@AdminDashboard) {
                            title("Change Password of ${model[2].split("\n")[0]}")
                            with(InputEditText("new_password") {
                                required()
                                inputType(InputType.TYPE_CLASS_NUMBER)
                                label("New Password")
                                hint("Put New Password")
                            })
                            onNegative {}
                            onPositive { result ->
                                //viewModel.updateCurrencyConversionRate(result.getString("conversion_rate")!!)
                            }
                        }
                    }
                }
            }

            override fun getViewHolder(parent: ViewGroup, viewType: Int): BaseViewHolder {
                if(viewType>-1 ){
                    return TheRecyclerAdapter(
                        DataBindingUtil.inflate(
                            LayoutInflater.from(parent.context),
                            R.layout.rv_reseller_list,
                            parent,
                            false
                        ), window.context, userDetails.permissionLists
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

        }, resellerList as ArrayList)
    }

    fun logout()
    {
        MaterialDialog(this@AdminDashboard).show {
            title(text = "Logout")
            message(text = "Are you sure about logout?")
            negativeButton(text = "Cancel") { dialog ->
                dialog.dismiss()
            }
            positiveButton(text = "Log Out") { dialog ->
                dataManager.preferencesHelper.prefLogout()
                startActivity(Intent(this@AdminDashboard, LoginActivity::class.java))
                finish()
            }
        }
    }

    fun addSearchValidation()
    {
        form {
            input(binding.SearchBy) {
                onValue { view, value ->
                    if(value.value != null && !value.value.isNullOrEmpty() && value.value.toString().length > 1) FilterCriteria["search_by"] = "Search By: ${value.value.toString()}||${value.value.toString()}"
                }
            }
            multiTaskingDropdownSpinnerView(binding.ResellerList) {
                onValue { view, value ->
                    if(value.value != null && !value.value.isNullOrEmpty()){

                        FilterCriteria["parent_store_id"] = "Reseller: ${storeByID[value.value]}||${value.value.toString()}"

                    }
                }
            }
            submitWith(binding.SearchButton) { result ->
                updateFilterChipsView("")
                binding.filterPanel.visibility = View.GONE
                binding.clearAllBtn.visibility = if(!(FilterCriteria.size > 0)) View.GONE else View.VISIBLE
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
            chip.setCloseIconVisible(true)
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
        binding.SearchBy.setText("")
        binding.ResellerList.clear()
        reload()
    }

    fun reload()
    {
        var searchParams = HashMap<String, String>()
        FilterCriteria.forEach { (key, value) ->
            if(value.split("||").size > 1) searchParams[key] = value.split("||")[1]
        }
        viewModel.getAllResellerList(searchParams)
    }

    override fun onBackPressed() {
        if(binding.filterPanel.visibility == View.VISIBLE){
            binding.filterPanel.visibility = View.GONE
        } else finish()
    }

    class TheRecyclerAdapter(itemView: ViewDataBinding, context: Context, val permissions:ArrayList<String>) :
        BaseViewHolder(itemView.root) {

        var binding = itemView as RvResellerListBinding
        var mContext: Context = context

        override fun <T> onBind(position: Int, model: T, mCallback: IAdapterListener) {
            model as ArrayList<String>

            //[1,"","best12 [best12]  (Pin: 1234)","BDT 0.00","<span style='color: green'> &euro;0.00<\/span>","enabled","03\/01\/2022","621e447b2e3a6a99b44b69b2677fc|enabled|0.000|bdt|2.000"]

            binding.title.setText("${androidx.core.text.HtmlCompat.fromHtml(model[2], HtmlCompat.FROM_HTML_MODE_LEGACY).toString()}")

            binding.resellerLogo.load((if(model[1].isNullOrEmpty()) "https://via.placeholder.com/150" else ("${BuildConfig.BASE_URL}${model[1]}")))

            binding.createdAt.setText("Created: ${model[6]}")
            binding.status.setText("${model[5].uppercase()}")

            binding.balance.setText("${model[3]}")

            var dueEuro = "${if(model[4].toFloat() < 0) "&euro; ${DecimalFormat("0.00").format((model[4].toFloat() * (-1)).toDouble())}" else "&euro; ${DecimalFormat("0.00").format(model[4].toDouble())}"}"

            binding.euroBalance.setText("${if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                Html.fromHtml(dueEuro, Html.FROM_HTML_MODE_COMPACT)
            } else {
                Html.fromHtml(dueEuro)
            }}")

            if(model[4].toFloat() < 0) binding.euroBalance.setTextColor(mContext.resources.getColor(
                R.color.md_green_c1))
            if(model[4].toFloat() >= 0) binding.euroBalance.setTextColor(mContext.resources.getColor(
                R.color.md_red_A700))

            when(model[5])
            {
                "enabled"->{
                    binding.status.chipBackgroundColor = ColorStateList.valueOf(ContextCompat.getColor(mContext, R.color.md_green_c1))
                    binding.status.setTextColor(mContext.resources.getColor(R.color.white))
                }
                "disabled"->{
                    binding.status.chipBackgroundColor = ColorStateList.valueOf(ContextCompat.getColor(mContext, R.color.md_red_A700))
                    binding.status.setTextColor(mContext.resources.getColor(R.color.white))
                }
            }

            binding.addBalanceBtt.setOnClickListener {
                mCallback.clickListener(0, model, it.rootView)
            }
            binding.ChangeStatusBtt.setOnClickListener {
                mCallback.clickListener(1, model, it.rootView)
            }
            binding.AddReturnBtt.setOnClickListener { mCallback.clickListener(2, model, it.rootView) }
            binding.AddPaymentReceiveBtt.setOnClickListener { mCallback.clickListener(3, model, it.rootView) }

            binding.ChangePasswordButton.visibility = View.GONE
            binding.ChangePasswordButton.setOnClickListener { mCallback.clickListener(4, model, it.rootView) }
        }
    }





    data class ResellerListResponse (
        @SerializedName("right_now"    ) var rightNow     : String?                   = null,
        @SerializedName("timestamp"    ) var timestamp    : Int?                      = null,
        @SerializedName("success"      ) var success      : Boolean?                  = null,
        @SerializedName("data"         ) var data         : ArrayList<ArrayList<String>> = arrayListOf(),
        @SerializedName("store_list"   ) var storeList    : ArrayList<StoreList>      = arrayListOf(),
        @SerializedName("allStoreList" ) var allStoreList : ArrayList<StoreList>   = arrayListOf()
    )


    data class StoreList (
        @SerializedName("store_id"   ) var storeId   : String? = null,
        @SerializedName("store_name" ) var storeName : String? = null
    )

    fun String.capitalizeWords(): String = split(" ").map { it.capitalize() }.joinToString(" ")
}
