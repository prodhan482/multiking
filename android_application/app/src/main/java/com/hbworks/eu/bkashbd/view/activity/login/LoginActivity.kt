package com.hbworks.eu.bkashbd.view.activity.login

import android.content.Intent
import android.os.Bundle
import android.text.InputType
import android.text.TextUtils
import androidx.lifecycle.ViewModelProviders
import com.afollestad.materialdialogs.MaterialDialog
import com.hbworks.eu.bkashbd.BuildConfig
import com.hbworks.eu.bkashbd.data.prefence.PreferencesHelper
import com.hbworks.eu.bkashbd.databinding.ActivityLoginBinding
import com.hbworks.eu.bkashbd.util.Constants
import com.hbworks.eu.bkashbd.util.showToast
import com.hbworks.eu.bkashbd.view.activity.dashboard.AdminDashboard
import com.hbworks.eu.bkashbd.view.activity.dashboard.StoreDashboard
import com.hbworks.eu.bkashbd.view.activity.dashboard.VendorDashboard
import com.hbworks.eu.bkashbd.view.activity.main.MainActivity
import com.hbworks.eu.bkashbd.view.base.BaseActivity
import com.maxkeppeler.sheets.input.InputSheet
import com.maxkeppeler.sheets.input.type.InputEditText

class LoginActivity : BaseActivity() {

    private val binding: ActivityLoginBinding by lazy {
        ActivityLoginBinding.inflate(layoutInflater)
    }

    private lateinit var viewModel: LoginViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(binding.root)

        this.viewModel = ViewModelProviders.of(this).get(LoginViewModel::class.java)
    }

    override fun viewRelatedTask() {
        binding.versionName.text = "Version ${BuildConfig.VERSION_NAME}"

        if(BuildConfig.DEBUG)
            println("================> FCM Token is: ${PreferencesHelper(applicationContext).getFCMtoken()}")

        binding.login.setOnClickListener {
            if (TextUtils.isEmpty(binding.email.text)) {
                showToast("Please enter user name")
            } else if (TextUtils.isEmpty(binding.password.text)) {
                showToast("Please enter password")
            } else {
                viewModel.doLogin(
                    binding.email.text.toString(),
                    binding.password.text.toString(),
                    PreferencesHelper(applicationContext).getFCMtoken(),
                    this
                )
            }
        }



        viewModel.loginResponse.observe(this@LoginActivity, androidx.lifecycle.Observer {
            dataManager.preferencesHelper.prefSetToken(it!!.token!!)

            viewModel.getUserProfile(this@LoginActivity)

        })

        viewModel.userProfileResponse.observe(this@LoginActivity, androidx.lifecycle.Observer {
            dataManager.preferencesHelper.saveUserInfo(it)
            when(it.data!!.userType)
            {
                Constants.newInstance().USER_TYPE_SUPER_ADMIN -> {
                    dataManager.preferencesHelper.prefSetSuccessfullyLogin()
                    startActivity(Intent(this@LoginActivity, AdminDashboard::class.java))
                    finish()
                }
                Constants.newInstance().USER_TYPE_MANAGER -> {
                    dataManager.preferencesHelper.prefSetSuccessfullyLogin()
                    startActivity(Intent(this@LoginActivity, StoreDashboard::class.java))
                    finish()
                }
                Constants.newInstance().USER_TYPE_STORE -> {
                    InputSheet().show(this@LoginActivity) {
                        title("Credentials")
                        with(InputEditText("pin_number") {
                            required()
                            inputType(InputType.TYPE_CLASS_NUMBER)
                            passwordVisible(false)
                            label("Transaction Pin")
                            hint("Put your transaction pin number")
                        })
                        onNegative {}
                        onPositive("Submit") { result ->
                            viewModel.verifyTransactionPin(result.getString("pin_number")!!)
                        }
                    }
                }
                Constants.newInstance().USER_TYPE_VENDOR -> {
                    dataManager.preferencesHelper.prefSetSuccessfullyLogin()
                    startActivity(Intent(this@LoginActivity, VendorDashboard::class.java))
                    finish()
                }
            }
        })

        viewModel.errorMessage.observe(this, androidx.lifecycle.Observer {
            showToast(it!!)
        })

        viewModel.forceLogOut.observe(this, androidx.lifecycle.Observer {
            dataManager.preferencesHelper.prefLogout()
            startActivity(Intent(this@LoginActivity, LoginActivity::class.java))
            finish()
        })

        viewModel.isLoading.observe(this, androidx.lifecycle.Observer {
            if(it!!) progressBarHandler!!.show()
            if(!it) progressBarHandler!!.hide()
        })

        binding.email.setText(dataManager.preferencesHelper.getUserName())
        binding.password.setText(dataManager.preferencesHelper.getUserPassword())

        viewModel.transactionPinVerificationDone.observe(this, androidx.lifecycle.Observer {
            if(it){
                dataManager.preferencesHelper.saveUserName(binding.email.text.toString().trim())
                dataManager.preferencesHelper.saveUserPassword(binding.password.text.toString().trim())
                openStoreDashboard()
            }
        })
    }

    fun openStoreDashboard()
    {
        dataManager.preferencesHelper.prefSetSuccessfullyLogin()
        startActivity(Intent(this@LoginActivity, StoreDashboard::class.java))
    }

    override fun onBackPressed() {
        MaterialDialog(this@LoginActivity).show {
            title(text = "Exit")
            message(text = "Are you sure about exit this app?")
            negativeButton(text = "Cancel") { dialog ->
                dialog.dismiss()
            }
            positiveButton(text = "Log Out") { dialog ->
                finishAffinity()
            }
        }
    }
}
