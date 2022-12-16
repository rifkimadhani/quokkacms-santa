<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/4/2019
 * Time: 3:41 PM
 */

require_once __DIR__ . '/../../config/Koneksi.php';

class ModelLivetv
{
    const SQL_GET = 'SELECT livetv_id, name, url_stream1, status, countryId FROM tlivetv WHERE status=\'A\' AND livetv_id=? ORDER BY  name';
    const SQL_GET_ALL_ACTIVE = 'SELECT livetv_id, name, url_stream1, status, countryId FROM tlivetv WHERE status=\'A\' ORDER BY  name';
	const SQL_GET_CATEGORY = 'SELECT * FROM tlivetv_category ORDER BY category';
	const SQL_GET_BY_PACKAGE = 'SELECT * FROM vpackage_livetv WHERE package_id=?';
	const SQL_GET_PACKAGE_BY_ARRAY = 'SELECT DISTINCT * FROM vpackage_livetv WHERE package_id IN (:ARRAY)';
	const SQL_GET_MARKETING_BANNER = 'SELECT * FROM tpackage WHERE price>0';
	const SQL_GET_CHANNEL_LIST = 'SELECT * FROM vpackage_livetv WHERE package_id=?';
	const SQL_GET_PACKAGE = 'SELECT * FROM tpackage WHERE package_id=?';
	const SQL_PURCHASE = 'INSERT INTO tsubscriber_tvpackage (subscriber_id, room_id, expired_date, package_id, rent_duration, currency, currency_sign, purchase_amount, percent_tax, tax, title, package_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)';
    const SQL_GET_RUNNING_TEXT = 'SELECT running_text FROM tlivetv WHERE name=? LIMIT 1';
    const SQL_GET_BY_NAME= 'SELECT * FROM tlivetv WHERE name=?';

    /**
     * ini sama spt getAllActive, hanya saja lbh specific
     *
     * @param $livetvId
     * @return array|Exception|PDOException
     */
    static public function getActive($livetvId){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET);
            $stmt->execute( [$livetvId] );

            return $stmt->fetchAll();

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function getAllActive(){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET_ALL_ACTIVE);
            $stmt->execute( );

            $rows = $stmt->fetchAll();
            return $rows;

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function getCategory(){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelLivetv::SQL_GET_CATEGORY);
			$stmt->execute( [ ] );

			return $stmt->fetchAll();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

	static public function getByPackage($packageId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelLivetv::SQL_GET_BY_PACKAGE);
			$stmt->execute( [ $packageId] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

	static public function getPackageByArray($ar){

		$str = implode( ",", $ar );

		$sql = str_replace(':ARRAY', $str, ModelLivetv::SQL_GET_PACKAGE_BY_ARRAY);

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare($sql);
			$stmt->execute( );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

	static public function getMarketingBanner(){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelLivetv::SQL_GET_MARKETING_BANNER);
			$stmt->execute( );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	/**
	 * utk menampilkan list channel berdasarkan packageId,
	 * tdk ada urlStream di query ini
	 *
	 * @param $packageId
	 * @return array|Exception|PDOException
	 *
	 */
	static public function getChannelList($packageId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelLivetv::SQL_GET_CHANNEL_LIST);
			$stmt->execute( [$packageId] );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getPackage($packageId){
		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelLivetv::SQL_GET_PACKAGE);
			$stmt->execute( [$packageId] );

			$rows = $stmt->fetchAll();

			if (count($rows)==0) return null;

			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function purchase($subscriberId, $roomId, $rentDuration, $price, $curr, $currSign, $percentTax, $packageId, $packageName, $title){
		$now = new DateTime();
		$expDate = $now->add(new DateInterval('PT' . $rentDuration . 'H'));
		$stamp = $expDate->format('Y-m-d H:i:s');

		$tax = $price * $percentTax / 100;

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelLivetv::SQL_PURCHASE);

     //subscriber_id, room_id, expired_date, package_id, rent_duration, currency, currency_sign, purchase_amount, percent_tax, tax, title, package_name
			$stmt->execute( [$subscriberId, $roomId, $stamp, $packageId, $rentDuration, $curr, $currSign, $price, $percentTax, $tax, $title, $packageName] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}

	}

    static public function getRunningText($liveTvName){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(ModelLivetv::SQL_GET_RUNNING_TEXT);
            $stmt->execute( [$liveTvName] );

            $rows = $stmt->fetchAll();

            if (count($rows)==0) return null;

            return $rows[0]['running_text'];

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

    static public function getByName($channel){
        try{
            $pdo = Koneksi::create();
            $stmt = $pdo->prepare(self::SQL_GET_BY_NAME);
            $stmt->execute( [$channel] );

            $rows = $stmt->fetchAll();

            if (count($rows)==0) return null;

            return $rows[0];

        }catch (PDOException $e){
            Log::writeErrorLn($e->getMessage());
            return $e;
        }
    }

}