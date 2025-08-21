package com.hbworks.eu.bkashbd.data

import com.hbworks.eu.bkashbd.data.local_db.RoomHelper
import com.hbworks.eu.bkashbd.data.network.ApiHelper
import com.hbworks.eu.bkashbd.data.prefence.PreferencesHelper
import javax.inject.Inject

class DataManager @Inject constructor(
    val preferencesHelper: PreferencesHelper, val roomHelper: RoomHelper,val apiHelper: ApiHelper
)