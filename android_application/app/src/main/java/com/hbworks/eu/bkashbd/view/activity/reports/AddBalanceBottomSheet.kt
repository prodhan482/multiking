package com.hbworks.eu.bkashbd.view.activity.reports

import android.annotation.SuppressLint
import android.app.Dialog
import android.view.View
import android.widget.Toast
import androidx.core.widget.doOnTextChanged
import androidx.lifecycle.MutableLiveData
import com.afollestad.vvalidator.util.onTextChanged
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import com.google.android.material.bottomsheet.BottomSheetBehavior
import com.hbworks.eu.bkashbd.R
import com.hbworks.eu.bkashbd.data.model.CommissionPercent
import kotlinx.android.synthetic.main.add_balance_bottom_sheet.view.*
import java.text.DecimalFormat
import java.util.*
import kotlin.collections.HashMap

class AddBalanceBottomSheet : BottomSheetDialogFragment()
{
    private var position: Int = 0
    var listener: IBottomSheetDialogClicked? = null
    private lateinit var title: String
    private lateinit var bdtRate:String
    private lateinit var baseCurrency:String
    private lateinit var commissionPercent: String
    var submittingInfo:HashMap<String, String> = HashMap<String, String>()

    val decrementTimer = Timer()
    val doCalculation: MutableLiveData<Boolean> by lazy {
        MutableLiveData<Boolean>()
    }
    var listItems = mutableListOf<String>()



    @SuppressLint("RestrictedApi")
    override fun setupDialog(dialog: Dialog, style: Int) {
        super.setupDialog(dialog, style)
        val contentView = View.inflate(getContext(), R.layout.add_balance_bottom_sheet, null)
        dialog.setContentView(contentView)
        isCancelable = false

        contentView.TheCloseButton.setOnClickListener {
            doDismiss()
        }

        contentView.NoButton.setOnClickListener {
            doDismiss()
        }

        contentView.yesButton.setOnClickListener {
            if(submittingInfo.containsKey("master_add_balance") &&
                !submittingInfo["master_add_balance"].isNullOrEmpty() &&
                submittingInfo["master_add_balance"]!!.toFloat() > 0)
            {
                if(listener != null) listener!!.onYesPressed(submittingInfo)
                doDismiss()
            }
            else
            {
                Toast.makeText(context, "Invalid Balance", Toast.LENGTH_SHORT).show()
            }
        }

        contentView.title.setText(title)

        contentView.euroAmount.doOnTextChanged { text, start, before, count ->
            if(!text.isNullOrEmpty()) submittingInfo["euro_amount"] = "${text}"
            doCalculation.postValue(true)
        }
        contentView.CommissionPercent.doOnTextChanged { text, start, before, count ->
            if(!text.isNullOrEmpty()) submittingInfo["commission"] = "${text}"
            doCalculation.postValue(true)
        }
        contentView.BDTRate.doOnTextChanged { text, start, before, count ->
            if(!text.isNullOrEmpty()) submittingInfo["selected_currency_rate"] = "${text}"
            doCalculation.postValue(true)
        }
        contentView.Notes.doOnTextChanged { text, start, before, count ->
            if(!text.isNullOrEmpty()) submittingInfo["note"] = "${text}"
            doCalculation.postValue(true)
        }

        contentView.euroAmount.setText("1")
        contentView.CommissionPercent.setText(commissionPercent)
        contentView.BDTRate.setText(bdtRate)
        contentView.BDTRateHold.setHint("${baseCurrency.uppercase()} Rate")

        doCalculation.observe(this@AddBalanceBottomSheet) {

            var euroAmount = if(!submittingInfo["euro_amount"].isNullOrEmpty()) submittingInfo["euro_amount"] else "1"
            var selectedCurrencyAmount = if(!submittingInfo["selected_currency_rate"].isNullOrEmpty()) submittingInfo["selected_currency_rate"] else "0"
            var commissionPercent = if(!submittingInfo["commission"].isNullOrEmpty()) submittingInfo["commission"] else "0"

            submittingInfo["master_add_balance"] = "${((selectedCurrencyAmount!!.toFloat() * euroAmount!!.toFloat()) * ((100 - commissionPercent!!.toFloat()) / 100))}"

            if(!submittingInfo["master_add_balance"].isNullOrEmpty()) contentView.masterBalance.setText("Total Amount: ${baseCurrency.uppercase()} ${
                DecimalFormat("0.00").format(submittingInfo["master_add_balance"]!!.toDouble())}/=")
        }

    }

    fun setup(title:String, bdtRate:String, commissionPercent: String, baseCurrency:String):AddBalanceBottomSheet
    {
        this.title = title
        this.bdtRate = bdtRate
        this.commissionPercent = commissionPercent
        this.baseCurrency = baseCurrency

        return this@AddBalanceBottomSheet
    }

    fun doDismiss()
    {
        if(listener != null) listener!!.onNoPressed()
        dismiss()
    }

    fun setBottomDialogListener(listener: IBottomSheetDialogClicked):AddBalanceBottomSheet {
        this.listener = listener

        return this@AddBalanceBottomSheet
    }

    interface IBottomSheetDialogClicked {
        fun onYesPressed(info:HashMap<String, String>)
        fun onNoPressed()
    }
}
