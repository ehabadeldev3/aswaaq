<?php
namespace App\Traits;

use GuzzleHttp\Client;
use Log;

/**
 * trait NotificationTrait
 */
trait NotificationTrait
{
    public $API_KEY = 'AAAAM-llB-8:APA91bF3WJ2cjiKewOs6w09QxVP2WiO0evTZcXQoU36a4Nby8Sk7xguu11rxfOW7emjugobntvlMuLRg-SJwCqwx1W5LUH4JerDflSPykphpqwzyHNA4MorcMWP-bfZ6eDxRIpHDkRGQ';
    public $title = 'اسواق';

    public function notification($tokens,$body,$type,$productData){

        $SERVER_API_KEY = $this->API_KEY;

        $data = [

            "registration_ids" => $tokens,

            "data" => [
                'type' => $type,
                'productData' => json_encode($productData),
            ],

            "notification" => [

                "title" => $this->title,

                "body" => $body,

                "sound" => "default", // required for sound on ios

                // "image" =>asset('uploads/products/'.$product -> image),

                "click_action"=> "FLUTTER_NOTIFICATION_CLICK"

            ],

        ];

        $dataString = json_encode($data);

        $headers = [

            'Authorization: key=' . $SERVER_API_KEY,

            'Content-Type: application/json',

        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);
        return $response;
    }

   
}
