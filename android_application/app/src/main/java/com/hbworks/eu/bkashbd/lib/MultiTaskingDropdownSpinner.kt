package com.hbworks.eu.bkashbd.lib

import android.content.Context
import android.content.ContextWrapper
import android.content.DialogInterface
import android.content.res.TypedArray
import android.graphics.Color
import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
import android.util.AttributeSet
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.constraintlayout.widget.ConstraintLayout
import androidx.core.content.res.getIntegerOrThrow
import androidx.core.content.res.getStringOrThrow
import androidx.databinding.DataBindingUtil
import androidx.databinding.ViewDataBinding
import androidx.fragment.app.FragmentActivity
import androidx.recyclerview.widget.LinearLayoutManager
import com.google.android.flexbox.FlexDirection
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import com.hbworks.eu.bkashbd.R
import com.hbworks.eu.bkashbd.databinding.BottomSheetWithRvT1Binding
import com.hbworks.eu.bkashbd.databinding.RecycleViewWithOptionsT1Binding
import com.hbworks.eu.bkashbd.databinding.RecycleViewWithOptionsT2Binding
import com.hbworks.eu.bkashbd.view.adapter.IAdapterListener
import com.hbworks.eu.bkashbd.view.base.BaseRecyclerAdapter
import com.hbworks.eu.bkashbd.view.base.BaseViewHolder
import com.hbworks.eu.bkashbd.view.common.EmptyViewHolder
import java.io.IOException

private const val TYPE = "dialogType"
private const val TITLE = "dialogTitle"

open class MultiTaskingDropdownSpinner(context: Context, attrs: AttributeSet?): ConstraintLayout(context, attrs)
{
    var type:Int
    private lateinit var listItems: MutableList<Items>
    private lateinit var title:String
    private lateinit var labelTxt:String
    var SelectedItem: Items = Items()
    var SelectedItems:MutableList<Items> = arrayListOf()
    var theValue:String = ""
    private lateinit var theListener: MultiTaskingBottomSheetDropdownListener
    lateinit var label: TextView
    lateinit var Biglabel: TextView
    lateinit var attributes: TypedArray
    lateinit var MultiChoiceContainer:com.google.android.flexbox.FlexboxLayout
    lateinit var addPhotoBottomDialogFragment: TheBottomSheet

    var ItemSelected:Boolean = false
    var spinnerDisabled:Boolean = false

    init
    {
        inflate(context, R.layout.multi_taksing_spinner_base, this)
        label = findViewById(R.id.label)
        Biglabel = findViewById(R.id.Biglabel)
        MultiChoiceContainer = findViewById(R.id.multiChoiceContainer)
        MultiChoiceContainer.visibility = View.GONE

        attributes = context.obtainStyledAttributes(attrs, R.styleable.MultiTaskingDropdownSpinner)
        labelTxt = java.lang.String.format(" %s ", attributes.getStringOrThrow(R.styleable.MultiTaskingDropdownSpinner_label))
        label.text = labelTxt
        label.visibility = View.GONE
        title = attributes.getStringOrThrow(R.styleable.MultiTaskingDropdownSpinner_label)

        Biglabel.text = labelTxt
        type = attributes.getIntegerOrThrow(R.styleable.MultiTaskingDropdownSpinner_type)

        val container: View =  findViewById(R.id.container)

        container.setOnClickListener {
            if(::listItems.isInitialized)
            {
                if(!spinnerDisabled)
                    ShowBottomSheet()
            }
            else
            {
                println("************************ ERROR. ********************************")
                println("******** You have to add items using addItems() method. ********")
                throw IOException()
            }
        }

        addPhotoBottomDialogFragment = TheBottomSheet.newInstance(type, title)

        attributes.recycle()
    }

    fun clear()
    {
        label.visibility = View.GONE
        Biglabel.text = labelTxt
        Biglabel.setTextColor(Color.parseColor("#5d636d"))
        SelectedItem = Items()
        SelectedItems = arrayListOf()
        theValue = ""
        if(::theListener.isInitialized)
        {
            theListener.onSelect(SelectedItem, 0)
        }
    }

