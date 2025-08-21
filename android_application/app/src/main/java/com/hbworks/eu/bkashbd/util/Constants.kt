package com.hbworks.eu.bkashbd.util


class Constants private constructor(){

    var USER_TYPE_SUPER_ADMIN = "super_admin"
    var USER_TYPE_MANAGER = "manager"
    var USER_TYPE_VENDOR = "vendor"
    var USER_TYPE_STORE = "store"

    companion object {
        private var constantField = Constants()
        fun newInstance(): Constants {
            return constantField
        }
    }
}
