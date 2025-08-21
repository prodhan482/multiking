package com.hbworks.eu.bkashbd.view.activity.dashboard

import android.content.Context
import android.content.Intent
import android.content.res.ColorStateList
import android.database.Cursor
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.provider.ContactsContract.CommonDataKinds
import android.text.Html
import android.text.InputType
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.webkit.URLUtil
import androidx.activity.viewModels
import androidx.core.content.ContextCompat
import androidx.core.view.GravityCompat
import androidx.core.widget.doOnTextChanged
import androidx.databinding.DataBindingUtil
import androidx.databinding.ViewDataBinding
import androidx.lifecycle.MutableLiveData
import androidx.recyclerview.widget.GridLayoutManager
import androidx.recyclerview.widget.LinearLayoutManager
import coil.load
import com.afollestad.materialdialogs.MaterialDialog
import com.afollestad.vvalidator.form
import com.github.florent37.inlineactivityresult.kotlin.startForResult
import com.google.android.material.chip.Chip
import com.google.android.material.snackbar.Snackbar
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken
import com.hbworks.eu.bkashbd.BuildConfig
import com.hbworks.eu.bkashbd.R
import com.hbworks.eu.bkashbd.data.model.*
import com.hbworks.eu.bkashbd.data.prefence.PreferencesHelper
import com.hbworks.eu.bkashbd.databinding.*
import com.hbworks.eu.bkashbd.lib.MultiTaskingDropdownSpinner
import com.hbworks.eu.bkashbd.util.Constants
import com.hbworks.eu.bkashbd.util.showToast
import com.hbworks.eu.bkashbd.view.activity.login.LoginActivity
import com.hbworks.eu.bkashbd.view.activity.login.UserCurrentBalance
import com.hbworks.eu.bkashbd.view.activity.login.UserProfileData
import com.hbworks.eu.bkashbd.view.activity.reports.AddBalanceReport
import com.hbworks.eu.bkashbd.view.activity.reports.MfsSummeryReport
import com.hbworks.eu.bkashbd.view.activity.reports.PaymentReport
import com.hbworks.eu.bkashbd.view.activity.reports.ResellersList
import com.hbworks.eu.bkashbd.view.adapter.IAdapterListener
import com.hbworks.eu.bkashbd.view.base.BaseActivity
import com.hbworks.eu.bkashbd.view.base.BaseRecyclerAdapter
import com.hbworks.eu.bkashbd.view.base.BaseViewHolder
import com.hbworks.eu.bkashbd.view.common.EmptyViewHolder
import com.maxkeppeler.sheets.calendar.CalendarSheet
import com.maxkeppeler.sheets.calendar.SelectionMode
import com.maxkeppeler.sheets.info.InfoSheet
import com.maxkeppeler.sheets.input.InputSheet
import com.maxkeppeler.sheets.input.type.InputEditText
import com.maxkeppeler.sheets.input.type.InputRadioButtons
import com.sslwireless.crm.lib.multi_tasking_dropdown_spinner_form_validation.multiTaskingDropdownSpinnerView
import java.text.DecimalFormat
import java.util.*
import java.util.regex.Pattern


class StoreDashboard : BaseActivity() {

    private val binding: ActivityStoreDashboardBinding by lazy {
        ActivityStoreDashboardBinding.inflate(layoutInflater)
    }

    private val viewModel: DashboardViewModel by viewModels()

    private var userDetails: UserProfileData? = null
    private var userCurrentBalance: UserCurrentBalance? = null
    private var rechargeRequestList:MutableList<ArrayList<String>> =  mutableListOf()
    private var mfsList:MutableList<MfsList> =  mutableListOf<MfsList>()
    private var mfsPackageList:MutableList<MfsPackageList> =  mutableListOf<MfsPackageList>()
    private var mfsListById:HashMap<String, MfsList> =  HashMap<String, MfsList>()
    private var euroServiceChargeListMobile : MutableList<EuroServiceChargeList>? = null
    private var euroServiceChargeListMfs : MutableList<EuroServiceChargeList>? = null
    var euroServiceChargeList : MutableList<EuroServiceChargeList> = mutableListOf()
    var storeMfsSlab: ArrayList<StoreCommissionPercent> = arrayListOf()

    var selectedMfs: MutableLiveData<MfsList?> =  MutableLiveData<MfsList?>()
    private var selectedCurrencySendMoney: MutableLiveData<String> =  MutableLiveData<String>()
    private var selectedChargeTypeSendMoney: MutableLiveData<String> =  MutableLiveData<String>()

    private var FilterCriteria: LinkedHashMap<String, String> = LinkedHashMap<String, String>()

    var submissionRechargeRequest = DashboardViewModel.SubmitRechargeRequest()
    var PreSelectedPackageID = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(binding.root)

        userDetails = dataManager.preferencesHelper.getUserInfo()
        userCurrentBalance = dataManager.preferencesHelper.getUserCurrentBalance()

