<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 15/12/2021
 * Time: 15:02
 */

if (defined('STATUS_AVAILABLE')==false) define('STATUS_AVAILABLE', 'AVAILABLE');

//di pakai apabila ada api error
if (defined('HTTP_BAD_REQUEST')==false) define('HTTP_BAD_REQUEST', 400);

require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/../config/ErrorAPI.php';
require_once __DIR__ . '/model/ModelStbCredential.php';

Log::writeLn('==========================================================================================================');
Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
    case 'get_product_list': //pesanan yg sudh checkout
        doGetList();
        break;
    case 'checkout':
        doCheckOut($stbId);
        break;
    case 'get_order_list':
        doGetOrderList($stbId);
        break;
    default:
        http_response_code(404);
        break;
}

exit();

function doGetList(){
    require_once __DIR__ . '/model/ModelShop.php';

//    $offset = (empty($_GET['offset']) ? 0 : $_GET['offset']);

    $listSeller = ModelShop::getAllSeller();
    $list = ModelShop::getAllAvailable();

    echo json_encode(['count_product'=>count($list), 'list'=>$list, 'count_seller'=>count($listSeller), 'sellers'=>$listSeller]);
}

function doCheckOut($stbId){
    require_once __DIR__ . '/model/ModelShop.php';
    require_once __DIR__ . '/model/ModelStb.php';
    require_once __DIR__ . '/model/ModelSetting.php';

    $pin = (empty($_GET['pin']) ? '' : $_GET['pin']);
    $payment = (empty($_GET['payment']) ? '' : $_GET['payment']);
    $json = (empty($_GET['order']) ? '' : $_GET['order']);

    //ambil room
    $room = ModelStb::get($stbId);
    if (is_null($room)){
        echo errCompose(ERR_STB_HAS_NO_ROOM);
        http_response_code(400);
        exit();
    }
    if ($room instanceof PDOException){
        echo errCompose($room);
        http_response_code(400);
        exit();
    }

    $pin2 = $room['security_pin'];

    if ($pin2!=$pin){
        echo errCompose(ERR_INVALID_SECURITY_PIN);
        http_response_code(HTTP_BAD_REQUEST);
        exit();
    }

    $subscriberId = $room['subscriber_id'];
    if (is_null($subscriberId)){
        echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
        http_response_code(HTTP_BAD_REQUEST);
        exit();
    }

    $order = json_decode($json);

    if (empty($order)){
        echo errCompose(ERR_SHOP_PRODUCT_NOT_DEFINED);
        http_response_code(HTTP_BAD_REQUEST);
        exit();
    }

    $list = $order->list;
    $note = $order->note;

    $totalAmount = 0;
    $actualOrder = array();
    foreach ($list as $item){
        $productId= $item->product_id;
        $qty = $item->qty;

        //cari product, hanya add apabile status product AVAILABLE
        $product = ModelShop::get($productId);

        if ($product!=null){
            if ($product['status']==STATUS_AVAILABLE){
                //hapus field yg tdk perlu
                unset($product['description']);
                unset($product['create_date']);
                unset($product['update_date']);
                unset($product['status']);
                unset($product['path_image']);
                $order = [ 'qty'=> $qty, 'product'=>$product];
                array_push($actualOrder, $order);
                $totalAmount += $product['price'] * $qty;
            }
        }
    }

    $json = json_encode(['count'=>count($actualOrder), 'list'=>$actualOrder]);

    $orderCode = ModelShop::genOrderCode();

    ModelShop::createOrder($orderCode, $subscriberId, $payment, $note, $totalAmount, $json);

    echo json_encode(['order_code'=>$orderCode]);
}

function doGetOrderList($stbId){
    require_once __DIR__ . '/model/ModelShop.php';
    require_once __DIR__ . '/model/ModelStb.php';
    require_once __DIR__ . '/model/ModelSetting.php';

    //ambil room
    $room = ModelStb::get($stbId);
    if (is_null($room)){
        echo errCompose(ERR_STB_HAS_NO_ROOM);
        exit();
    }
    if ($room instanceof PDOException){
        echo errCompose($room);
        exit();
    }

    $subscriberId = $room['subscriber_id'];
    if (is_null($subscriberId)){
        echo errCompose(ERR_SUBSCRIBER_NOT_DEFINED);
        exit();
    }

    $list = ModelShop::getAllOrder($subscriberId);

    foreach ($list as &$item){
        //convert dari json string ke bentuk object
        $o = json_decode($item['order_json']);
        //
        $item['list'] = $o->list;
        $item['count'] = $o->count;
        unset($item['order_json']);
    }

    echo json_encode(['count'=>count($list), 'list'=>$list]);
}