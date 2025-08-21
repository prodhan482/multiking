package com.hbworks.eu.bkashbd.util

import android.app.Activity
import android.content.Context
import android.content.pm.PackageManager
import android.graphics.PorterDuff
import android.os.Build
import android.util.Log
import android.view.View
import android.view.inputmethod.InputMethodManager
import android.widget.EditText
import android.widget.TextView
import android.widget.Toast
import androidx.core.content.ContextCompat
import com.google.android.material.snackbar.Snackbar
import com.hbworks.eu.bkashbd.R


fun Context.showToast(message: String) {
    //val toast = Toast(this)
    //toast.duration = Toast.LENGTH_LONG

    //val inflater = getSystemService(Context.LAYOUT_INFLATER_SERVICE) as LayoutInflater
    //val view = inflater.inflate(R.layout.custom_toast_layout, null)

    //val toastText = view.findViewById<TextView>(R.id.toastText)
    //toastText.text = message

    //toast.view = view

    //val view = toast.view
    //view!!.background.setColorFilter(resources.getColor(R.color.colorPrimaryDark), PorterDuff.Mode.SRC_IN)
    //val text = view!!.findViewById<TextView>(android.R.id.message)
    //text.setTextColor(resources.getColor(R.color.white))

    //toast.setText(message)
    //toast.show()
    Toast.makeText(this, message, Toast.LENGTH_LONG).show()
}

fun EditText.showKeyboard() {
    val imm =
        context.getSystemService(Context.INPUT_METHOD_SERVICE) as InputMethodManager
    this.postDelayed({
        this.requestFocus()
        imm.showSoftInput(this, InputMethodManager.SHOW_IMPLICIT)
    }, 100)
}

fun Activity.hideKeyboard() {
    val inputManager = getSystemService(
        Context.INPUT_METHOD_SERVICE
    ) as InputMethodManager
    val focusedView = currentFocus

    if (focusedView != null) {
        inputManager.hideSoftInputFromWindow(
            focusedView.windowToken,
            InputMethodManager.RESULT_HIDDEN
        )
    }
}

fun Activity.hideKeyboard2()
{
    val imm = this.getSystemService(Activity.INPUT_METHOD_SERVICE) as InputMethodManager?
    imm!!.toggleSoftInput(InputMethodManager.HIDE_IMPLICIT_ONLY, 0)
}

fun Context?.hasPermissions(vararg permissions: Array<String>): Boolean {
    if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M && this != null && permissions != null) {
        Log.e("log per", "granted 1")
        for (permission  in permissions[0]) {
            if (ContextCompat.checkSelfPermission(this, permission) != PackageManager.PERMISSION_GRANTED) {
                Log.e("log per", "granted 2")
                return false
            }
        }
    }
    return true
}

fun View.snacksbarView(message: String, flag: Boolean) {
    if (flag) {
        val snackbar = Snackbar
            .make(this, message, Snackbar.LENGTH_INDEFINITE)
        snackbar.show()
    } else if (!flag) {
        val snackbar = Snackbar
            .make(this, message, Snackbar.LENGTH_LONG)
        snackbar.show()
    }

}