    fun setLabel(_label:String)
    {
        labelTxt = _label
        Biglabel.text = labelTxt
        label.text = labelTxt
    }

    fun setError()
    {
        val bg: View = findViewById(R.id.view10)
        bg.background = context.resources.getDrawable(R.drawable.corner_bg_with_stroke_2)
    }

    fun clearError()
    {
        val bg: View = findViewById(R.id.view10)
        bg.background = context.resources.getDrawable(R.drawable.corner_bg_with_stroke_1)
    }

    fun setListener(_listenr: MultiTaskingBottomSheetDropdownListener)
    {
        theListener = _listenr
    }

    fun disable(){
        spinnerDisabled = true;
    }

    fun enable(){
        spinnerDisabled = false;
    }

    fun addItems(items: MutableList<Items>)
    {
        listItems = items
        for (item in listItems){

            if(type == 0)
            {
                if(item.selcted)
                {
                    ItemSelected = true
                    SelectedItem = item
                    theValue = item.id
                    label.visibility = View.VISIBLE

                    Biglabel.text = "${item.title}"
                    Biglabel.setTextColor(context.resources.getColor(R.color.black))
                }
            }
            else
            {
                populateMultiCheckedItems(items)
            }
        }
    }

    fun populateMultiCheckedItems(items: MutableList<Items>)
    {
        MultiChoiceContainer.removeAllViewsInLayout()
        MultiChoiceContainer.visibility = View.VISIBLE

        MultiChoiceContainer.layoutDirection = FlexDirection.ROW
        SelectedItems = arrayListOf()
        theValue = ""
        ItemSelected = false

        items.forEachIndexed { position, item ->
            if(item.selcted)
            {
                theValue = if(theValue.isNullOrEmpty()) theValue.plus("${item.id}") else theValue.plus(",${item.id}")
                SelectedItems.add(item)
                MultiChoiceContainer.addView(
                    SelectedItemView(
                        context,
                        item.title,
                        position,
                        object : Actions {
                            override fun deleted(pos: Int) {
                                items.get(pos).selcted = false
                                populateMultiCheckedItems(items)
                                if(::theListener.isInitialized)
                                {
                                    theListener.onMultiSelect(items)
                                }
                            }
                        }
                    )
                )

                ItemSelected = true
            }
        }

        if(ItemSelected)
        {
            label.visibility = View.VISIBLE
            Biglabel.visibility = View.GONE
        }
        else
        {
            label.visibility = View.GONE
            Biglabel.visibility = View.VISIBLE
        }

        if(ItemSelected) clearError()
    }

    private fun ShowBottomSheet()
    {
        addPhotoBottomDialogFragment.addItems(listItems)
        addPhotoBottomDialogFragment.setListener(object :
            MultiTaskingBottomSheetDropdownListener {
            override fun onSelect(item: Items, position:Int) {
                SelectedItem = item
                label.visibility = View.VISIBLE

                Biglabel.text = java.lang.String.format(" %s ", item.title)
                Biglabel.setTextColor(context.resources.getColor(R.color.black))

                if(::theListener.isInitialized)
                {
                    theListener.onSelect(item, position)
                }
                ItemSelected = true
                theValue = item.id
                if(ItemSelected) clearError()
            }

            override fun onMultiSelect(item: MutableList<Items>)
            {
                if(::theListener.isInitialized)
                {
                    theListener.onMultiSelect(item)
                }
                populateMultiCheckedItems(item)
            }
        })

        var finalContext = context
        if (finalContext !is FragmentActivity && context is ContextWrapper) {
            finalContext = (context as ContextWrapper).baseContext
        }
        addPhotoBottomDialogFragment.show((finalContext as FragmentActivity).supportFragmentManager, "ActionBottomDialog")
    }

    class Items
    {
        var id:String = ""
        var title:String = ""
        var selcted:Boolean = false
    }

    interface MultiTaskingBottomSheetDropdownListener
    {
        fun onSelect(item: Items, position:Int)
        fun onMultiSelect(selectedItem: MutableList<Items>)
    }

