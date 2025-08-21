package com.hbworks.eu.bkashbd.view.activity.reports

import android.content.Intent
import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import androidx.lifecycle.ViewModelProvider
import com.hbworks.eu.bkashbd.databinding.ActivityAddNewResellerBinding
import com.hbworks.eu.bkashbd.util.showToast
import com.hbworks.eu.bkashbd.view.activity.login.LoginActivity
import com.hbworks.eu.bkashbd.view.base.BaseActivity

class AddNewReseller : BaseActivity() {

    private lateinit var viewModel: ReportViewModel

    private val binding: ActivityAddNewResellerBinding by lazy {
        ActivityAddNewResellerBinding.inflate(layoutInflater)
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(binding.root)
        this.viewModel = ViewModelProvider(this).get(ReportViewModel::class.java)
    }

    override fun viewRelatedTask() {
        viewModel.errorMessage.observe(this, androidx.lifecycle.Observer {
            showToast(it!!)
        })

        viewModel.forceLogOut.observe(this, androidx.lifecycle.Observer {
            dataManager.preferencesHelper.prefLogout()
            startActivity(Intent(this@AddNewReseller, LoginActivity::class.java))
            finish()
        })

        viewModel.isLoading.observe(this, androidx.lifecycle.Observer {
            if(it!!) progressBarHandler!!.show()
            if(!it) progressBarHandler!!.hide()
        })
    }
}
