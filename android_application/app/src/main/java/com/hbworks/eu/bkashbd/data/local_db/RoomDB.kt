package com.hbworks.eu.bkashbd.data.local_db

import androidx.room.Database
import androidx.room.RoomDatabase
import com.hbworks.eu.bkashbd.data.local_db.dao.CategoryDao
import com.hbworks.eu.bkashbd.data.local_db.dao.DashboardNotificationDao
import com.hbworks.eu.bkashbd.data.local_db.entity.Category
import com.hbworks.eu.bkashbd.data.local_db.entity.DashboardNotification


@Database(entities = arrayOf(Category::class, DashboardNotification::class) , version = 2, exportSchema = false)
abstract class RoomDB : RoomDatabase() {

    abstract fun categoryDao(): CategoryDao
    abstract fun dashboardNotificationDao(): DashboardNotificationDao
}