    class TheBottomSheet : BottomSheetDialogFragment()
    {
        private var dialogType: Int = 0
        private var dialogTitle: String? = null
        lateinit var listItems: MutableList<Items>
        lateinit var filteredItems: MutableList<Items>
        lateinit var theListener: MultiTaskingBottomSheetDropdownListener
        lateinit var theRVAdapter: BaseRecyclerAdapter<Items>

        lateinit var binding: BottomSheetWithRvT1Binding

        override fun onCreate(savedInstanceState: Bundle?) {
            super.onCreate(savedInstanceState)
            arguments?.let {
                dialogType = it.getInt(TYPE)
                dialogTitle = it.getString(TITLE)
            }
        }

        fun setListener(_listenr: MultiTaskingBottomSheetDropdownListener)
        {
            theListener = _listenr
        }

        override fun onDismiss(dialog: DialogInterface)
        {
            super.onDismiss(dialog)
            if(dialogType == 1)
            {
                if(::theListener.isInitialized)
                {
                    theListener.onMultiSelect(listItems)
                }
            }
        }

        fun PrepareStage()
        {
            binding.pageTitleText.text = dialogTitle

            binding.theList.layoutManager = LinearLayoutManager(context)
            theRVAdapter = BaseRecyclerAdapter(context, object : IAdapterListener
            {
                override fun <T> clickListener(position: Int, model: T, view: View)
                {
                    if(dialogType == 0)
                    {
                        // Single Select Radio Button.

                        var pos:Int = 0
                        for (items in listItems)
                        {
                            items.selcted = false
                            if(pos == position)
                            {
                                items.selcted = true
                            }
                            pos = pos + 1
                            binding.theList.adapter?.notifyDataSetChanged()
                        }
                        if(::theListener.isInitialized)
                        {
                            theListener.onSelect(model as Items, position)
                        }
                        dismiss()
                    }
                    if(dialogType == 1)
                    {
                        if(::theListener.isInitialized)
                        {
                            theListener.onMultiSelect(listItems)
                        }
                    }
                }

                override fun getViewHolder(parent: ViewGroup, viewType: Int): BaseViewHolder
                {
                    if(viewType>-1 ){
                        if(dialogType == 0)
                        {
                            return RVIH_radio(
                                DataBindingUtil.inflate(
                                    LayoutInflater.from(parent.context)
                                    , R.layout.recycle_view_with_options_t1
                                    , parent, false
                                )
                                , parent.context
                            )
                        }
                        else
                        {
                            return RVIH_checkbox(
                                DataBindingUtil.inflate(
                                    LayoutInflater.from(parent.context)
                                    , R.layout.recycle_view_with_options_t2
                                    , parent, false
                                )
                                , parent.context
                            )
                        }
                    }else{
                        return EmptyViewHolder(
                            DataBindingUtil.inflate(
                                LayoutInflater.from(parent.context)
                                , R.layout.empty_page
                                , parent, false
                            )
                            , parent.context
                        )
                    }
                }

            }, filteredItems as ArrayList)

            binding.theList.adapter = theRVAdapter
            binding.TheCloseButton.setOnClickListener {dismiss()}

            binding.searchBox.addTextChangedListener(object : TextWatcher
            {
                override fun afterTextChanged(p0: Editable?){}
                override fun beforeTextChanged(p0: CharSequence?, p1: Int, p2: Int, p3: Int){}
                override fun onTextChanged(p0: CharSequence?, p1: Int, p2: Int, p3: Int) {
                    populateFilteredItems(p0.toString())
                }
            })
        }

        fun populateFilteredItems(searchString:String)
        {
            if(searchString.isEmpty())
            {
                filteredItems = listItems
            }
            else
            {
                var selectedIDs:MutableList<String> = arrayListOf()

                filteredItems.forEach {
                    if(it.selcted) selectedIDs.add(it.id)
                }

                filteredItems = arrayListOf()

                listItems.forEach {
                    if(it.title.toLowerCase().contains(searchString.toLowerCase()))
                    {
                        filteredItems.add(it)
                    }
                    if(selectedIDs.contains(it.id))
                        it.selcted = true
                }
            }

            theRVAdapter.setData(filteredItems as ArrayList)
        }

