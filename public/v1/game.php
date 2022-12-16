<?php
/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 5/15/2019
 * Time: 2:01 PM
 */
require_once __DIR__ . '/../library/Log.php';
require_once __DIR__ . '/model/ModelStbCredential.php';


Log::writeLn('==========================================================================================================');
Log::writeRequestUri();

header('Content-type: application/json; charset=utf-8');

$stbId = ModelStbCredential::check();

$action = (empty($_GET['action']) ? '' : $_GET['action']);

switch ($action){
	case 'get_list':
		doGetList();
		break;
}

die();


function doGetList(){

	$json = <<< JSON
{
  "count": 6,
  "url_download": "{BASE-HOST}/assets/game/",
  "list": [
    {
      "icon": "snake_icon.png",
      "flash": "snake.swf",
      "html" : "snake.html"
    },
    {
      "icon": "apple_icon.png",
      "flash": "apple.swf",
      "html" : "apple.html"
    },
    {
      "icon": "climb_icon.png",
      "flash": "climb.swf",
      "html" : "climb.html"
    },
    {
      "icon": "parallel_icon.png",
      "flash": "parallel.swf",
      "html" : "parallel.html"
    },
    {
      "icon": "opendoors2_icon.png",
      "flash": "opendoors2.swf",
      "html" : "opendoors2.html"
    },
    {
      "icon": "impasse_icon.png",
      "flash": "impasse.swf",
      "html" : "impasse.html"
    }
  ]
}
JSON;

	require_once 'model/ModelSetting.php';

//	$urlHost = ModelSetting::getHostApi();
//	$json= str_replace('{HOST}', $urlHost, $json);

    $baseHost = ModelSetting::getBaseHost('../'); //turun 1 level dari posisi api ini
    $json= str_replace('{BASE-HOST}', $baseHost, $json);

    echo $json;
}