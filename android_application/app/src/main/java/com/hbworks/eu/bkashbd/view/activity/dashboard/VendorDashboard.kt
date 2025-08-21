package com.hbworks.eu.bkashbd.view.activity.dashboard

import android.content.ClipData
import android.content.ClipboardManager
import android.content.Context
import android.content.Intent
import android.content.res.ColorStateList
import android.os.Build
import android.os.Bundle
import android.text.Html
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.webkit.URLUtil
import androidx.core.content.ContextCompat
import androidx.core.view.GravityCompat
import androidx.databinding.DataBindingUtil
import androidx.databinding.ViewDataBinding
import androidx.lifecycle.ViewModelProviders
import androidx.recyclerview.widget.LinearLayoutManager
import coil.load
import com.afollestad.materialdialogs.MaterialDialog
import com.afollestad.vvalidator.form
import com.google.android.material.chip.Chip
import com.google.gson.Gson
import com.hbworks.eu.bkashbd.BuildConfig
import com.hbworks.eu.bkashbd.R
import com.hbworks.eu.bkashbd.data.model.MfsList
import com.hbworks.eu.bkashbd.data.prefence.PreferencesHelper
import com.hbworks.eu.bkashbd.databinding.ActivityVendorDashboardBinding
import com.hbworks.eu.bkashbd.databinding.RechargeReportVhBinding
import com.hbworks.eu.bkashbd.lib.MultiTaskingDropdownSpinner
import com.hbworks.eu.bkashbd.util.showToast
import com.hbworks.eu.bkashbd.view.activity.login.LoginActivity
import com.hbworks.eu.bkashbd.view.activity.login.UserProfileData
import com.hbworks.eu.bkashbd.view.activity.reports.MfsSummeryReport
import com.hbworks.eu.bkashbd.view.adapter.IAdapterListener
import com.hbworks.eu.bkashbd.view.base.BaseActivity
import com.hbworks.eu.bkashbd.view.base.BaseRecyclerAdapter
import com.hbworks.eu.bkashbd.view.base.BaseViewHolder
import com.hbworks.eu.bkashbd.view.common.EmptyViewHolder
import com.maxkeppeler.sheets.calendar.CalendarSheet
import com.maxkeppeler.sheets.calendar.SelectionMode
import com.maxkeppeler.sheets.input.InputSheet
import com.maxkeppeler.sheets.input.type.InputEditText
import com.sslwireless.crm.lib.multi_tasking_dropdown_spinner_form_validation.multiTaskingDropdownSpinnerView
import java.util.*


class VendorDashboard : BaseActivity() {

    private val binding: ActivityVendorDashboardBinding by lazy {
        ActivityVendorDashboardBinding.inflate(layoutInflater)
    }

    private lateinit var viewModel: DashboardViewModel
    private lateinit var userDetails:UserProfileData
    private var pendingRechargeRequestList:MutableList<ArrayList<String>> =  mutableListOf()
    private var mfsList:MutableList<MfsList> =  mutableListOf<MfsList>()

    private var FilterCriteria:LinkedHashMap<String, String> = LinkedHashMap<String, String>()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(binding.root)

        userDetails = dataManager.preferencesHelper.getUserInfo()

