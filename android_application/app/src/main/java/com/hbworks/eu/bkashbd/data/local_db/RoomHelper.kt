package com.hbworks.eu.bkashbd.data.local_db

import android.content.Context
import androidx.room.Room
import androidx.room.migration.Migration
import androidx.sqlite.db.SupportSQLiteDatabase

class RoomHelper(private val context: Context)  {

    private val MIGRATION_1_2: Migration =
        object : Migration(1, 2) {
            override fun migrate(database: SupportSQLiteDatabase) {
                database.execSQL("CREATE TABLE IF NOT EXISTS `MerchantAcDetailsByID` (" +
                    "`localId` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL," +
                    "`notification_id` TEXT NOT NULL," +
                    "`received_at` TEXT NOT NULL," +
                    "`notification_message` TEXT NOT NULL," +
                    "`raw_data` TEXT NOT NULL," +
                    "`unread` INTEGER NOT NULL)")
            }
        }

    private val db = Room.databaseBuilder(context, RoomDB::class.java, "BD_NAME").addMigrations(
        MIGRATION_1_2
    ).allowMainThreadQueries().build()

    fun getDatabase():RoomDB{
        return db
    }
}
