package com.sslwireless.crm.lib.multi_tasking_dropdown_spinner_form_validation

import androidx.annotation.IdRes
import com.afollestad.vvalidator.checkAttached
import com.afollestad.vvalidator.field.FieldBuilder
import com.afollestad.vvalidator.form.Form
import com.afollestad.vvalidator.getViewOrThrow
import com.hbworks.eu.bkashbd.lib.MultiTaskingDropdownSpinner

fun Form.multiTaskingDropdownSpinnerView(
    view: MultiTaskingDropdownSpinner,
    name: String? = null,
    builder: FieldBuilder<MultiTaskingDropdownSpinnerField>
) {
    val newField = MultiTaskingDropdownSpinnerField(
        container = container.checkAttached(),
        view = view,
        name = name.toString()
    )
    builder(newField)
    appendField(newField)
}

fun Form.multiTaskingDropdownSpinnerView(
    @IdRes id: Int,
    name: String? = null,
    builder: FieldBuilder<MultiTaskingDropdownSpinnerField>
) = multiTaskingDropdownSpinnerView(
    view = container.getViewOrThrow(id),
    name = name,
    builder = builder
)
