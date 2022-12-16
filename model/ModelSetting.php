<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/20/2019
 * Time: 10:34 AM
 */

require_once __DIR__ . '/../config/Koneksi.php';
require_once __DIR__ . '/../library/Log.php';

class ModelSetting
{
	const SQL_GET_STRING = 'SELECT value_string FROM tsetting WHERE setting_id=?';
	const SQL_GET_INT = 'SELECT value_int FROM tsetting WHERE setting_id=?';
	const SQL_GET = 'SELECT value_int, value_string, value_float FROM tsetting WHERE setting_id=?';
	const SQL_SET = 'UPDATE tsetting SET value_int=?, value_string=?, value_float=? WHERE setting_id=?';

    const REPLACE_BASE_PATH_VOD = '{BASEPATH-VOD}';

    const SETTING_HOST_API = 1;
	const SETTING_EMGERCENCY_STATE = 2;
	const SETTING_PATH_APPLICATION = 3;
	const SETTING_PATH_UPLOAD_VIDEO = 4;

	const SETTING_EMAIL_FROM= 8;

	const SETTING_DEFAULT_CURRENCY = 10;
	const SETTING_DEFAULT_CURRENCY_SIGN = 11;
	const SETTING_TIMEZONE = 14;
	const SETTING_WEATHERLOCALCITY = 15;

	const SETTING_HOST_VOD = 21;
	const SETTING_HOST_TV = 22;

	const SETTING_BASEPATH_VOD = 30;
	const SETTING_PATH_VOD = 31;
	const SETTING_URL_VOD_VIDEO = 32;
	const SETTING_URL_VOD_THUMBNAIL = 33;

	const SETTING_PATH_CONTENT = 40; //video & image utk locality / facility / messag /ads akan di taruh di path ini
	const SETTING_URL_CONTENT_VIDEO = 41;
	const SETTING_URL_CONTENT_THUMBNAIL = 42;

	const SETTING_SITE_NAME = 100;
	const SETTING_WELCOME_MESSAGE = 101;
	const SETTING_SITE_ADDRESS = 200;

	const SETTING_TAX_VOD = 310;

	const SETTING_WEATHER_SERVER = 400;

	const SETTING_DIMSUM_AUTOLOGIN = 501;

	const SETTING_DEFAULT_THEMEID = 1000;
	const SETTING_DEFAULT_PACKAGEID = 1010;
	const SETTING_DEFAULT_KITCHENID = 1020;

    const SETTING_FEATURE_APP_MENULIST = 2000;
    const SETTING_FEATURE_MOREAPP_LIST= 2002;

    const SETTING_MAILGUN_DOMAIN = 2010;
    const SETTING_MAILGUN_KEY = 2012;

    const SETTING_FEATURE_KITCHEN = 3001;
    const SETTING_FEATURE_MARKETING = 3002; //vod & karaoke
    const SETTING_FEATURE_DIMSUM = 3003;
    const SETTING_FEATURE_LIVETV_STAT = 3004;

    //cache
	static public $pathApplication = null;
	static public $hostApi = null;
	static public $hostVod = null;
    static public $basePath = null;
    static public $baseUrl = null;

	static public $basePathVod = null;
	static public $pathVod = null;

	static public function getString($settingId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelSetting::SQL_GET_STRING);
			$stmt->execute( [$settingId] );

			$rows = $stmt->fetchAll();
			if (count($rows)==0) return null;

			return $rows[0]['value_string'];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getInt($settingId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelSetting::SQL_GET_INT);
			$stmt->execute( [$settingId] );

			$rows = $stmt->fetchAll();
			if (count($rows)==0) return null;

			return $rows[0]['value_int'];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function get($settingId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelSetting::SQL_GET);
			$stmt->execute( [$settingId] );

			$rows = $stmt->fetchAll();
			if (count($rows)==0) return null;

			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

