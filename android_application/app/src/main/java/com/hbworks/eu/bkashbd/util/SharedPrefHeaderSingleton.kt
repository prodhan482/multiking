package com.hbworks.eu.bkashbd.util

import android.app.Activity
import android.content.Context
import android.content.Intent
import android.content.SharedPreferences
import android.net.ConnectivityManager
import android.util.Log
import android.view.LayoutInflater
import android.widget.TextView
import android.widget.Toast
import com.hbworks.eu.bkashbd.R
import java.math.BigDecimal

class SharedPrefHeaderSingleton {
    companion object {
        val instance = SharedPrefHeaderSingleton()

        private var mSharedPref: SharedPreferences? = null
        private val listString = StringBuilder()

        const val deviceId = "deviceId"
    }

    fun init(context: Context) {
        if (mSharedPref == null) mSharedPref =
            context.getSharedPreferences(context.packageName, Activity.MODE_PRIVATE)
    }

    external fun getEncKey(): String?

    fun getData(key: String?, defValue: String?): String? {
        return mSharedPref!!.getString(key, defValue)
    }

    fun setData(key: String?, value: String?) {
        val prefsEditor = mSharedPref!!.edit()
        prefsEditor.putString(key, value)
        prefsEditor.commit()
    }


    fun getBoolData(key: String?, defValue: Boolean): Boolean {
        return mSharedPref!!.getBoolean(key, defValue)
    }

    fun setBoolData(key: String?, value: Boolean) {
        val prefsEditor = mSharedPref!!.edit()
        prefsEditor.putBoolean(key, value)
        prefsEditor.commit()
    }

    fun getIntData(key: String?, defValue: Int): Int? {
        return mSharedPref!!.getInt(key, defValue)
    }

    fun setIntData(key: String?, value: Int?) {
        val prefsEditor = mSharedPref!!.edit()
        prefsEditor.putInt(key, value!!).commit()
    }

    fun getLongData(key: String?, defValue: Long): Long? {
        return mSharedPref!!.getLong(key, defValue)
    }

    fun setLongData(key: String?, value: Long?) {
        val prefsEditor = mSharedPref!!.edit()
        prefsEditor.putLong(key, value!!).commit()
    }

    fun isNetworkAvailable(mContext: Context): Boolean {
        val connectivityManager =
            mContext.getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
        val activeNetworkInfo = connectivityManager.activeNetworkInfo
        return activeNetworkInfo != null && activeNetworkInfo.isConnected
    }

    fun showToast(mContext: Context, message: String) {
        val toast = Toast(mContext)
        toast.duration = Toast.LENGTH_LONG

        val inflater = mContext.getSystemService(Context.LAYOUT_INFLATER_SERVICE) as LayoutInflater
        val view = inflater.inflate(R.layout.custom_toast_layout, null)

        val toastText = view.findViewById<TextView>(R.id.toastText)

        toastText.text = message

        toast.view = view
        toast.show()
    }

    fun nFormate(d: BigDecimal): String? {
        var data: String = ""
        var a = BigDecimal(d.toString())
        a = a.setScale(2, BigDecimal.ROUND_HALF_EVEN)

        if ("00" == a.toString().substring(a.toString().length - 2, a.toString().length)) {
            data = a.toString()
                .replace(a.toString().substring(a.toString().length - 3, a.toString().length), "")
            Log.d("TAG", "value" + data)

        } else {
            data = a.toString()
        }
        return data
    }

    fun getPriceFormatInEnglish(price: String): Double {
        val replacedOne =
            price.replace("০".toRegex(), "0").replace("১".toRegex(), "1")
                .replace("২".toRegex(), "2").replace("৩".toRegex(), "3").replace("৪".toRegex(), "4")
                .replace("৫".toRegex(), "5").replace("৬".toRegex(), "6").replace("৭".toRegex(), "7")
                .replace("৮".toRegex(), "8").replace("৯".toRegex(), "9")
        Log.e("TAG", "getPriceFormatInEnglish: $replacedOne")
        return replacedOne.toDouble()
    }

    fun returnMessage(message: List<String?>?): String {
        listString.clear()

        for (item in message!!.iterator()) {
            listString.append(item)
        }

        return listString.toString()
    }

//    fun initForceLogOut(context: Context, message: String) {
//        showToast(message)
//
//        setData(
//            sessionId,
//            null
//        )
//        ShareInfo.instance.saveLoginStatus(context, false)
//
//        val intent = Intent(context, LoginActivity::class.java)
//        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK or Intent.FLAG_ACTIVITY_CLEAR_TASK)
//        context.startActivity(intent)
//    }
}