<?php

namespace App\Libs;

class FirebaseMessaging
{

    private $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    private $fcmNotification = [];
    private $headers = [];
    private $channel = "/topics/alerts";
    private $serverKey = "AAAAZIIIdUE:APA91bFYT7eEc_oo-avRjw_9_pFXbscU8CQ-OdC2KkaG6mj3LEF8nUagV_A8iz3AxCXM0p2SeOLfj_z1HN2n0YvHG0rntlJuqBaNXkenUY0ASYAj1VUAMP4N9EPU5VJUqOeHq-RXcaer";

    function __construct($channel) {
        $this->setHeader();
        if(!empty($channel)) $this->setChannel($channel);
    }

    private function setChannel($channel)
    {
        $this->channel = $channel;
    }

    private function setHeader()
    {
        $this->headers= [
            'Authorization: key=' . $this->serverKey,
            'Content-Type: application/json'
        ];
    }

    function setMessage($title, $body, $request_type = "mobile_recharge_request_received"):FirebaseMessaging
    {
        $this->fcmNotification= [
            "to" => $this->channel,//"dEVws-4tRCW0vgM7I_w3fk:APA91bEQnlbUVxdI1_8QUFSZ1kxjbmavhFrZzeJjBn9n6vYJlnIPt5Dt9vVIpbY3mXySwOwXZiZEolH58KMf5Lg5IUs-LCChUS-XHklvmLUDuDKQuoJ-iAwzsCPhCz2bv2CXrFE8Jvfj",
            "data" => [
                "title" => $title, //"New Recharge Request",
                "body" =>$body, //"A recharge request received of bKash for BDT 25,000/= on 01758930809.",
                "description"=>"",
                "priority" => "high",
                "request_type" => $request_type,
                "data_type"=>"notification",
                "content_available" => true

                //{"priority":"high","body":"2","title":"1","request_type":"mobile_recharge_request_approved mobile_recharge_request_rejected mobile_recharge_request_received"}
            ],
            "priority" => 10
        ];

        return $this;
    }

    function setDashboardNotification($title, $body, $request_type = "mobile_recharge_request_received"):FirebaseMessaging
    {
        $this->fcmNotification= [
            "to" => $this->channel,//"dEVws-4tRCW0vgM7I_w3fk:APA91bEQnlbUVxdI1_8QUFSZ1kxjbmavhFrZzeJjBn9n6vYJlnIPt5Dt9vVIpbY3mXySwOwXZiZEolH58KMf5Lg5IUs-LCChUS-XHklvmLUDuDKQuoJ-iAwzsCPhCz2bv2CXrFE8Jvfj",
            "data" => [
                "title" => $title, //"New Recharge Request",
                "body" =>$body, //"A recharge request received of bKash for BDT 25,000/= on 01758930809.",
                "description"=>"",
                "priority" => "high",
                "request_type" => $request_type,
                "data_type"=>"dashboard_notification",
                "content_available" => true

                //{"priority":"high","body":"2","title":"1","request_type":"mobile_recharge_request_approved mobile_recharge_request_rejected mobile_recharge_request_received"}
            ],
            "priority" => 10
        ];

        return $this;
    }


    function sendMessage():FirebaseMessaging {
        $cRequest = curl_init();
        curl_setopt($cRequest, CURLOPT_URL, $this->fcmUrl);
        curl_setopt($cRequest, CURLOPT_POST, true);
        curl_setopt($cRequest, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($cRequest, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cRequest, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cRequest, CURLOPT_POSTFIELDS, json_encode($this->fcmNotification));
        $result = curl_exec($cRequest);
        curl_close($cRequest);
        echo $result;

        return $this;
    }
}
