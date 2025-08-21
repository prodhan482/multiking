package com.sslwireless.crm.lib.multi_tasking_dropdown_spinner_form_validation

import com.afollestad.vvalidator.assertion.Assertion
import com.hbworks.eu.bkashbd.lib.MultiTaskingDropdownSpinner

class MultiTaskingDropdownSpinnerAssertion : Assertion<MultiTaskingDropdownSpinner, MultiTaskingDropdownSpinnerAssertion>() {
    override fun isValid(view: MultiTaskingDropdownSpinner): Boolean {
        return view.ItemSelected
    }

    override fun defaultDescription(): String {
        return "This field cannot be empty"
    }
}