    /**
     * update tsetting
     * isikan type value yg dibutuhkan saja, sisanya di set null
     *
     * @param $settingId
     * @param $intValue = nullable
     * @param $strValue = nullable
     * @param $floatValue = nullable
     * @return Exception|int|PDOException, 1= success
     */
	static public function set($settingId, $intValue, $strValue, $floatValue){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelSetting::SQL_SET);
            $stmt->execute( [$intValue, $strValue, $floatValue, $settingId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getDefaultThemeId(){
		return ModelSetting::getInt(ModelSetting::SETTING_DEFAULT_THEMEID);
	}

	static public function getDefaultPackageId(){
		return ModelSetting::getInt(ModelSetting::SETTING_DEFAULT_PACKAGEID);
	}

	static public function getSiteName(){
		return ModelSetting::getString(ModelSetting::SETTING_SITE_NAME);
	}

	static public function getSiteAddress(){
		return ModelSetting::getString(ModelSetting::SETTING_SITE_ADDRESS);
	}

	static public function getDefaultKitchenId(){
		return ModelSetting::getInt(ModelSetting::SETTING_DEFAULT_KITCHENID);
	}

	static public function getEmailFrom(){
		return ModelSetting::getString(ModelSetting::SETTING_EMAIL_FROM);
	}
	static public function getDefaultCurrency(){
		return ModelSetting::getString(ModelSetting::SETTING_DEFAULT_CURRENCY);
	}
	static public function getDefaultCurrencySign(){
		return ModelSetting::getString(ModelSetting::SETTING_DEFAULT_CURRENCY_SIGN);
	}

	static public function getTimezone(){
		return ModelSetting::getString(ModelSetting::SETTING_TIMEZONE);
	}

	static public function getHostApi(){
//		if (is_null(ModelSetting::$hostApi)) ModelSetting::$hostApi = ModelSetting::getString(ModelSetting::SETTING_HOST_API);
//		return ModelSetting::$hostApi;
		return ModelSetting::getString(ModelSetting::SETTING_HOST_API);;
	}
	static public function getHostVod(){
		if (is_null(ModelSetting::$hostVod)) ModelSetting::$hostVod= ModelSetting::getString(ModelSetting::SETTING_HOST_VOD);
		return ModelSetting::$hostVod;
	}
	static public function getHostTv(){
		return ModelSetting::getString(ModelSetting::SETTING_HOST_TV);
	}

	static public function getEmergencyState(){
		return ModelSetting::get(ModelSetting::SETTING_EMGERCENCY_STATE);
	}

	static public function getTaxMovie(){
		$ar = ModelSetting::get(ModelSetting::SETTING_TAX_VOD);
		return $ar['value_float'];
	}

	static public function getPathUploadVideo(){
		return ModelSetting::getString(ModelSetting::SETTING_PATH_UPLOAD_VIDEO);
	}

	static public function getPathVideo(){
		return ModelSetting::getString(ModelSetting::SETTING_PATH_VIDEO);
	}

	static public function getPathApplication(){
		if (is_null(ModelSetting::$pathApplication)) ModelSetting::$pathApplication = ModelSetting::getString(ModelSetting::SETTING_PATH_APPLICATION);
		return ModelSetting::$pathApplication;
	}

	/**
	 * path content di pakai utk video & image (locality / facility / message dan ads)
	 * @return mixed
	 */
	static public function getPathContent(){
//		$pathApp = self::getPathApplication();
		$path = ModelSetting::getString(ModelSetting::SETTING_PATH_CONTENT);
		return $path;//str_replace('{PATH-APP}', $pathApp, $path);
	}

	static public function getUrlContentVideo(){
//		$host = self::getHostVod();
		$url = ModelSetting::getString(ModelSetting::SETTING_URL_CONTENT_VIDEO);
		return $url;//pstr_replace('{HOST-VOD}', $host, $path);
	}

	static public function getUrlContentThumbnail(){
//		$host = self::getHostApi();
		$url = ModelSetting::getString(ModelSetting::SETTING_URL_CONTENT_THUMBNAIL);
		return $url;//str_replace('{HOST-API}', $host, $path);
	}

//	static public function getBasePathVod(){
//		if (is_null(ModelSetting::$basePathVod)) {
//			ModelSetting::$basePathVod = ModelSetting::getString(ModelSetting::SETTING_BASEPATH_VOD);
//		};
//		return ModelSetting::$basePathVod;
//	}
//	static public function getPathVod(){
//		if (is_null(ModelSetting::$pathVod))
//		{
//			ModelSetting::$pathVod = ModelSetting::getString(ModelSetting::SETTING_PATH_VOD);
//			$basePath = ModelSetting::getBasePathVod();
//			ModelSetting::$pathVod = str_replace('{BASEPATH-VOD}', $basePath, ModelSetting::$pathVod);
//		}
//		return ModelSetting::$pathVod;
//	}
	static public function getBasePathVod(){
		ModelSetting::$basePathVod = ModelSetting::getString(ModelSetting::SETTING_BASEPATH_VOD);
		return ModelSetting::$basePathVod;
	}
	static public function getPathVod(){
		ModelSetting::$pathVod = ModelSetting::getString(ModelSetting::SETTING_PATH_VOD);
		$basePath = ModelSetting::getBasePathVod();
		ModelSetting::$pathVod = str_replace(ModelSetting::REPLACE_BASE_PATH_VOD, $basePath, ModelSetting::$pathVod);
		return ModelSetting::$pathVod;
	}
	static public function getUrlVodVideo(){
		$path = ModelSetting::getString(ModelSetting::SETTING_URL_VOD_VIDEO);
		return $path;
	}
	static public function getUrlVodThumbnail(){
		$path = ModelSetting::getString(ModelSetting::SETTING_URL_VOD_THUMBNAIL);
		return $path;
	}

	static public function getWeatherServer(){
		$path = ModelSetting::getString(ModelSetting::SETTING_WEATHER_SERVER);
		return $path;
	}

    /**
     * Utk dapetin base host.
     * Base host di dapat dari url di invoke
     * apabila di invoke http://localhost/ott2/v1/api.php --> http://localhost/ott2
     * apabila di invoke http://127.0.0.1/ott2/v1/api.php --> http://127.0.0.1/ott2
     *
     * jadi bergantung dari domain yg di pakai saat hit api
     * level path juga bergantung dari php yg di hit
     *
     * @return null
     */
    static public function getBaseHost($path){
        require_once __DIR__ . '/../../library/Util.php';

//        if (is_null(ModelSetting::$baseUrl)) {
//            ModelSetting::$baseUrl = Util::baseUrl($path);
//        }
        return Util::baseUrl($path);
    }

    /**
     * Path berdasarkan instalasi app
     *
     * @return null
     */
    static public function getBasePath($pathRelative){
        if (is_null(ModelSetting::$basePath)) {
            ModelSetting::$basePath = realpath(__DIR__ . $pathRelative); //__DIR__ = PATH dari file ini
        }
        return ModelSetting::$basePath;
    }

    static public function getDimsumAutoLogin(){
        return ModelSetting::getInt(ModelSetting::SETTING_DIMSUM_AUTOLOGIN);
    }

    /**
     * @param int $status
     * @return Exception|int|PDOException
     */
    static public function setDimsumAutoLogin(int $status){
        //hanya perlu update type int saja, yg lain di null kan
        return ModelSetting::set(ModelSetting::SETTING_DIMSUM_AUTOLOGIN, $status, null, null);
    }

    static public function getWelcomeMessage(){
        return ModelSetting::getString(ModelSetting::SETTING_WELCOME_MESSAGE);
    }

    static public function getFeatureKitchen(){
        return ModelSetting::getInt(ModelSetting::SETTING_FEATURE_KITCHEN);
    }
    static public function getFeatureMarketing(){
        return ModelSetting::getInt(ModelSetting::SETTING_FEATURE_MARKETING);
    }
    static public function getFeatureDimsum(){
        return ModelSetting::getInt(ModelSetting::SETTING_FEATURE_DIMSUM);
    }
    static public function getFeatureLivetvStat(){
        return ModelSetting::getInt(ModelSetting::SETTING_FEATURE_LIVETV_STAT);
    }
    static public function getWeatherLocalCityId(){
        return ModelSetting::getString(ModelSetting::SETTING_WEATHERLOCALCITY);
    }

    static public function getFeatureAppMenuList(){
        return ModelSetting::getString(ModelSetting::SETTING_FEATURE_APP_MENULIST);
    }
    static public function getFeatureMoreAppList(){
        return ModelSetting::getString(ModelSetting::SETTING_FEATURE_MOREAPP_LIST);
    }

    static public function getMailgunDomain(){
        return ModelSetting::getString(self::SETTING_MAILGUN_DOMAIN);
    }
    static public function getMailgunKey(){
        return ModelSetting::getString(self::SETTING_MAILGUN_KEY);
    }
}