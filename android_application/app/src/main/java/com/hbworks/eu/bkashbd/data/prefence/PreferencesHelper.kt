package com.hbworks.eu.bkashbd.data.prefence

import android.content.Context
import android.content.SharedPreferences
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken
import com.hbworks.eu.bkashbd.view.activity.login.UserCurrentBalance
import com.hbworks.eu.bkashbd.view.activity.login.UserProfileData
import com.hbworks.eu.bkashbd.view.activity.login.UserProfileResponse

class PreferencesHelper(context: Context) {
    private val preferencesHelper: SharedPreferences
    private val sslPref = "ssl-prefs"
    private val PREF_KEY_IS_LOGIN = "PREF_KEY_IS_LOGIN"
    private val PREF_KEY_TOKEN = "PREF_KEY_TOKEN"
    private val PREF_KEY_USER_INFO = "PREF_KEY_USER_INFO"
    private val PREF_KEY_USER_CURRENT_BALANCE = "PREF_KEY_USER_CURRENT_BALANCE"
    private val PREF_KEY_LANG = "PREF_KEY_LANG"
    private val FCM_TOKEN = "FCM_TOKEN"


    init {
        preferencesHelper = context.getSharedPreferences(sslPref, Context.MODE_PRIVATE)
    }

    fun put(key: String, value: String?) {
        preferencesHelper.edit().putString(key, value).apply()
    }

    fun put(key: String, value: Int) {
        preferencesHelper.edit().putInt(key, value).apply()
    }

    fun put(key: String, value: Long) {
        preferencesHelper.edit().putLong(key, value).apply()
    }

    fun put(key: String, value: Float) {
        preferencesHelper.edit().putFloat(key, value).apply()
    }

    fun put(key: String, value: Boolean) {
        preferencesHelper.edit().putBoolean(key, value).apply()
    }

    operator fun get(key: String, defaultValue: String): String {
        return preferencesHelper.getString(key, defaultValue) ?: defaultValue
    }

    operator fun get(key: String, defaultValue: Int): Int {
        return preferencesHelper.getInt(key, defaultValue)
    }

    operator fun get(key: String, defaultValue: Float): Float {
        return preferencesHelper.getFloat(key, defaultValue)
    }

    operator fun get(key: String, defaultValue: Boolean): Boolean {
        return preferencesHelper.getBoolean(key, defaultValue)
    }

    operator fun get(key: String, defaultValue: Long): Long {
        return preferencesHelper.getLong(key, defaultValue)
    }

    fun <T> getResponse(key: String, clazz: Class<T>): T? {
        return try {
            val response = preferencesHelper.getString(key, "") ?: ""
            Gson().fromJson(response, clazz)
        } catch (e: Exception) {
            null
        }
    }

    fun deleteSavedData(key: String) {
        preferencesHelper.edit().remove(key).apply()
    }

    fun saveUserInfo(lr: UserProfileResponse)
    {
        preferencesHelper.edit().putString(PREF_KEY_USER_INFO, Gson().toJson(lr.data)).apply()
        preferencesHelper.edit().putString(PREF_KEY_USER_CURRENT_BALANCE, Gson().toJson(lr.currentBalance)).apply()
    }

    fun getUserInfo(): UserProfileData
    {
        return Gson().fromJson(
            preferencesHelper.getString(PREF_KEY_USER_INFO, "{}"),
            object : TypeToken<UserProfileData>() {}.type
        )
    }
    fun getUserCurrentBalance(): UserCurrentBalance
    {
        return try {
            preferencesHelper.getString(PREF_KEY_USER_CURRENT_BALANCE, null)?.let {
                Gson().fromJson(
                    it,
                    object : TypeToken<UserCurrentBalance>() {}.type
                )
            }?:run {
                UserCurrentBalance("", "", "")
            }
        } catch (e:Exception) {
            UserCurrentBalance("", "", "")
        }
    }

    fun saveFCMToken(token:String)
    {
        preferencesHelper.edit().putString(FCM_TOKEN, token).apply()
    }

    fun getFCMtoken():String
    {
        return preferencesHelper.getString(FCM_TOKEN, "")!!
    }

    fun prefSetSuccessfullyLogin()
    {
        preferencesHelper.edit().putBoolean(PREF_KEY_IS_LOGIN, true).apply()
    }

    fun saveUserName(logged_user_user_name:String)
    {
        preferencesHelper.edit().putString("logged_user_name", logged_user_user_name).apply()
    }

    fun saveUserPassword(logged_user_password:String)
    {
        preferencesHelper.edit().putString("logged_user_password", logged_user_password).apply()
    }

    fun getUserName():String
    {
        return preferencesHelper.getString("logged_user_name", "")!!
    }

    fun getUserPassword():String
    {
        return preferencesHelper.getString("logged_user_password", "")!!
    }

    fun prefLogout() {
        preferencesHelper.edit().putString(PREF_KEY_USER_INFO, null).apply()
        preferencesHelper.edit().putBoolean(PREF_KEY_IS_LOGIN, false).apply()
        preferencesHelper.edit().putString(PREF_KEY_TOKEN, null).apply()
    }

    fun prefGetLoginMode(): Boolean {
        return preferencesHelper.getBoolean(PREF_KEY_IS_LOGIN, false)
    }

    fun prefGetToken(): String {
        return preferencesHelper.getString(PREF_KEY_TOKEN, "")!!
    }

    fun prefSetToken(token: String) {
        preferencesHelper.edit().putString(PREF_KEY_TOKEN, token).apply()
    }

}
