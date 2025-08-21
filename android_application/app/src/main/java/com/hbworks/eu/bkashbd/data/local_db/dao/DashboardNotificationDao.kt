package com.hbworks.eu.bkashbd.data.local_db.dao

import androidx.room.Dao
import androidx.room.Delete
import androidx.room.Insert
import androidx.room.Query
import com.hbworks.eu.bkashbd.data.local_db.entity.DashboardNotification

@Dao
interface DashboardNotificationDao {
    @Query("SELECT * FROM DashboardNotification")
    fun getAll(): List<DashboardNotification>

    @Query("SELECT * FROM DashboardNotification ORDER BY DashboardNotification.notification_id ASC LIMIT 1")
    fun getFirstNotification(): List<DashboardNotification>

    @Query("SELECT * FROM DashboardNotification where localId=:localId")
    fun getAllById(localId: Int): List<DashboardNotification>

    @Insert
    fun insert(categories: List<DashboardNotification>): List<Long>

    @Insert
    fun insert(users: DashboardNotification)

    @Query("DELETE FROM DashboardNotification")
    fun delete(): Int

    @Delete
    fun delete(categories: List<DashboardNotification>): Int

    @Delete
    fun delete(category: DashboardNotification): Int
}
