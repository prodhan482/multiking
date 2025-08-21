package com.hbworks.eu.bkashbd.view.activity.main

import android.os.Bundle
import com.hbworks.eu.bkashbd.databinding.ActivityMainBinding
import com.hbworks.eu.bkashbd.view.base.BaseActivity

class MainActivity : BaseActivity() {

    private val binding: ActivityMainBinding by lazy {
        ActivityMainBinding.inflate(layoutInflater)
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(binding.root)
    }

    override fun viewRelatedTask() {

    }
}
