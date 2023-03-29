<?php
/**
 * Created by PhpStorm.
 * User: echri
 * Date: 03/11/2020
 * Time: 15:27
 */

require_once '../../model/ModelSetting.php';

/**
 * Saat skrg yg di pakai hanya simple on/off saja dgn 1 account email & password,
 * nantinya akan berkembang menjadi perdevice email & password
 *
 */
$autoLogin = ModelSetting::getDimsumAutoLogin();

if ($autoLogin==1){
    $email = "madeira@dimsum.my";
    $password = "dimsum@12345";
} else {
    $email = "";
    $password = "";
}

echo json_encode([ 'email'=>$email, 'password'=>$password]);
