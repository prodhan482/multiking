package com.hbworks.eu.bkashbd.data.local_db.dao

import androidx.room.Dao
import androidx.room.Delete
import androidx.room.Insert
import androidx.room.Query
import com.hbworks.eu.bkashbd.data.local_db.entity.Category


@Dao
interface CategoryDao {

    @Query("SELECT * FROM Category")
    fun getAll(): List<Category>

    @Query("SELECT * FROM Category where localId=:localId")
    fun getAllById(localId: Int): List<Category>

    @Insert
    fun insert(categories: List<Category>): List<Long>

    @Insert
    fun insert(users: Category)

    @Query("DELETE FROM Category")
    fun delete(): Int

    @Delete
    fun delete(categories: List<Category>): Int

    @Delete
    fun delete(category: Category): Int
}