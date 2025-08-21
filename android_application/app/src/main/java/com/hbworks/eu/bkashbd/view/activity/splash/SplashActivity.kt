package com.hbworks.eu.bkashbd.view.activity.splash

import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.net.Uri
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.lifecycle.ViewModelProviders
import com.afollestad.materialdialogs.MaterialDialog
import com.hbworks.eu.bkashbd.R
import com.hbworks.eu.bkashbd.util.Constants
import com.hbworks.eu.bkashbd.util.showToast
import com.hbworks.eu.bkashbd.view.activity.dashboard.AdminDashboard
import com.hbworks.eu.bkashbd.view.activity.dashboard.StoreDashboard
import com.hbworks.eu.bkashbd.view.activity.dashboard.VendorDashboard
import com.hbworks.eu.bkashbd.view.activity.login.LoginActivity
import com.hbworks.eu.bkashbd.view.activity.main.MainActivity
import com.hbworks.eu.bkashbd.view.base.BaseActivity
import java.util.*
import kotlin.concurrent.schedule

class SplashActivity : BaseActivity() {

    private var version: String? = "1.0.0"

    private lateinit var viewModel: SplashViewModel

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_splash)

        this.viewModel = ViewModelProviders.of(this).get(SplashViewModel::class.java)
    }

    override fun viewRelatedTask() {
        try {
            val pInfo = getPackageManager().getPackageInfo(getPackageName(), 0)
            version = pInfo.versionName
        } catch (e: PackageManager.NameNotFoundException) {
            e.printStackTrace()
        }

        viewModel.errorMessage.observe(this, androidx.lifecycle.Observer {
            showToast(it!!)
        })

        viewModel.fetchVersionCheck(version!!, this)
        viewModel.qResponse.observe(this, androidx.lifecycle.Observer {
            var mandatoryUpdateTo = (it.mandatoryUpdateTo!!.replace(".", "")).toInt()
            var allowVersionUpTo = (it.allowVersionUpTo!!.replace(".", "")).toInt()
            var appVersion = version!!.replace(".", "").toInt()

            if (appVersion < mandatoryUpdateTo) {
                MaterialDialog(this@SplashActivity).show {
                    title(text = "Need Mandatory Update")
                    cancelable(false)
                    message(text = "You cannot run this application. Press the button to download new application.")
                    positiveButton(text = "Download") { dialog ->
                        it.downloadUrl!!.asUri()?.openInBrowser(context)
                    }
                }
            } else if (!(appVersion < mandatoryUpdateTo) && (appVersion < allowVersionUpTo)) {
                MaterialDialog(this@SplashActivity).show {
                    cancelable(false)
                    title(text = "New Update Available")
                    message(text = "New Update have been available. Please download now.")
                    positiveButton(text = "Skip") { dialog ->
                        // Do something
                    }
                    negativeButton(text = "Download") { dialog ->
                        it.downloadUrl!!.asUri()?.openInBrowser(context)
                    }
                }
            } else {
                Timer("SettingUp", false).schedule(3000) {
                    if(dataManager.preferencesHelper.prefGetLoginMode())
                    {
                        when(dataManager.preferencesHelper.getUserInfo().userType)
                        {
                            Constants.newInstance().USER_TYPE_SUPER_ADMIN -> {
                                startActivity(Intent(this@SplashActivity, AdminDashboard::class.java))
                            }
                            Constants.newInstance().USER_TYPE_MANAGER -> {
                                startActivity(Intent(this@SplashActivity, StoreDashboard::class.java))
                            }
                            Constants.newInstance().USER_TYPE_STORE -> {
                                startActivity(Intent(this@SplashActivity, StoreDashboard::class.java))
                            }
                            Constants.newInstance().USER_TYPE_VENDOR -> {
                                startActivity(Intent(this@SplashActivity, VendorDashboard::class.java))
                            }
                        }

                        finish()
                    }
                    else
                    {
                        startActivity(Intent(this@SplashActivity, LoginActivity::class.java))
                        finish()
                    }
                }

            }
        })
    }

    fun Uri?.openInBrowser(context: Context) {
        this ?: return // Do nothing if uri is null

        val browserIntent = Intent(Intent.ACTION_VIEW, this)
        ContextCompat.startActivity(context, browserIntent, null)
    }

    fun String?.asUri(): Uri? {
        return try {
            Uri.parse(this)
        } catch (e: Exception) {
            null
        }
    }
}
