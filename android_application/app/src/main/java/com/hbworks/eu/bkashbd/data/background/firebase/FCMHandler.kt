package com.hbworks.eu.bkashbd.data.background.firebase

import android.app.Notification
import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.PendingIntent
import android.content.Context
import android.content.Intent
import android.graphics.BitmapFactory
import android.graphics.Color
import android.media.AudioAttributes
import android.media.RingtoneManager
import android.net.Uri
import android.os.Build
import android.util.Log
import androidx.core.app.NotificationCompat
import androidx.core.content.ContextCompat
import com.google.firebase.messaging.FirebaseMessagingService
import com.google.firebase.messaging.RemoteMessage
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken
import com.hbworks.eu.bkashbd.MyApp
import com.hbworks.eu.bkashbd.R
import com.hbworks.eu.bkashbd.data.DataManager
import com.hbworks.eu.bkashbd.data.local_db.entity.DashboardNotification
import com.hbworks.eu.bkashbd.data.prefence.PreferencesHelper
import com.hbworks.eu.bkashbd.view.activity.splash.SplashActivity
import java.text.SimpleDateFormat
import java.util.*
import javax.inject.Inject

/**
 * handles fcm service methods
 */
class FCMHandler : FirebaseMessagingService() {
    /**
     * Called when message is received.
     *
     * @param remoteMessage Object representing the message received from Firebase Cloud Messaging.
     */

    override fun onMessageReceived(remoteMessage: RemoteMessage) {
        super.onMessageReceived(remoteMessage)

        val params = remoteMessage!!.data
        //val jsonObject = JSONObject(params)
        //Log.e("JSON_OBJECT", jsonObject.toString())
        Log.d("FCM Message Received",Gson().toJson(params))

        if(params != null && params.containsKey("body") && params.containsKey("title"))
        {
            if(!params.containsKey("data_type"))
                sendNotification(params)
            else if(params.containsKey("data_type") && params["data_type"] == "notification")
                sendNotification(params)
            else
                prepareOnData(params)
            //"data_type"=>"notification",
        }
    }

    override fun onNewToken(token: String) {
        sendRegistrationToServer(token)
    }

    /**
     * Schedule async work using WorkManager.
     */
    private fun scheduleJob() {

    }

    /**
     * Handle time allotted to BroadcastReceivers.
     */
    private fun handleNow() {
    }

    /**
     * Persist token to third-party servers.
     *
     * Modify this method to associate the user's FCM registration token with any server-side account
     * maintained by your application.
     *
     * @param token The new token.
     */
    private fun sendRegistrationToServer(token: String?) {
        PreferencesHelper(applicationContext!!).saveFCMToken(token!!)
        Log.d("fcm token"," ........ "+ token)
        Log.d(TAG, token ?: "")
    }

    private fun prepareOnData(params:Map<String, String>)
    {
        if(params.containsKey("dashboard_notification"))
        {
            var info = DashboardNotification()
            info.notification_id = "${Calendar.getInstance().time}"
            info.received_at = "${SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(Calendar.getInstance().time)}"
            info.notification_message = Gson().toJson(params)
            info.raw_data = Gson().toJson(params)
            info.unread = 1
            (application as MyApp).dataManager.roomHelper.getDatabase().dashboardNotificationDao().insert(info)
        }
    }

    private fun sendNotification(params:Map<String, String>)
    {
        val NOTIFICATION_CHANNEL_ID = params["request_type"]

        var notificationIntent: Intent? = Intent(this, SplashActivity::class.java)

        /*if(params.containsKey("description") && params.get("description") != null)
        {
            val type = object : TypeToken<HashMap<String, String>>() {}.type
            var tempNotificationData:HashMap<String, String> = Gson().fromJson(params.get("description")!!, type)

            tempNotificationData.put("notificationTitle", params.get("title")!!)
            tempNotificationData.put("notificationBody", params.get("body")!!)
            if(tempNotificationData.containsKey("comments") && tempNotificationData.get("comments") != null)
                tempNotificationData.put("notificationComment",tempNotificationData.get("comments")!!)

            notificationIntent?.putExtra("TempNotificationData",Gson().toJson(tempNotificationData))

            Intent().also { intent ->
                intent.setAction("CRM_Notification")
                intent.putExtra("NotificationBR", Gson().toJson(tempNotificationData))
                sendBroadcast(intent)
            }

            var CRM_Notification: String by LocalPreference("CRM_Notification", "{}")
            CRM_Notification = Gson().toJson(tempNotificationData)
        }*/

        notificationIntent!!.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP or Intent.FLAG_ACTIVITY_SINGLE_TOP)

        val intent = if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            PendingIntent.getActivity(
                this, 0,
                notificationIntent, PendingIntent.FLAG_IMMUTABLE or PendingIntent.FLAG_UPDATE_CURRENT
            )
        } else {
            PendingIntent.getActivity(
                this, 0,
                notificationIntent, 0
            )
        }

        val mNotificationManager = getSystemService(Context.NOTIFICATION_SERVICE) as NotificationManager

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val notificationChannel = NotificationChannel(
                NOTIFICATION_CHANNEL_ID, "Your Notifications",
                NotificationManager.IMPORTANCE_HIGH
            )

            val pattern = longArrayOf(0, 1000, 500, 1000)

            notificationChannel.description = ""
            notificationChannel.enableLights(true)
            notificationChannel.lightColor = Color.RED
            notificationChannel.vibrationPattern = pattern

            val audioAttributes = AudioAttributes.Builder()
                .setContentType(AudioAttributes.CONTENT_TYPE_MUSIC)
                .setUsage(AudioAttributes.USAGE_NOTIFICATION)
                .build()

            notificationChannel.setSound(RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION), audioAttributes)

            notificationChannel.enableVibration(true)
            mNotificationManager.createNotificationChannel(notificationChannel)
        }

        // to diaplay notification in DND Mode
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = mNotificationManager.getNotificationChannel(NOTIFICATION_CHANNEL_ID)
            channel.canBypassDnd()
        }

        val notificationBuilder = NotificationCompat.Builder(this, NOTIFICATION_CHANNEL_ID!!)
        val uri: Uri = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION)

        var smallIcon = R.drawable.ic_stat_add_to_home_screen
        when(params["request_type"])
        {
            "mobile_recharge_request_approved"->{
                smallIcon = R.drawable.ic_stat_add_to_home_screen
            }
            "mobile_recharge_request_rejected"->{
                smallIcon = R.drawable.ic_stat_block
            }
            "mobile_recharge_request_received"->{
                smallIcon = R.drawable.ic_stat_bolt
            }
            "sim_card_have_been_sold_by_reseller"->{
                smallIcon = R.drawable.sold
            }
            "sim_card_have_been_activated_by_admin"->{
                smallIcon = R.drawable.checked
            }
            "sim_card_have_been_rejected_by_admin"->{
                smallIcon = R.drawable.cancel
            }
        }

        notificationBuilder.setAutoCancel(true)
            .setOnlyAlertOnce(true)
            .setContentTitle(params.get("title"))
            .setContentText(params.get("body"))
            .setColor(ContextCompat.getColor(this, android.R.color.transparent))
            .setDefaults(Notification.DEFAULT_ALL)
            .setWhen(System.currentTimeMillis())
            .setSmallIcon(smallIcon)
            .setLargeIcon(BitmapFactory.decodeResource(getResources(), R.mipmap.ic_launcher))
            .setSound(uri)
            .setContentIntent(intent)

        mNotificationManager.notify(1000, notificationBuilder.build())
    }

    companion object {
        private const val TAG = "FirebaseNotification"
    }
}