        this.viewModel = ViewModelProviders.of(this).get(DashboardViewModel::class.java)
    }

    override fun viewRelatedTask() {

        viewModel.updateFCM(PreferencesHelper(applicationContext).getFCMtoken())

        binding.drawerNavigationIcon.setOnClickListener {
            binding.navDrawerLayout.openDrawer(GravityCompat.START)
        }

        binding.logout.setOnClickListener {
            logout()
        }

        binding.filterBtt.setOnClickListener {
            if(FilterCriteria.size > 0)
            {
                binding.filterGroup.visibility = View.VISIBLE
                binding.clearAllBtn.visibility = View.VISIBLE
                binding.filterPanel.visibility = if(binding.filterPanel.visibility == View.VISIBLE) View.GONE else View.VISIBLE
            }
            else
            {
                binding.filterGroup.visibility = if(binding.filterGroup.visibility == View.VISIBLE) View.GONE else View.VISIBLE
                binding.filterPanel.visibility = binding.filterGroup.visibility
                binding.clearAllBtn.visibility = if(!(FilterCriteria.size > 0)) View.GONE else View.VISIBLE
            }
        }

        binding.clearAllBtn.setOnClickListener {
            clearAllFilter()
        }

        binding.dashboardDrawerNavigation.logoutButton.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            logout()
        }

        binding.dashboardDrawerNavigation.mfsSummeryBtn.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            startActivity(Intent(this@VendorDashboard, MfsSummeryReport::class.java))
        }

        /*binding.dashboardDrawerNavigation.rechargeGroupBtn.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            showToast("Work In Progress")
        }*/

        binding.dashboardDrawerNavigation.ChangePasswordButton.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            InputSheet().show(this@VendorDashboard) {
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
                        var data = HashMap<String, String>();
                        data["new_password"] = confirm_new_password
                        viewModel.updateUser(data)
                    }
                    else
                    {
                        showToast("Invalid Input. Please try again.")
                    }
                }
            }
        }

        viewModel.userUpdated.observe(this, androidx.lifecycle.Observer {
            if(it)
            {
                dataManager.preferencesHelper.prefLogout()
                MaterialDialog(this@VendorDashboard).show {
                    cancelable(false)
                    title(text = "Password Updated")
                    message(text = "Your password have been updated. Please Re-Login now.")
                    positiveButton(text = "Login Now") { dialog ->
                        startActivity(Intent(this@VendorDashboard, LoginActivity::class.java))
                        finish()
                    }
                }
            }
        })

        viewModel.errorMessage.observe(this, androidx.lifecycle.Observer {
            showToast(it!!)
        })

        viewModel.forceLogOut.observe(this, androidx.lifecycle.Observer {
            dataManager.preferencesHelper.prefLogout()
            startActivity(Intent(this@VendorDashboard, LoginActivity::class.java))
            finish()
        })

        viewModel.isLoading.observe(this, androidx.lifecycle.Observer {
            if(it!!) progressBarHandler!!.show()
            if(!it) progressBarHandler!!.hide()
        })

        binding.title.text = "Recharge Request"
        binding.dashboardDrawerNavigation.UserName.text = "Hello, ${userDetails.username}"
        binding.dashboardDrawerNavigation.UserEmail.text = "${userDetails.storePhoneNumber}"

        pendingRechargeRequestList.clear()

        binding.theList.layoutManager = LinearLayoutManager(window.context)
        binding.theList.adapter = BaseRecyclerAdapter(window.context, object : IAdapterListener {
            override fun <T> clickListener(position: Int, model: T, view: View) {
                model as ArrayList<String>
                var request_id = model[4].split("|")[0]
                when(position)
                {
                    1->{
                        // Approve Button
                        changeRequestStatus(request_id, "approve")
                    }
                    2->{
                        // Cancel/Reject Button
                        changeRequestStatus(request_id, "reject")
                    }
                    3->{
                        // Update Note
                        updateNoteSheet(request_id, model[10])
                    }
                    4->{
                        // Share button
                        val shareIntent = Intent()
                        shareIntent.action = Intent.ACTION_SEND
                        shareIntent.type="text/plain"
                        shareIntent.putExtra(Intent.EXTRA_TEXT, ("HelloDuniya\nNumber: ${model[2]}\nAmount: ${model[3]}\nType: ${model[1]}\nRefer ID: ${model[0]}\nReseller: ${model[8]}")+(if(model[9].length > 1) "\nParent: ${model[9]}" else ""))
                        startActivity(Intent.createChooser(shareIntent,"Share Details"))
                    }
                    5->{
                        // Copy Mobile Number
                        val clipboard: ClipboardManager =
                            getSystemService(CLIPBOARD_SERVICE) as ClipboardManager
                        val clip = ClipData.newPlainText("Mobile Number", "${model[2]}")
                        clipboard.setPrimaryClip(clip)
                        showToast("Mobile Number Copied Successfully")
                    }
                    6->{
                        // Lock Request Button
                        changeRequestStatus("${model[4].split("|")[0]}", "lock")
                    }
                }
            }

            override fun getViewHolder(parent: ViewGroup, viewType: Int): BaseViewHolder {
                if(viewType>-1 ){
                    return PendingRecyclerAdapter(
                        DataBindingUtil.inflate(
                            LayoutInflater.from(parent.context),
                            R.layout.recharge_report_vh,
                            parent,
                            false
                        ), window.context ,
                        mfsList
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

        }, pendingRechargeRequestList as ArrayList)


        viewModel.rechargeActivityResponse.observe(this, androidx.lifecycle.Observer {
            pendingRechargeRequestList.clear()
            mfsList.clear()
            pendingRechargeRequestList.addAll(it!!.data!!.toMutableList())
            mfsList.addAll(it.mfsList)
            binding.theList.adapter!!.notifyDataSetChanged()

            dataManager.preferencesHelper.put("basic_store_list", Gson().toJson(it.storeList))

            val items: MutableList<MultiTaskingDropdownSpinner.Items> = ArrayList()
            mfsList.forEach {
                val item: MultiTaskingDropdownSpinner.Items = MultiTaskingDropdownSpinner.Items()
                item.id = "${it.mfsId}"
                item.title = "${it.mfsName}"
                items.add(item)
            }
            binding.MfsList.addItems(items)
        })

        reload()
        binding.swipeToRefresh.setOnRefreshListener {
            binding.swipeToRefresh.isRefreshing = false
            reload()
        }

        viewModel.reloadList.observe(this, androidx.lifecycle.Observer {
            if(it){
                reload()
            }
        })

        if(!userDetails.logo.isNullOrBlank()){
            binding.dashboardDrawerNavigation.profileImage.load((if(URLUtil.isValidUrl(userDetails.logo)) "${BuildConfig.BASE_URL}${userDetails.logo}" else "${userDetails.logo}"))
        }

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

        addSearchValidation()

        /*Timer().scheduleAtFixedRate(object : TimerTask() {
            override fun run() {
                reload()
            }
        }, 0, 15000)*/
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
        binding.PhoneNumber.setText("")
        binding.MfsList.clear()
        reload()
    }

    fun addSearchValidation()
    {
        form {
            input(binding.FromDate) {
                conditional({ !binding.ToDate.text.isNullOrBlank() }) {
                    isNotEmpty()
                }
                onValue { view, value ->
                    if(value.value != null && !value.value.isNullOrEmpty() && value.value.toString().length > 1) FilterCriteria["date_from"] = "From ${value.value.toString()}||${value.value.toString()}"
                }
            }
            input(binding.ToDate) {
                conditional({ !binding.FromDate.text.isNullOrBlank() }) {
                    isNotEmpty()
                }
                onValue { view, value ->
                    if(value.value != null && !value.value.isNullOrEmpty() && value.value.toString().length > 1) FilterCriteria["date_to"] = "To ${value.value.toString()}||${value.value.toString()}"
                }
            }
            multiTaskingDropdownSpinnerView(binding.MfsList) {
                onValue { view, value ->
                    if(value.value != null && !value.value.isNullOrEmpty()){
                        var mfsName = ""
                        mfsList.forEach {
                            if(value.value.toString().equals(it.mfsId!!))
                            {
                                mfsName = "${it.mfsName}"
                            }
                        }

                        FilterCriteria["mfs_id"] = "MFS: ${mfsName}||${value.value.toString()}"
                    }
                }
            }
            input(binding.PhoneNumber) {
                onValue { view, value ->
                    if(value.value != null && !value.value.isNullOrEmpty()) FilterCriteria["phone_number"] = "Mobile: ${value.value.toString()}||${value.value.toString()}"
                }
            }
            submitWith(binding.SearchButton) { result ->
                //hideKeyboard2()
                updateFilterChipsView("")
                //binding.filterBtt.visibility = if(FilterCriteria.size > 0) View.GONE else View.VISIBLE
                binding.filterPanel.visibility = View.GONE
                binding.clearAllBtn.visibility = if(!(FilterCriteria.size > 0)) View.GONE else View.VISIBLE
                reload()
            }
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
            binding.filterChips.addView(chip)
        }
    }

    fun changeRequestStatus(request_id:String, status:String)
    {
        when(status)
        {
            "approve"->{
                InputSheet().show(this@VendorDashboard) {
                    title("Please put a note that about approve request")
                    with(InputEditText("new_note") {
                        required()
                        maxLines(20)
                        label("Note")
                        hint("Put Note Here")
                    })
                    onNegative {

                    }
                    onPositive("Approve") { result ->
                        val check = result.getString("new_note")
                        var postData = HashMap<String, String>()
                        postData["recharge_status"] = "approved"
                        postData["note"] = result.getString("new_note")!!
                        viewModel.approveRejectRequest(request_id, postData)
                    }
                }
            }
            "reject"->{
                InputSheet().show(this@VendorDashboard) {
                    title("Please put a note that about cancel request")
                    with(InputEditText("new_note") {
                        required()
                        maxLines(20)
                        label("Note")
                        hint("Put Note Here")
                    })
                    onNegative {

                    }
                    onPositive("Reject") { result ->
                        var postData = HashMap<String, String>()
                        postData["recharge_status"] = "rejected"
                        postData["note"] = result.getString("new_note")!!
                        viewModel.approveRejectRequest(request_id, postData)
                    }
                }
            }
            "lock"->{
                MaterialDialog(this@VendorDashboard).show {
                    title(text = "Lock Request")
                    message(text = "Are you sure about lock this request?")
                    negativeButton(text = "Cancel") { dialog ->
                        dialog.dismiss()
                    }
                    positiveButton(text = "Lock This") { dialog ->
                        var postData = HashMap<String, String>()
                        viewModel.lockUnlockRequest(request_id, true, postData)
                    }
                }
            }
        }
    }

    fun updateNoteSheet(request_id:String, existingNote:String)
    {
        InputSheet().show(this@VendorDashboard) {
            title("Update Note")
            with(InputEditText("new_note") {
                required()
                defaultValue(existingNote)
                maxLines(20)
                label("Note")
                hint("Put Note Here")
            })
            onNegative {}
            onPositive { result ->
                viewModel.updateNote(request_id, result.getString("new_note")!!)
            }
        }
    }

    fun reload()
    {
        var searchParams = HashMap<String, String>()
        FilterCriteria.forEach { (key, value) ->
            if(value.split("||").size > 1) searchParams[key] = value.split("||")[1]
        }
        viewModel.getRechargeActivity(searchParams)
    }

    fun logout()
    {
        MaterialDialog(this@VendorDashboard).show {
            title(text = "Logout")
            message(text = "Are you sure about logout?")
            negativeButton(text = "Cancel") { dialog ->
                dialog.dismiss()
            }
            positiveButton(text = "Log Out") { dialog ->
                dataManager.preferencesHelper.prefLogout()
                startActivity(Intent(this@VendorDashboard, LoginActivity::class.java))
                finish()
            }
        }
    }

    override fun onBackPressed() {
        clearAllFilter()
        logout()
    }

    class PendingRecyclerAdapter(itemView: ViewDataBinding, context: Context, val mfsList: MutableList<MfsList>) :
        BaseViewHolder(itemView.root) {

        var binding = itemView as RechargeReportVhBinding
        var mContext: Context = context

        override fun <T> onBind(position: Int, model: T, mCallback: IAdapterListener) {
            var mfsPlaceHolder = "https://via.placeholder.com/150"

            binding.lockBtn.visibility = View.GONE
            binding.CopyBtn.visibility = View.GONE
            binding.ShareBtn.visibility = View.GONE
            binding.UpdateBtn.visibility = View.GONE
            binding.CancelBtn.visibility = View.GONE
            binding.ApproveBtn.visibility = View.GONE
            binding.reInitBtn.visibility = View.GONE
            binding.unLockBtn.visibility = View.GONE

            model as ArrayList<String>

            mfsList.forEach {
                if(model[1].contains(it.mfsName!!))
                {
                    mfsPlaceHolder = "${BuildConfig.BASE_URL}${it.imagePath}"
                }
            }

            binding.mfsImage.load(mfsPlaceHolder)

            binding.trId.text = "#${model[0]}"
            var HHTTMMLL = ("<b>Mobile: ${model[2]}<br>[${model[3]}]</b><br><br>${model[1]}<br><br>Note: ${if(model[10] != "null" && model[10] != null) "${model[10]}" else ""}<br>Store:${model[8]}"+(if(model[9].length > 0) " [Parent Store: ${model[9]}]" else ""))
            binding.details.text = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                Html.fromHtml(HHTTMMLL, Html.FROM_HTML_MODE_COMPACT)
            } else {
                Html.fromHtml(HHTTMMLL)
            }

            binding.createdAt.text = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                Html.fromHtml("Created on: ${model[6]}", Html.FROM_HTML_MODE_COMPACT)
            } else {
                Html.fromHtml("Created on: ${model[6]}")
            }

            binding.updatedAt.text = "Last Updated on: ${model[7]}"
            binding.status.text = "${model[5]}"

            when(model[5])
            {
                "Approved"->{
                    binding.status.chipBackgroundColor = ColorStateList.valueOf(ContextCompat.getColor(mContext, R.color.md_green_c1))
                    binding.status.setTextColor(mContext.resources.getColor(R.color.white))

                    binding.CopyBtn.visibility = View.VISIBLE
                    //binding.ShareBtn.visibility = View.VISIBLE
                    binding.UpdateBtn.visibility = View.VISIBLE
                }
                "Rejected"->{
                    binding.status.chipBackgroundColor = ColorStateList.valueOf(ContextCompat.getColor(mContext, R.color.md_red_A700))
                    binding.status.setTextColor(mContext.resources.getColor(R.color.white))

                    binding.CopyBtn.visibility = View.VISIBLE
                    binding.UpdateBtn.visibility = View.VISIBLE
                }
                "Pending", "Requested"->{
                    binding.status.chipBackgroundColor = ColorStateList.valueOf(ContextCompat.getColor(mContext, R.color.md_grey_400))
                    binding.status.setTextColor(mContext.resources.getColor(R.color.black))

                    binding.lockBtn.visibility = View.VISIBLE
                    binding.CopyBtn.visibility = View.VISIBLE
                }
                "Progressing"->{
                    binding.status.chipBackgroundColor = ColorStateList.valueOf(ContextCompat.getColor(mContext, R.color.md_blue_700))
                    binding.status.setTextColor(mContext.resources.getColor(R.color.white))

                    binding.CopyBtn.visibility = View.VISIBLE
                    binding.CancelBtn.visibility = View.VISIBLE
                    binding.ApproveBtn.visibility = View.VISIBLE
                    binding.ShareBtn.visibility = View.VISIBLE
                }
            }

            binding.lockBtn.setOnClickListener {
                mCallback.clickListener(6, model, it.rootView)
            }
            binding.CopyBtn.setOnClickListener {
                mCallback.clickListener(5, model, it.rootView)
            }
            binding.ShareBtn.setOnClickListener {
                mCallback.clickListener(4, model, it.rootView)
            }
            binding.UpdateBtn.setOnClickListener {
                mCallback.clickListener(3, model, it.rootView)
            }
            binding.CancelBtn.setOnClickListener {
                mCallback.clickListener(2, model, it.rootView)
            }
            binding.ApproveBtn.setOnClickListener {
                mCallback.clickListener(1, model, it.rootView)
            }
        }
    }
}
