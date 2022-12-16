<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/19/2019
 * Time: 10:18 AM
 */

require_once __DIR__ . '/../../library/Log.php';
require_once __DIR__ . '/../../config/ErrorAPI.php';
require_once __DIR__ . '/../model/ModelStb.php';

class ModelStbCredential
{
	/**
	 * apabila tdk valid maka akan die
	 *
	 * return stbId
	 */
	public static function check(){

		//ambil sessionId
		$sessionId = (empty($_GET['session_id']) ? '' : $_GET['session_id']);

		//apabila kosong maka pergunakan mac address
		if (empty($sessionId)==false) {

			$stb = ModelStb::getFromSessionId($sessionId);
			if (empty($stb)) {
				echo errCompose(ERR_SESSIONID_NOT_FOUND);
				die();
			}
			if($stb instanceof PDOException){
				echo errCompose($stb);
				die();
			}

			$stbId = $stb['stb_id'];
			ModelStb::updateStbSessionLastSeen($stbId, $sessionId);

		} else {
//			$cmd = "arp -a ".escapeshellarg($_SERVER['REMOTE_ADDR'])." | grep -o -E '(:xdigit:{1,2}:){5}:xdigit:{1,2}'";
			$cmd = "arp -a ".escapeshellarg($_SERVER['REMOTE_ADDR']);
			$str = shell_exec($cmd);

			//use regulax expression to parse str from arp
			preg_match('([0-9a-z]{2}:[0-9a-z]{2}:[0-9a-z]{2}:[0-9a-z]{2}:[0-9a-z]{2}:[0-9a-z]{2})', $str, $result );

			if (sizeof($result)==0){
				echo errCompose(ERR_MAC_ADDRESS_NOT_FOUND);
				die();
			}
			$mac = $result[0];

			$stb = ModelStb::getFromMac($mac);
			if (empty($stb)) {
				echo errCompose(ERR_MAC_ADDRESS_NOT_FOUND);
				die();
			}

			$stbId = $stb['stb_id'];
			ModelStb::updateStbMacLastSeen($stbId, $mac);
		}

		$stbId = $stb['stb_id'];
		ModelStb::updateLastSeen($stbId);
		return $stbId;
	}

}