        //this.viewModel = ViewModelProviders.of(this).get(DashboardViewModel::class.java)
    }

    override fun viewRelatedTask()
    {
        dashboardInit()
        rechargeWindowInit()

        binding.filterBtt.visibility = View.GONE

        binding.ShowRechargeWindowFavBttn.setOnClickListener {
            binding.rechargeScreenView.root.visibility = View.VISIBLE
            //binding.ShowRechargeWindowFavBttn.visibility = View.GONE
        }
    }

    fun packageWindowInit()
    {
        val populateList = mutableListOf<MfsPackageList>()
        mfsPackageList.forEach {
            if(it.mfsDetails != null) populateList.add(it)
        }

        binding.packageScreenView.TheCloseButton.setOnClickListener {
            binding.packageScreenView.root.visibility = View.GONE
            binding.mfsBigScreenView.root.visibility = View.VISIBLE
        }
        binding.packageScreenView.skipWindow.setOnClickListener {
            binding.packageScreenView.root.visibility = View.GONE
            binding.mfsBigScreenView.root.visibility = View.VISIBLE
        }

        binding.packageScreenView.theList.layoutManager = LinearLayoutManager(window.context)
        binding.packageScreenView.theList.adapter = BaseRecyclerAdapter(window.context, object : IAdapterListener {
            override fun <T> clickListener(position: Int, model: T, view: View) {
                model as MfsPackageList
                MaterialDialog(this@StoreDashboard).show {
                    title(text = model.packageName)
                    message(text = "Do you want to try this offer?")
                    negativeButton(text = "Cancel") { dialog ->
                        dialog.dismiss()
                    }
                    positiveButton(text = "Yes") { dialog ->
                        dialog.dismiss()

                        selectedMfs.value = model.mfsDetails
                        PreSelectedPackageID = model.rowId!!
                        submissionRechargeRequest.selected_mfs_package = model.rowId!!
                        submissionRechargeRequest.selected_mfs_package_name = model.packageName

                        rechargeCalculateMfsPackageCommission()
                        rechargeUpdateMfsPackages()

                        binding.packageScreenView.root.visibility = View.GONE
                        binding.rechargeScreenView.root.visibility = View.VISIBLE
                    }
                }
            }

            override fun getViewHolder(parent: ViewGroup, viewType: Int): BaseViewHolder {
                if(viewType>-1 ){
                    return PackegeDetailsAdapter(
                        DataBindingUtil.inflate(
                            LayoutInflater.from(parent.context),
                            R.layout.package_screen_rv,
                            parent,
                            false
                        ), window.context
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



        }, populateList as ArrayList)
    }

    fun prepareMfsWindow()
    {
        binding.mfsBigScreenView.TheCloseButton.setOnClickListener {
            binding.mfsBigScreenView.root.visibility = View.GONE
            binding.filterBtt.visibility = View.VISIBLE
        }

        val sendMoney = arrayListOf<MfsList>()
        val mobileRecharge = arrayListOf<MfsList>()

        mfsList.forEach {
            if(it.mfsType == "financial_transaction") sendMoney.add(it)
            if(it.mfsType != "financial_transaction") mobileRecharge.add(it)
        }

        binding.mfsBigScreenView.theList1.layoutManager = GridLayoutManager(window.context, 3, LinearLayoutManager.VERTICAL, false)
        binding.mfsBigScreenView.theList1.adapter = BaseRecyclerAdapter(window.context, object : IAdapterListener {
            override fun <T> clickListener(position: Int, model: T, view: View) {
                model as MfsList
                PreSelectedPackageID = ""
                selectedMfs.value = model
                binding.mfsBigScreenView.root.visibility = View.GONE
                binding.rechargeScreenView.root.visibility = View.VISIBLE
                binding.filterBtt.visibility = View.VISIBLE
                binding.rechargeScreenView.t1.text = "Send Money"
            }

            override fun getViewHolder(parent: ViewGroup, viewType: Int): BaseViewHolder {
                if(viewType>-1 ){
                    return ImageBoxAdapterT2(
                        DataBindingUtil.inflate(
                            LayoutInflater.from(parent.context),
                            R.layout.rv_recharge_screen_mfs_box_t2,
                            parent,
                            false
                        ), window.context
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

        }, sendMoney as ArrayList)

        binding.mfsBigScreenView.theList2.layoutManager = GridLayoutManager(window.context, 3, LinearLayoutManager.VERTICAL, false)
        binding.mfsBigScreenView.theList2.adapter = BaseRecyclerAdapter(window.context, object : IAdapterListener {
            override fun <T> clickListener(position: Int, model: T, view: View) {
                model as MfsList
                PreSelectedPackageID = ""
                selectedMfs.value = model
                binding.rechargeScreenView.t1.text = "Mobile Recharge"
                binding.mfsBigScreenView.root.visibility = View.GONE
                binding.rechargeScreenView.root.visibility = View.VISIBLE
            }

            override fun getViewHolder(parent: ViewGroup, viewType: Int): BaseViewHolder {
                if(viewType>-1 ){
                    return ImageBoxAdapterT2(
                        DataBindingUtil.inflate(
                            LayoutInflater.from(parent.context),
                            R.layout.rv_recharge_screen_mfs_box_t2,
                            parent,
                            false
                        ), window.context
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

        }, mobileRecharge as ArrayList)
    }

    fun rechargeWindowInit()
    {
        binding.rechargeScreenView.DetailedInfo.visibility = View.GONE
        binding.rechargeScreenView.doRequest.visibility = View.GONE
        binding.rechargeScreenView.view9.visibility = View.GONE
        binding.rechargeScreenView.bottomInfo.visibility = View.GONE

        binding.rechargeScreenView.TheCloseButton.setOnClickListener {
            MaterialDialog(this@StoreDashboard).show {
                title(text = "Cancel Recharge")
                message(text = "Are you sure about cancel it ?")
                negativeButton(text = "Skip") { dialog ->
                    dialog.dismiss()
                }
                positiveButton(text = "Cancel It") { dialog ->
                    binding.rechargeScreenView.root.visibility = View.GONE
                    //binding.ShowRechargeWindowFavBttn.visibility = View.VISIBLE
                    rechargeWindowReset1()
                }
            }
        }

        binding.rechargeScreenView.selectedMFSHolder.setOnClickListener {

            val optionList = mutableListOf<String>()
            mfsList.forEach {
                optionList.add(it.mfsName!!)
            }

            InputSheet().show(this@StoreDashboard) {
                title("Select a Service")
                with(InputRadioButtons("service_index") {
                    required()
                    label("Select a service by which you want to send money.")
                    options(optionList)
                })
                onNegative {}
                onPositive { result ->
                    val service_index = result.getInt("service_index")
                    selectedMfs.value = mfsList[service_index]
                }
            }
        }

        binding.rechargeScreenView.pickerButton.setOnClickListener {
            val intent = Intent(Intent.ACTION_PICK)
            intent.type = CommonDataKinds.Phone.CONTENT_TYPE
            startForResult(intent){
                val contactUri: Uri? = it.data!!.data
                println("sdjhfkjhsdkjf ${contactUri!!.toString()}")
                val cursor: Cursor? = contentResolver.query(contactUri, null, null, null, null)
                cursor!!.moveToFirst()
                val column: Int = cursor.getColumnIndex(CommonDataKinds.Phone.NUMBER)
                var phoneNumber: String = cursor.getString(column)
                phoneNumber = phoneNumber.replace("+88", "").filter {it in '0'..'9'}
                if(phoneNumber.isNotEmpty()) binding.rechargeScreenView.MobileNumber.setText(
                    phoneNumber
                )

            }.onFailed { result ->

            }
        }

        /*binding.rechargeScreenView.theList.layoutManager = GridLayoutManager(window.context, 5, LinearLayoutManager.VERTICAL, false)
        binding.rechargeScreenView.theList.adapter = BaseRecyclerAdapter(window.context, object : IAdapterListener {
            override fun <T> clickListener(position: Int, model: T, view: View) {
                model as MfsList
                if(selectedMfs.value == null)
                {
                    selectedMfs.value = model
                }
                else
                {
                    MaterialDialog(this@StoreDashboard).show {
                        title(text = "Change Service")
                        message(text = "Are you sure about change service?")
                        negativeButton(text = "Cancel") { dialog ->
                            dialog.dismiss()
                        }
                        positiveButton(text = "Change") { dialog ->
                            selectedMfs.value = model
                        }
                    }
                }
            }

            override fun getViewHolder(parent: ViewGroup, viewType: Int): BaseViewHolder {
                if(viewType>-1 ){
                    return ImageBoxAdapter(
                        DataBindingUtil.inflate(
                            LayoutInflater.from(parent.context),
                            R.layout.rv_recharge_screen_mfs_box,
                            parent,
                            false
                        ), window.context
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

        }, mfsList as ArrayList)*/

        binding.rechargeScreenView.receiverType.setListener(object : MultiTaskingDropdownSpinner.MultiTaskingBottomSheetDropdownListener{
            override fun onSelect(item: MultiTaskingDropdownSpinner.Items, position: Int) {
                submissionRechargeRequest.mfs_type = item.id
            }
            override fun onMultiSelect(selectedItem: MutableList<MultiTaskingDropdownSpinner.Items>) {}
        })

        binding.rechargeScreenView.packagesType.setListener(object : MultiTaskingDropdownSpinner.MultiTaskingBottomSheetDropdownListener{
            override fun onSelect(item: MultiTaskingDropdownSpinner.Items, position: Int) {
                submissionRechargeRequest.selected_mfs_package = item.id
                submissionRechargeRequest.selected_mfs_package_name = item.title
                rechargeCalculateMfsPackageCommission()
            }
            override fun onMultiSelect(selectedItem: MutableList<MultiTaskingDropdownSpinner.Items>) {}
        })

        binding.rechargeScreenView.SendAmountChargeTypeBtt.setOnClickListener {
            InputSheet().show(this@StoreDashboard) {
                title("Select Send Amount Charge Type")
                with(InputRadioButtons("charge_type") {
                    required()
                    label("Select Send Amount Charge Type")
                    selected((if(selectedChargeTypeSendMoney.value == "with_charge") 0 else 1))
                    options(mutableListOf<String>("With Charge", "Without Charge"))
                })
                onNegative {}
                onPositive { result ->
                    val charge_type = result.getInt("charge_type")
                    selectedChargeTypeSendMoney.value = mutableListOf<String>("with_charge", "without_charge")[charge_type]
                }
            }
        }

        binding.rechargeScreenView.SendAmountTypeBtt.setOnClickListener {
            InputSheet().show(this@StoreDashboard) {
                title("Select Send Amount Currency Type")
                with(InputRadioButtons("currency_type") {
                    required()
                    selected((if(selectedCurrencySendMoney.value === "euro") 0 else 1))
                    label("Select Send Amount Currency Type")
                    options(mutableListOf<String>("euro".uppercase(),
                        (userDetails?.storeBaseCurrency?:"").uppercase()
                    ))
                })
                onNegative {}
                onPositive { result ->
                    val charge_type = result.getInt("currency_type")
                    selectedCurrencySendMoney.value = mutableListOf<String>("euro",
                        userDetails?.storeBaseCurrency?:""
                    )[charge_type]
                }
            }
        }

        binding.rechargeScreenView.ConversionRateSetBtn.setOnClickListener {
            InputSheet().show(this@StoreDashboard) {
                title("Update Conversion Rate")
                with(InputEditText("conversion_rate") {
                    required()
                    inputType(InputType.TYPE_CLASS_NUMBER)
                    defaultValue(binding.rechargeScreenView.ConversionRate.text.toString())
                    label("Conversion Rate (1 € = ? ${userDetails?.storeBaseCurrency!!})")
                    hint("Put your New Conversion Rate")
                })
                onNegative {}
                onPositive { result ->
                    viewModel.updateCurrencyConversionRate(result.getString("conversion_rate")!!)
                }
            }
        }

        binding.rechargeScreenView.ServiceChargeSetBtn.setOnClickListener {
            binding.StoreServiceChargesFragmentView.visibility = View.VISIBLE
            binding.rechargeScreenView.root.visibility = View.GONE
        }

        selectedCurrencySendMoney.observe(this) {

            submissionRechargeRequest.sending_currency = it

            binding.rechargeScreenView.SendAmountType.text = it.uppercase().trim()
            binding.rechargeScreenView.ServiceChargeCurrencyType.text = it.uppercase().trim()
            binding.rechargeScreenView.ConversionRateCurrencyType.text = it.uppercase().trim()

            when (it) {
                (userDetails?.storeBaseCurrency?:"BDT") -> {
                    binding.rechargeScreenView.SendAmountType.text = it.uppercase().trim()

                    binding.rechargeScreenView.SendAmountChargeTypeBtt.visibility = View.GONE
                    binding.rechargeScreenView.SendAmountChargeType.visibility = View.GONE
                    binding.rechargeScreenView.ReceiveAmountHoldBase.visibility = View.VISIBLE

                    binding.rechargeScreenView.ConversionRateHoldBase.visibility = View.GONE
                    binding.rechargeScreenView.ServiceChargeHoldBase.visibility = View.GONE
                }

                else -> {
                    binding.rechargeScreenView.SendAmountType.text = it.uppercase().trim()

                    binding.rechargeScreenView.SendAmountChargeTypeBtt.visibility = View.VISIBLE
                    binding.rechargeScreenView.SendAmountChargeType.visibility = View.VISIBLE
                    binding.rechargeScreenView.ReceiveAmountHoldBase.visibility = View.GONE

                    binding.rechargeScreenView.ConversionRateHoldBase.visibility = View.VISIBLE
                    binding.rechargeScreenView.ServiceChargeHoldBase.visibility = View.VISIBLE

                    rechargeCalculateReceiveMoney()
                }
            }
        }

        selectedChargeTypeSendMoney.observe(this, androidx.lifecycle.Observer {
            binding.rechargeScreenView.SendAmountChargeType.text = it.split("_").joinToString(separator = " ").capitalizeWords().trim()
            submissionRechargeRequest.send_money_type = it
            if(!submissionRechargeRequest.send_money.isNullOrEmpty()) rechargeCalculateReceiveMoney()
        })

        binding.rechargeScreenView.SendAmount.doOnTextChanged { text, start, count, after ->
            if(binding.rechargeScreenView.SendAmount.hasFocus() && !text.isNullOrEmpty()){
                submissionRechargeRequest.send_money = text.toString()
                rechargeCalculateReceiveMoney()
            }
        }

        binding.rechargeScreenView.MobileNumber.doOnTextChanged { text, start, before, count ->
            submissionRechargeRequest.mobile_number = text.toString()
        }

        selectedChargeTypeSendMoney.value = "with_charge"

        binding.rechargeScreenView.ReceiveAmount.doOnTextChanged { text, start, count, after ->
            if(binding.rechargeScreenView.ReceiveAmount.hasFocus() && !text.isNullOrEmpty()){
                submissionRechargeRequest.receive_money = text.toString()
                rechargeCalculateSendMoney()
            }
        }

        binding.rechargeScreenView.ConversionRate.doOnTextChanged { text, start, count, after ->
            submissionRechargeRequest.conversion_rate = text.toString()
            if(!submissionRechargeRequest.send_money.isNullOrEmpty() && binding.rechargeScreenView.ConversionRate.hasFocus() && submissionRechargeRequest.conversion_rate.isNotEmpty()) rechargeCalculateReceiveMoney()
        }

        binding.rechargeScreenView.ServiceCharge.doOnTextChanged { text, start, count, after ->
            submissionRechargeRequest.service_charge = text.toString()
            if(!submissionRechargeRequest.send_money.isNullOrEmpty() && binding.rechargeScreenView.ServiceCharge.hasFocus() && submissionRechargeRequest.service_charge.isNotEmpty()){
                rechargeCalculateReceiveMoney(true)
            }
        }

        selectedMfs.observe(this, androidx.lifecycle.Observer {
            it?.apply {
                binding.rechargeScreenView.doRequest.visibility = View.VISIBLE
                binding.rechargeScreenView.view9.visibility = View.VISIBLE
                //binding.rechargeScreenView.bottomInfo.visibility = View.VISIBLE

                binding.rechargeScreenView.DetailedInfo.visibility = View.VISIBLE
                binding.rechargeScreenView.SelectedMfsLogo.visibility = View.VISIBLE
                binding.rechargeScreenView.SelectedMfsLogo.load("${BuildConfig.BASE_URL}${(it as MfsList).imagePath}")
                binding.rechargeScreenView.SelectedMfsName.text = "${it.mfsName}"
                rechargeUpdateSendType()
                rechargeUpdateMfsPackages()
                selectedCurrencySendMoney.value = userDetails?.storeBaseCurrency?:"BDT"

                submissionRechargeRequest.mfs_id = (it).mfsId
                submissionRechargeRequest.mfs_name = (it).mfsName

                euroServiceChargeList = when(it.mfsType) {
                    "mobile_recharge" -> (euroServiceChargeListMobile?: emptyList()).toMutableList()
                        else -> (euroServiceChargeListMfs?: emptyList()).toMutableList()
                }

                submissionRechargeRequest.charge_c = if(!it.defaultCharge.isNullOrEmpty() && (it.defaultCharge?:"0").toFloat() > 0) "${(it.defaultCharge?:"0").toFloat() / 100}" else "0.0"

                submissionRechargeRequest.commission_c = if(!it.defaultCommission.isNullOrEmpty() && (it.defaultCommission?:"0").toFloat() > 0) "${(it.defaultCommission?:"0").toFloat() / 100}" else "0.0"

                storeMfsSlab.forEach { slabDetails ->
                    if(slabDetails.id == submissionRechargeRequest.mfs_id)
                    {
                        slabDetails.charge?.let {
                            if(it.ifEmpty { "0" }.toFloat() > 0)
                                submissionRechargeRequest.charge_c = "${(it.toFloat() / 100)}"
                        }
                        slabDetails.commission?.let {
                            if(it.ifEmpty { "0" }.toFloat() > 0)
                                submissionRechargeRequest.commission_c = "${(it.toFloat() / 100)}"
                        }
                    }
                }

                submissionRechargeRequest.send_money = "0.0"
                binding.rechargeScreenView.SendAmount.setText("0")

                rechargeCalculateReceiveMoney()
            }
        })

        binding.rechargeScreenView.doRequest.setOnClickListener {

            val sPattern = Pattern.compile("^(?:\\+?88|0088)?01[13-9]\\d{8}$")
            val sPatternRocket = Pattern.compile("^(?:\\+?88|0088)?01[13-9]\\d{9}$")

            if(binding.rechargeScreenView.MobileNumber.text.isNullOrEmpty())
            {
                Snackbar.make(findViewById(android.R.id.content), "Invalid Mobile Number", Snackbar.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            if((!sPattern.matcher(binding.rechargeScreenView.MobileNumber.text).matches() && !submissionRechargeRequest.mfs_name.equals("Rocket")))
            {
                Snackbar.make(findViewById(android.R.id.content), "Invalid Mobile Number", Snackbar.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            if((!sPatternRocket.matcher(binding.rechargeScreenView.MobileNumber.text).matches() && submissionRechargeRequest.mfs_name.equals("Rocket")))
            {
                Snackbar.make(findViewById(android.R.id.content), "Invalid Rocket Mobile Number", Snackbar.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            if(selectedMfs!!.value!!.mfsType.equals("mobile_recharge") && submissionRechargeRequest.send_money!!.toFloat() > 1000)
            {
                Snackbar.make(findViewById(android.R.id.content), "You cannot recharge more then 1000/=", Snackbar.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            if(selectedMfs!!.value!!.mfsType.equals("financial_transaction") && submissionRechargeRequest.sending_currency == "euro" && submissionRechargeRequest.visualSendMoney!!.toFloat() < 1000)
            {
                Snackbar.make(findViewById(android.R.id.content), "You cannot send less then 1000/=", Snackbar.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            if(selectedMfs!!.value!!.mfsType.equals("financial_transaction") && submissionRechargeRequest.sending_currency !== "euro" && submissionRechargeRequest.send_money!!.toFloat() < 1000)
            {
                Snackbar.make(findViewById(android.R.id.content), "You cannot send less then 1000/=", Snackbar.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            var Content = ""
            var Content2 = ""

            when(submissionRechargeRequest.sending_currency) {
                "euro" -> {
                    Content += "Mobile Number: ${submissionRechargeRequest.mobile_number}\n"
                    Content += "Gateway: ${submissionRechargeRequest.mfs_name} (${submissionRechargeRequest.mfs_type!!.uppercase()})\n"

                    if(!submissionRechargeRequest.selected_mfs_package.isNullOrEmpty())
                    {
                        Content += "Gateway Package: ${submissionRechargeRequest.selected_mfs_package_name}\n"
                    }

                    Content += "Send Amount (EURO): ${DecimalFormat("##,##,##0.00").format((submissionRechargeRequest.send_money!!.toDouble() - ((if(submissionRechargeRequest.service_charge.toDouble() > 0) submissionRechargeRequest.service_charge.toDouble() else 0.0) * (if(submissionRechargeRequest.send_money_type == "without_charge") (-1) else (+1)))))}/=\n"
                    Content += "Send Amount (${userDetails?.storeBaseCurrency!!.uppercase()}): ${
                        DecimalFormat(
                            "##,##,##0.00"
                        ).format(submissionRechargeRequest.visualSendMoney!!.toDouble())
                    }/=\n"
                    Content += "Charge: (${userDetails?.storeBaseCurrency!!.uppercase()}): ${
                        DecimalFormat(
                            "##,##,##0.00"
                        ).format(submissionRechargeRequest.visualCharge!!.toDouble())
                    }/=\n"

                    Content2 = "Pin Number (To send ${submissionRechargeRequest.sending_currency!!.uppercase()}: ${
                        DecimalFormat(
                            "##,##,##0.00"
                        ).format(submissionRechargeRequest.visualSendMoney!!.toDouble())
                    }/= to ${submissionRechargeRequest.mobile_number})"
                }
                else ->{

                    Content += "Mobile Number: ${submissionRechargeRequest.mobile_number}\n"
                    Content += "Gateway: ${submissionRechargeRequest.mfs_name} (${submissionRechargeRequest.mfs_type!!.uppercase()})\n"

                    if(!submissionRechargeRequest.selected_mfs_package.isNullOrEmpty())
                    {
                        Content += "Gateway Package: ${submissionRechargeRequest.selected_mfs_package_name}\n"
                    }

                    Content += "Send Amount: ${userDetails?.storeBaseCurrency!!.uppercase()} ${
                        DecimalFormat(
                            "##,##,##0.00"
                        ).format(submissionRechargeRequest.send_money!!.toDouble())
                    }/=\n"

                    Content += "Commission: ${submissionRechargeRequest.sending_currency!!.uppercase()} ${DecimalFormat("##,##,##0.00").format(submissionRechargeRequest.commission!!.toDouble())}/=\n"

                    Content += "Charge: ${submissionRechargeRequest.sending_currency!!.uppercase()} ${DecimalFormat("##,##,##0.00").format(submissionRechargeRequest.charge!!.toDouble())}/=\n"

                    Content += "Received Amount: ${submissionRechargeRequest.sending_currency!!.uppercase()} ${
                        DecimalFormat(
                            "##,##,##0.00"
                        ).format(submissionRechargeRequest.receive_money!!.toDouble())
                    }/=\n"

                    Content2 = "Pin Number (To send ${submissionRechargeRequest.sending_currency!!.uppercase()}: ${
                        DecimalFormat(
                            "##,##,##0.00"
                        ).format(submissionRechargeRequest.send_money!!.toDouble())
                    }/= to ${submissionRechargeRequest.mobile_number})"
                }
            }

            InfoSheet().show(this@StoreDashboard) {
                title("Recharge Request")
                content(Content)
                onNegative("No") {}
                onPositive("Confirm") {
                    InputSheet().show(this@StoreDashboard) {
                        title("Credentials")
                        with(InputEditText("pin_number") {
                            required()
                            inputType(InputType.TYPE_CLASS_NUMBER)
                            label(Content2)
                            hint("Put your transaction pin number")
                        })
                        with(InputEditText("note") {
                            label("Note")
                            hint("Put your note")
                        })
                        onNegative {}
                        onPositive("Submit") { result ->
                            submissionRechargeRequest.transaction_pin = result.getString("pin_number")
                            submissionRechargeRequest.note = result.getString("note")
                            when(submissionRechargeRequest.sending_currency) {
                                "euro" -> {
                                    submissionRechargeRequest.recharge_amount = submissionRechargeRequest.visualSendMoney
                                }
                                else-> {
                                    submissionRechargeRequest.recharge_amount = submissionRechargeRequest.receive_money
                                }
                            }
                            viewModel.submitNewRechargeRequest(submissionRechargeRequest)
                        }
                    }
                }
            }
        }
    }

    fun rechargeCalculateMfsPackageCommission()
    {
        mfsPackageList.forEach {
            if(submissionRechargeRequest.selected_mfs_package == it.rowId)
            {
                submissionRechargeRequest.charge_c = (if(it.charge.isNullOrEmpty() && it.charge!!.toFloat() > 0) "${(it.charge!!.toFloat() / 100)}" else "0.0")

                submissionRechargeRequest.commission_c = (if(it.discount.isNullOrEmpty() && it.discount!!.toFloat() > 0) "${(it.discount!!.toFloat() / 100)}" else "0.0")

                submissionRechargeRequest.send_money = it.amount

                binding.rechargeScreenView.SendAmount.setText(submissionRechargeRequest.send_money)

                submissionRechargeRequest.charge = (submissionRechargeRequest.send_money!!.toFloat() * submissionRechargeRequest.charge_c!!.toFloat()).toString()

                submissionRechargeRequest.commission = (submissionRechargeRequest.send_money!!.toFloat() * submissionRechargeRequest.commission_c!!.toFloat()).toString()

                rechargeCalculateReceiveMoney()
            }
        }
    }

    fun rechargeCalculateReceiveMoney(noBasServiceCharge:Boolean = false)
    {
        when(submissionRechargeRequest.sending_currency)
        {
            "euro"->{
                if(!noBasServiceCharge) rechargeCalculateServiceCharge()

                submissionRechargeRequest.charge = (if(submissionRechargeRequest.service_charge!!.toFloat() > 0) "${submissionRechargeRequest.service_charge!!.toFloat() / submissionRechargeRequest.send_money!!.toFloat()}" else "0.0")

                submissionRechargeRequest.commission = "0.0"

                submissionRechargeRequest.visualCharge = ((submissionRechargeRequest.send_money!!.toFloat() * submissionRechargeRequest.conversion_rate!!.toFloat()) * submissionRechargeRequest.charge!!.toFloat()).toString()

                when(submissionRechargeRequest.send_money_type)
                {
                    "without_charge"->{
                        submissionRechargeRequest.visualSendMoney = (submissionRechargeRequest.conversion_rate!!.toFloat() * submissionRechargeRequest.send_money!!.toFloat()).toString()

                        submissionRechargeRequest.receive_money = (
                        (submissionRechargeRequest.send_money!!.toFloat() * submissionRechargeRequest.conversion_rate!!.toFloat()) +
                        (submissionRechargeRequest.commission!!.toFloat() * submissionRechargeRequest.conversion_rate!!.toFloat()) -
                        (submissionRechargeRequest.service_charge!!.toFloat() * submissionRechargeRequest.conversion_rate!!.toFloat())).toString()
                    }
                    "with_charge"->{
                        submissionRechargeRequest.visualSendMoney = (submissionRechargeRequest.conversion_rate!!.toFloat() * (submissionRechargeRequest.send_money!!.toFloat() - (submissionRechargeRequest.service_charge!!.toFloat() * (+1)))).toString()

                        submissionRechargeRequest.receive_money = (
                        (submissionRechargeRequest.send_money!!.toFloat() * submissionRechargeRequest.conversion_rate!!.toFloat()) +
                        (submissionRechargeRequest.commission!!.toFloat() * submissionRechargeRequest.conversion_rate!!.toFloat())
                        ).toString()
                    }
                }
            }
            else->{
                submissionRechargeRequest.charge = (
                    submissionRechargeRequest.send_money!!.toFloat() *
                        submissionRechargeRequest.charge_c!!.toFloat()).toString()

                submissionRechargeRequest.commission = (submissionRechargeRequest.send_money!!.toFloat() * submissionRechargeRequest.commission_c!!.toFloat()).toString()

                submissionRechargeRequest.receive_money = (submissionRechargeRequest.send_money!!.toFloat() + submissionRechargeRequest.commission!!.toFloat() - submissionRechargeRequest.charge!!.toFloat()).toString()
            }
        }

        binding.rechargeScreenView.ReceiveAmount.setText(DecimalFormat("0.00").format(submissionRechargeRequest.receive_money!!.toDouble()))
        updateBottomInfo()
    }

    fun rechargeCalculateSendMoney()
    {
        when(submissionRechargeRequest.sending_currency)
        {
            "euro"->{
                rechargeCalculateServiceCharge()
                submissionRechargeRequest.send_money = (submissionRechargeRequest.receive_money!!.toFloat() - submissionRechargeRequest.commission!!.toFloat()).toString()
            }
            else->{

                submissionRechargeRequest.charge = (submissionRechargeRequest.receive_money!!.toFloat() * submissionRechargeRequest.charge_c!!.toFloat()).toString()

                submissionRechargeRequest.commission = (submissionRechargeRequest.receive_money!!.toFloat() * submissionRechargeRequest.commission_c!!.toFloat()).toString()

                submissionRechargeRequest.send_money = (submissionRechargeRequest.receive_money!!.toFloat() - submissionRechargeRequest.commission!!.toFloat() + submissionRechargeRequest.charge!!.toFloat()).toString()
            }
        }

        binding.rechargeScreenView.SendAmount.setText(submissionRechargeRequest.send_money)
        updateBottomInfo()
    }

    fun rechargeCalculateServiceCharge()
    {
        euroServiceChargeList.forEach {
            if(
                (it.from!!.toFloat() < submissionRechargeRequest.send_money!!.toFloat()) &&
                (it.to!!.toFloat() >= submissionRechargeRequest.send_money!!.toFloat())
            ){
                submissionRechargeRequest.service_charge = it.charge!!
                if(!binding.rechargeScreenView.ServiceCharge.hasFocus()) binding.rechargeScreenView.ServiceCharge.setText(it.charge)
            }
        }
    }

    fun rechargeUpdateMfsPackages()
    {
        val packages: MutableList<MultiTaskingDropdownSpinner.Items> = ArrayList()
        binding.rechargeScreenView.packagesType.clear()

        if(selectedMfs!!.value!!.mfsType.equals("mobile_recharge"))
        {
            binding.rechargeScreenView.packagesType.visibility = View.VISIBLE
            mfsPackageList.forEach {
                val item: MultiTaskingDropdownSpinner.Items = MultiTaskingDropdownSpinner.Items()
                item.id = "${it.rowId}"
                item.title = "${it.packageName}"
                item.selcted = (PreSelectedPackageID === it.rowId)
                if(selectedMfs.value!!.mfsId == it.mfsId)
                {
                    packages.add(item)
                }
            }
        }
        else
        {
            binding.rechargeScreenView.packagesType.visibility = View.GONE
        }

        binding.rechargeScreenView.packagesType.addItems(packages)
    }

    fun rechargeUpdateSendType()
    {
        val sendTypes: MutableList<MultiTaskingDropdownSpinner.Items> = ArrayList()
        var Types = mutableListOf<String>()
        binding.rechargeScreenView.receiverType.clear()
        if(!selectedMfs!!.value!!.mfsType.equals("mobile_recharge"))
        {
            Types = mutableListOf<String>("personal", "agent")
        }
        else
        {
            Types = mutableListOf<String>("prepaid", "postpaid")
        }

        Types.forEachIndexed{index, it->
            val item: MultiTaskingDropdownSpinner.Items = MultiTaskingDropdownSpinner.Items()
            item.id = "${it}"
            item.title = "${it.capitalizeWords()}"
            item.selcted = (index == 0)
            sendTypes.add(item)
        }
        submissionRechargeRequest.mfs_type = Types[0]
        binding.rechargeScreenView.receiverType.addItems(sendTypes)
    }

    fun updateBottomInfo()
    {
        var HHTTMMLL = ""

        when(submissionRechargeRequest.sending_currency) {
            "euro" -> {
                HHTTMMLL = "${if(!submissionRechargeRequest.send_money.isNullOrEmpty()) "<b>Send Amount (EURO):</b> ${DecimalFormat("##,##,##0.00").format((submissionRechargeRequest.send_money!!.toDouble() - ((if(submissionRechargeRequest.service_charge.toDouble() > 0) submissionRechargeRequest.service_charge.toDouble() else 0.0) * (if(submissionRechargeRequest.send_money_type == "without_charge") (-1) else (+1)))))}/=<br>" else ""}"

                HHTTMMLL += ("${
                    if (!submissionRechargeRequest.visualSendMoney.isNullOrEmpty()) "<b>Send Amount (${userDetails?.storeBaseCurrency!!.uppercase()}):</b> ${
                        DecimalFormat(
                            "##,##,##0.00"
                        ).format(submissionRechargeRequest.visualSendMoney!!.toDouble())
                    }/=<br>" else ""
                }")

                HHTTMMLL += ("${
                    if (!submissionRechargeRequest.visualCharge.isNullOrEmpty()) "<b>Charge:</b> ${
                        DecimalFormat(
                            "##,##,##0.00"
                        ).format(submissionRechargeRequest.visualCharge!!.toDouble())
                    }/=<br>" else ""
                }")

            }
            else ->{
                HHTTMMLL = ("${if(!submissionRechargeRequest.commission.isNullOrEmpty()) "<b>Commission:</b> ${submissionRechargeRequest.sending_currency!!.uppercase()} ${DecimalFormat("##,##,##0.00").format(submissionRechargeRequest.commission!!.toDouble())}/=<br>" else ""}" +
                    "${if(!submissionRechargeRequest.charge.isNullOrEmpty()) "<b>Charge:</b> ${submissionRechargeRequest.sending_currency!!.uppercase()} ${DecimalFormat("##,##,##0.00").format(submissionRechargeRequest.charge!!.toDouble())}/=<br>" else ""}")

                HHTTMMLL += "${
                    if (!submissionRechargeRequest.receive_money.isNullOrEmpty()) "<b>Receive:</b> ${submissionRechargeRequest.sending_currency!!.uppercase()} ${
                        DecimalFormat(
                            "##,##,##0.00"
                        ).format(submissionRechargeRequest.receive_money!!.toDouble())
                    }/=" else ""
                }"
            }
        }

        binding.rechargeScreenView.bottomInfo.setText(if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            Html.fromHtml(HHTTMMLL, Html.FROM_HTML_MODE_COMPACT)
        } else {
            Html.fromHtml(HHTTMMLL)
        })
    }

    fun rechargeWindowReset1()
    {
        binding.rechargeScreenView.DetailedInfo.visibility = View.GONE
        binding.rechargeScreenView.SelectedMfsLogo.visibility = View.GONE
        binding.rechargeScreenView.doRequest.visibility = View.GONE
        binding.rechargeScreenView.view9.visibility = View.GONE
        binding.rechargeScreenView.bottomInfo.visibility = View.GONE
        binding.rechargeScreenView.SelectedMfsName.text = "Select a Service"
        submissionRechargeRequest.send_money = "0.0"
        binding.rechargeScreenView.SendAmount.setText("0")
        selectedCurrencySendMoney.value = userDetails?.storeBaseCurrency!!
        selectedChargeTypeSendMoney.value = "with_charge"

        submissionRechargeRequest = DashboardViewModel.SubmitRechargeRequest()
        selectedMfs.value = null
        PreSelectedPackageID = ""
    }


    fun hideServiceChargeScreen()
    {
        binding.StoreServiceChargesFragmentView.visibility = View.GONE
        binding.rechargeScreenView.root.visibility = View.VISIBLE
    }

    fun dashboardInit()
    {
        viewModel.updateFCM(PreferencesHelper(applicationContext).getFCMtoken())

        binding.drawerNavigationIcon.setOnClickListener {
            binding.navDrawerLayout.openDrawer(GravityCompat.START)
        }

        binding.sendMoneyBtt.setOnClickListener {
            binding.mfsBigScreenView.root.visibility = View.VISIBLE
            binding.filterBtt.visibility = View.GONE
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
            binding.rechargeScreenView.root.visibility = View.GONE
            //binding.ShowRechargeWindowFavBttn.visibility = if(binding.filterPanel.visibility == View.VISIBLE) View.GONE else (if(userDetails?.userType == Constants.newInstance().USER_TYPE_STORE) View.VISIBLE else View.GONE)
        }

        binding.clearAllBtn.setOnClickListener {
            clearAllFilter()
        }

        binding.refreshBalanceBtt.setOnClickListener {
            updateBalance()
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
            startActivity(Intent(this@StoreDashboard, MfsSummeryReport::class.java))
        }

        binding.dashboardDrawerNavigation.PaymentReportBtn.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            binding.filterPanel.visibility = View.GONE
            binding.filterGroup.visibility = binding.filterPanel.visibility
            startActivity(Intent(this@StoreDashboard, PaymentReport::class.java))
        }

        /*binding.dashboardDrawerNavigation.rechargeGroupBtn.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            showToast("Work In Progress")
        }*/

        binding.CallSupportBtn.setOnClickListener {
            MaterialDialog(this@StoreDashboard).show {
                title(text = "Hotline On-Call Support")
                message(text = "You can call to our hotline ${userDetails?.notice_meta!!.hotline_number} to know more")
                negativeButton(text = "Cancel") { dialog ->
                    dialog.dismiss()
                }
                positiveButton(text = "Call Now") { dialog ->
                    if(userDetails?.notice_meta!!.hotline_number!!.length > 1) dialPhoneNumber(userDetails?.notice_meta!!.hotline_number!!)
                }
            }
        }

        binding.dashboardDrawerNavigation.ChangePasswordButton.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            binding.filterPanel.visibility = View.GONE
            binding.filterGroup.visibility = binding.filterPanel.visibility
            InputSheet().show(this@StoreDashboard) {
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

        binding.dashboardDrawerNavigation.resellerGroup.visibility =  if(userDetails?.permissionLists?.contains("StoreController::list") == true) View.VISIBLE else View.GONE
        binding.dashboardDrawerNavigation.AddBalanceReportGroup.visibility =  if(userDetails?.permissionLists?.contains("ReportController::reseller_balance_recharge") == true) View.VISIBLE else View.GONE


        binding.dashboardDrawerNavigation.resellerGroupBtn.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            binding.filterPanel.visibility = View.GONE
            binding.filterGroup.visibility = binding.filterPanel.visibility
            startActivity(Intent(this@StoreDashboard, ResellersList::class.java))
        }

        binding.dashboardDrawerNavigation.AddBalanceReportBtn.setOnClickListener {
            binding.navDrawerLayout.closeDrawer(GravityCompat.START, false)
            binding.filterPanel.visibility = View.GONE
            binding.filterGroup.visibility = binding.filterPanel.visibility
            startActivity(Intent(this@StoreDashboard, AddBalanceReport::class.java))
        }

        viewModel.userUpdated.observe(this, androidx.lifecycle.Observer {
            if(it)
            {
                dataManager.preferencesHelper.prefLogout()
                MaterialDialog(this@StoreDashboard).show {
                    cancelable(false)
                    title(text = "Password Updated")
                    message(text = "Your password have been updated. Please Re-Login now.")
                    positiveButton(text = "Login Now") { dialog ->
                        startActivity(Intent(this@StoreDashboard, LoginActivity::class.java))
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
            startActivity(Intent(this@StoreDashboard, LoginActivity::class.java))
            finish()
        })

        viewModel.isLoading.observe(this, androidx.lifecycle.Observer {
            if(it!!) progressBarHandler!!.show()
            if(!it) progressBarHandler!!.hide()
        })

        viewModel.userProfileResponse.observe(this@StoreDashboard, androidx.lifecycle.Observer {
            dataManager.preferencesHelper.saveUserInfo(it)
            userDetails = dataManager.preferencesHelper.getUserInfo()
            userCurrentBalance = dataManager.preferencesHelper.getUserCurrentBalance()
            updateTitle()
        })

        viewModel.updateUserBalance.observe(this@StoreDashboard, androidx.lifecycle.Observer {
            updateBalance()
        })

        rechargeRequestList.clear()

        binding.theList.layoutManager = LinearLayoutManager(window.context)
        binding.theList.adapter = BaseRecyclerAdapter(window.context, object : IAdapterListener {
            override fun <T> clickListener(position: Int, model: T, view: View) {
                model as ArrayList<String>
                var request_id = model[4].split("|")[0]
                when(position)
                {
                    4->{
                        // Share button
                        val shareIntent = Intent()
                        shareIntent.action = Intent.ACTION_SEND
                        shareIntent.type="text/plain"
                        shareIntent.putExtra(Intent.EXTRA_TEXT, ("HelloDuniya\nNumber: ${model[2]}\nAmount: ${model[3]}\nType: ${model[1]}\nRefer ID: ${model[0]}\nReseller: ${model[8]}")+(if(model[9].length > 1) "\nParent: ${model[9]}" else ""))
                        startActivity(Intent.createChooser(shareIntent,"Share Details"))
                    }
                }
            }

            override fun getViewHolder(parent: ViewGroup, viewType: Int): BaseViewHolder {
                if(viewType>-1 ){
                    return RechargeRecyclerAdapter(
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

        }, rechargeRequestList as ArrayList)


        viewModel.rechargeActivityResponse.observe(this, androidx.lifecycle.Observer {
            rechargeRequestList.clear()
            mfsList.clear()
            rechargeRequestList.addAll(it!!.data!!.toMutableList())
            mfsList.addAll(it.mfsList)
            binding.theList.adapter!!.notifyDataSetChanged()

            mfsListById.clear()

            it.storeList.forEach { k ->
                k.id = if(k.id.isNullOrEmpty()) k.storeId else k.id
                k.name = if(k.name.isNullOrEmpty()) k.storeName else k.name

                k.storeId = if(k.storeId.isNullOrEmpty()) k.id else k.storeId
                k.storeName = if(k.storeName.isNullOrEmpty()) k.name else k.storeName
            }

            dataManager.preferencesHelper.put("basic_store_list", Gson().toJson(it.storeList))
            populateResellerList()

            val items: MutableList<MultiTaskingDropdownSpinner.Items> = ArrayList()
            mfsList.forEach {
                val item: MultiTaskingDropdownSpinner.Items = MultiTaskingDropdownSpinner.Items()
                item.id = "${it.mfsId}"
                item.title = "${it.mfsName}"
                items.add(item)
                mfsListById.put(it.mfsId!!, it)
            }
            binding.MfsList.addItems(items)

            mfsPackageList.clear()

            it.mfsPackageList!!.forEach{ k ->
                if(mfsListById.contains(k.mfsId)) k.mfsDetails = mfsListById.get(k.mfsId)
            }

            mfsPackageList.addAll(it.mfsPackageList)
            packageWindowInit()
            prepareMfsWindow()

            storeMfsSlab.clear()
            it.storeMfsSlab?.let { data ->
                storeMfsSlab.addAll(data)
            }

            euroServiceChargeListMobile = it.euroServiceChargeListMobile
            euroServiceChargeListMfs = it.euroServiceChargeListMfs

            binding.rechargeScreenView.root.visibility = View.GONE
            //binding.ShowRechargeWindowFavBttn.visibility = (if(userDetails?.userType == Constants.newInstance().USER_TYPE_STORE) View.VISIBLE else View.GONE)
            rechargeWindowReset1()

            binding.rechargeScreenView.ConversionRate.setText((if(it.conversionRate !== null) it.conversionRate!! else "1"))
            binding.StoreServiceChargesFragmentView.visibility = View.GONE

            submissionRechargeRequest.conversion_rate = (if(it.conversionRate !== null) it.conversionRate!! else "1")

            updateBalance()
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

        if(!userDetails?.logo.isNullOrBlank()){
            binding.dashboardDrawerNavigation.profileImage.load((if(URLUtil.isValidUrl(userDetails?.logo)) "${BuildConfig.BASE_URL}${userDetails?.logo}" else "${userDetails?.logo}"))
        }

        binding.FromDate.setOnClickListener {
            CalendarSheet().show(this) {
                title("Select From Date")
                selectionMode(SelectionMode.DATE)
                onPositive { dateStart, dateEnd ->
                    binding.FromDate.setText("${dateStart.get(Calendar.YEAR)}-${(dateStart.get(
                        Calendar.MONTH) + 1).toString().padStart(2, '0')}-${dateStart.get(Calendar.DATE).toString().padStart(2, '0')}")
                }
            }
        }

        binding.ToDate.setOnClickListener {
            CalendarSheet().show(this) {
                title("Select To Date")
                selectionMode(SelectionMode.DATE)
                onPositive { dateStart, dateEnd ->
                    binding.ToDate.setText("${dateStart.get(Calendar.YEAR)}-${(dateStart.get(
                        Calendar.MONTH) + 1).toString().padStart(2, '0')}-${dateStart.get(Calendar.DATE).toString().padStart(2, '0')}")
                }
            }
        }

        addSearchValidation()
        updateTitle()
        populateResellerList()
    }

    fun populateResellerList()
    {
        if(userDetails?.userType == Constants.newInstance().USER_TYPE_SUPER_ADMIN || userDetails?.permissionLists?.contains("StoreController::list") == true)
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
    }

    fun reload()
    {
        var searchParams = HashMap<String, String>()
        FilterCriteria.forEach { (key, value) ->
            if(value.split("||").size > 1) searchParams[key] = value.split("||")[1]
        }
        viewModel.getRechargeActivity(searchParams)
    }

    private fun updateBalance()
    {
        viewModel.getUserProfile()
    }

    fun updateTitle(){
        binding.title1.text = "${userCurrentBalance?.currency} ${userCurrentBalance?.amount?:""}/="
        binding.title2.text = "€ ${userCurrentBalance?.dueEuro?:""}/="
        binding.dashboardDrawerNavigation.UserName.text = "Hello, ${userDetails?.username?:""}"
        binding.dashboardDrawerNavigation.UserEmail.text = "${userDetails?.storePhoneNumber?:""}"

        if(!userDetails?.notice_meta!!.site_notice.isNullOrEmpty())
        {
            binding.NoticeText.visibility = View.VISIBLE
            binding.NoticeText.text = userDetails?.notice_meta!!.site_notice
        }
        else
        {
            binding.NoticeText.visibility = View.GONE
        }
    }

    fun logout()
    {
        MaterialDialog(this@StoreDashboard).show {
            title(text = "Logout")
            message(text = "Are you sure about logout?")
            negativeButton(text = "Cancel") { dialog ->
                dialog.dismiss()
            }
            positiveButton(text = "Log Out") { dialog ->
                dataManager.preferencesHelper.prefLogout()
                startActivity(Intent(this@StoreDashboard, LoginActivity::class.java))
                finish()
            }
        }
    }

    fun clearAllFilter(){
        FilterCriteria.clear()
        binding.filterChips.removeAllViews()
        binding.filterBtt.visibility = View.VISIBLE
        binding.filterPanel.visibility = View.GONE
        binding.filterGroup.visibility = View.GONE
        //binding.ShowRechargeWindowFavBttn.visibility = (if(userDetails?.userType == Constants.newInstance().USER_TYPE_STORE) View.VISIBLE else View.GONE)
        binding.FromDate.setText("")
        binding.FromDate.error = ""
        binding.ToDate.setText("")
        binding.ToDate.error = ""
        binding.PhoneNumber.setText("")
        binding.MfsList.clear()
        reload()
    }

    override fun onBackPressed() {
        clearAllFilter()
        logout()
    }
    fun dialPhoneNumber(phoneNumber: String) {
        val intent = Intent(Intent.ACTION_DIAL, Uri.parse("tel:$phoneNumber")).apply {
            data = Uri.parse("tel:$phoneNumber")
        }
        startActivity(intent)
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
            multiTaskingDropdownSpinnerView(binding.ResellerList) {
                onValue { view, value ->
                    if(value.value != null && !value.value.isNullOrEmpty()){
                        var resellerName = ""
                        val basic_store_list = Gson().fromJson<List<StoreInfo>>(
                            dataManager.preferencesHelper.get("basic_store_list", "[]"),
                            object : TypeToken<List<StoreInfo>>() {}.type
                        )

                        basic_store_list.forEach {
                            if(value.value.toString().equals(it.id!!))
                            {
                                resellerName = "${it.name}"
                            }
                        }

                        FilterCriteria["store_id"] = "Reseller: ${resellerName}||${value.value.toString()}"
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
                //binding.filterPanel.visibility = View.GONE
                binding.clearAllBtn.visibility = if(!(FilterCriteria.size > 0)) View.GONE else View.VISIBLE
                //binding.ShowRechargeWindowFavBttn.visibility = if(binding.filterPanel.visibility == View.VISIBLE) View.GONE else (if(userDetails?.userType == Constants.newInstance().USER_TYPE_STORE) View.VISIBLE else View.GONE)
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

    class RechargeRecyclerAdapter(itemView: ViewDataBinding, context: Context, val mfsList: MutableList<MfsList>) :
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

            var HHTTMMLL = ("${model[6]}<br><b>Mobile: ${model[3]}<br>[${model[4]}]</b>${if(model[7] != "null" && model[7] != null) "<br><br>Your Note: ${model[7]}" else ""}${if(model[8] != "null" && model[8] != null) "<br><br>Vendor Note: ${model[8]}" else ""}")


            binding.details.text = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                Html.fromHtml(HHTTMMLL, Html.FROM_HTML_MODE_COMPACT)
            } else {
                Html.fromHtml(HHTTMMLL)
            }

            //"3","2022-02-21 09:09:05","","01758899009","BDT 1,233.000","BDT 98,736.000","bKash (Personal)","asdasd","hudai rrr","Rejected","21\/02\/2022"

            binding.createdAt.text = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
                Html.fromHtml("Created on: ${model[1]}", Html.FROM_HTML_MODE_COMPACT)
            } else {
                Html.fromHtml("Created on: ${model[1]}")
            }

            binding.updatedAt.text = "Last Updated on: ${model[10]}"
            binding.status.text = "${model[9]}"

            when(model[9])
            {
                "Approved"->{
                    binding.status.chipBackgroundColor = ColorStateList.valueOf(ContextCompat.getColor(mContext, R.color.md_green_c1))
                    binding.status.setTextColor(mContext.resources.getColor(R.color.white))
                }
                "Rejected"->{
                    binding.status.chipBackgroundColor = ColorStateList.valueOf(ContextCompat.getColor(mContext, R.color.md_red_A700))
                    binding.status.setTextColor(mContext.resources.getColor(R.color.white))
                }
                "Pending", "Requested"->{
                    binding.status.chipBackgroundColor = ColorStateList.valueOf(ContextCompat.getColor(mContext, R.color.md_grey_400))
                    binding.status.setTextColor(mContext.resources.getColor(R.color.black))
                }
                "Progressing"->{
                    binding.status.chipBackgroundColor = ColorStateList.valueOf(ContextCompat.getColor(mContext, R.color.md_blue_700))
                    binding.status.setTextColor(mContext.resources.getColor(R.color.white))
                }
            }

            binding.ShareBtn.setOnClickListener {
                mCallback.clickListener(4, model, it.rootView)
            }
        }
    }

    class ImageBoxAdapterT2(itemView: ViewDataBinding, context: Context) :
        BaseViewHolder(itemView.root) {

        var binding = itemView as RvRechargeScreenMfsBoxT2Binding
        var mContext: Context = context

        override fun <T> onBind(position: Int, model: T, mCallback: IAdapterListener) {
            binding.img.load("${BuildConfig.BASE_URL}${(model as MfsList).imagePath}")
            binding.img.setOnClickListener {
                mCallback.clickListener(position, model, it!!)
            }

            binding.t1.setText((model as MfsList).mfsName!!.replace("Mobile Recharge (", "").replace(")", ""))
        }
    }

    class ImageBoxAdapter(itemView: ViewDataBinding, context: Context) :
        BaseViewHolder(itemView.root) {

        var binding = itemView as RvRechargeScreenMfsBoxBinding
        var mContext: Context = context

        override fun <T> onBind(position: Int, model: T, mCallback: IAdapterListener) {
            binding.img.load("${BuildConfig.BASE_URL}${(model as MfsList).imagePath}")
            binding.img.setOnClickListener {
                mCallback.clickListener(position, model, it!!)
            }
        }
    }

    class PackegeDetailsAdapter(itemView: ViewDataBinding, context: Context) :
        BaseViewHolder(itemView.root) {

        var binding = itemView as PackageScreenRvBinding
        var mContext: Context = context

        override fun <T> onBind(position: Int, model: T, mCallback: IAdapterListener) {
            model as MfsPackageList

            binding.mfsImage.load("${BuildConfig.BASE_URL}${model.mfsDetails!!.imagePath}")
            binding.packageName.setText(model.packageName!!.chunked(20).joinToString(separator = "\n"))
            binding.details.setText(model.note)
            binding.status.setText("${model.amount!!.dropLast(4)}/=")
            binding.card.setOnClickListener {
                mCallback.clickListener(position, model, it!!)
            }
        }
    }

    fun String.capitalizeWords(): String = split(" ").map { it.capitalize() }.joinToString(" ")
}
