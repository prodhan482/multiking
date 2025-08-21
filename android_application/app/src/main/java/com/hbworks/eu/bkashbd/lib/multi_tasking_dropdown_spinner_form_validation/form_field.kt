package com.sslwireless.crm.lib.multi_tasking_dropdown_spinner_form_validation

import com.afollestad.vvalidator.ValidationContainer
import com.afollestad.vvalidator.field.FieldValue
import com.afollestad.vvalidator.field.FormField
import com.afollestad.vvalidator.field.TextFieldValue
import com.hbworks.eu.bkashbd.lib.MultiTaskingDropdownSpinner

class MultiTaskingDropdownSpinnerField(
    container: ValidationContainer,
    view: MultiTaskingDropdownSpinner,
    name: String
) : FormField<MultiTaskingDropdownSpinnerField, MultiTaskingDropdownSpinner, CharSequence>(container, view, name)
{
    init {
        onErrors { myView, errors ->
            if(errors.size > 0)
            {
                myView.parent.requestChildFocus(myView, myView)
                myView.setError()
            }
            else
            {
                myView.clearError()
            }
        }
    }

    // Your first custom assertion
    fun cannotBeEmpty() = assert(MultiTaskingDropdownSpinnerAssertion())

    override fun obtainValue(
        id: Int,
        name: String
    ): FieldValue<CharSequence>? {
        val currentValue = view.theValue as? CharSequence ?: return null
        return TextFieldValue(
            id = id,
            name = name,
            value = currentValue
        )
    }

    override fun startRealTimeValidation(debounce: Int) {
    }
}
