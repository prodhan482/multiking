package com.hbworks.eu.bkashbd.data.local_db.entity

import androidx.room.ColumnInfo
import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity
class DashboardNotification {
    @PrimaryKey(autoGenerate = true) @ColumnInfo(name = "localId") var localId:Int = 0
    @ColumnInfo(name = "notification_id") lateinit var notification_id: String
    @ColumnInfo(name = "received_at") lateinit var received_at: String
    @ColumnInfo(name = "notification_message") lateinit var notification_message: String
    @ColumnInfo(name = "raw_data") lateinit var raw_data: String
    @ColumnInfo(name = "unread") var unread: Int = 1
}