        fun addItems(items: MutableList<Items>)
        {
            items.add(Items())
            items.add(Items())
            items.add(Items())

            filteredItems = items
            listItems = items
        }

        override fun onCreateView(
            inflater: LayoutInflater,
            container: ViewGroup?,
            savedInstanceState: Bundle?
        ): View? {
            binding = DataBindingUtil.inflate(inflater, R.layout.bottom_sheet_with_rv_t1, container, false);
            PrepareStage();
            return binding.root
        }

        companion object {
            @JvmStatic
            fun newInstance(dialogType: Int, title:String) =
                TheBottomSheet()
                    .apply {
                        arguments = Bundle().apply {
                            putInt(TYPE, dialogType)
                            putString(TITLE, title)
                        }
                    }
        }

        class RVIH_radio(itemView: ViewDataBinding, context: Context) : BaseViewHolder(itemView.root) {

            var binding = itemView as RecycleViewWithOptionsT1Binding
            var mContext: Context = context

            override fun <T> onBind(position: Int, itemModel: T, mCallback: IAdapterListener)
            {
                itemModel as Items

                if(itemModel.title.toString().isNullOrEmpty())
                {
                    binding.holder.visibility = View.INVISIBLE
                }
                else {
                    binding.holder.visibility = View.VISIBLE

                    binding.Title.text = java.lang.String.format("%s", itemModel.title)

                    if(itemModel.selcted)
                    {
                        binding.Title.setTextColor(mContext.resources.getColor(R.color.colorAccent))
                        binding.RadioCheckIcon.isChecked = true
                    }
                    else
                    {
                        binding.Title.setTextColor(mContext.resources.getColor(R.color.black))
                        binding.RadioCheckIcon.isChecked = false
                    }

                    binding.holder.setOnClickListener {
                        itemModel.selcted = true
                        mCallback.clickListener(position, itemModel, it)
                    }
                }
            }
        }


        class RVIH_checkbox(itemView: ViewDataBinding, context: Context) : BaseViewHolder(itemView.root)
        {

            var binding = itemView as RecycleViewWithOptionsT2Binding
            var mContext: Context = context

            override fun <T> onBind(position: Int, itemModel: T, mCallback: IAdapterListener)
            {
                itemModel as Items

                if(itemModel.title.toString().isNullOrEmpty())
                {
                    binding.holder.visibility = View.INVISIBLE
                }
                else
                {
                    binding.holder.visibility = View.VISIBLE
                    binding.Title.text = java.lang.String.format("%s", itemModel.title)

                    if(itemModel.selcted)
                    {
                        binding.Title.setTextColor(mContext.resources.getColor(R.color.colorAccent))
                        binding.CheckBoxIcon.isChecked = true
                    }
                    else
                    {
                        binding.Title.setTextColor(mContext.resources.getColor(R.color.black))
                        binding.CheckBoxIcon.isChecked = false
                    }

                    binding.holder.setOnClickListener {
                        if(binding.CheckBoxIcon.isChecked)
                        {
                            itemModel.selcted = false
                            binding.CheckBoxIcon.isChecked = false
                            binding.Title.setTextColor(mContext.resources.getColor(R.color.black))
                        }
                        else
                        {
                            itemModel.selcted = true
                            binding.CheckBoxIcon.isChecked = true
                            binding.Title.setTextColor(mContext.resources.getColor(R.color.colorAccent))
                        }
                        mCallback.clickListener(position, itemModel, it)
                    }
                }
            }
        }
    }

    class SelectedItemView(context: Context, label:String, position: Int, listener: Actions): ConstraintLayout(context) {
        lateinit var theLabel: TextView
        lateinit var DeleteButton: View

        init {
            inflate(context, R.layout.multi_taksing_spinner_multi_selection, this)
            theLabel = findViewById(R.id.theLabel)
            theLabel.text = label

            DeleteButton = findViewById(R.id.TheCloseButton)
            DeleteButton.setOnClickListener {
                listener.deleted(position)
            }
        }
    }

    interface Actions
    {
        fun deleted(position: Int)
    }
}
