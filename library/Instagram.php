<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 06/10/2022
 * Time: 15:50
 */

class Instagram{

    public static function getToken($id, $secret, $redirUrl, $code){

        $parameter = [
            'client_id'=>$id,
            'client_secret'=>$secret,
            'grant_type'=>'authorization_code',
            'redirect_uri'=>$redirUrl,
            'code'=>$code
        ];

        $curl = curl_init('https://api.instagram.com/oauth/access_token');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($parameter));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * id yg di return dari api ini berbeda dgn user_id yg di terima saat getToken.
     *
     * @param $token
     * @return bool|string
     */
    public static function getInfo($token){
        $url = "https://graph.instagram.com/me?fields=id,username&access_token={$token}";
        return file_get_contents($url);
    }
}

