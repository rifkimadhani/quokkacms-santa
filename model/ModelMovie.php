<?php

/**
 * Created by PhpStorm.
 * User: Erick
 * Date: 3/5/2019
 * Time: 10:55 AM
 */
require_once __DIR__ . '/../../config/Koneksi.php';

class ModelMovie
{
	const SQL_GET_ALL = 'SELECT * FROM tvod LIMIT :_limit OFFSET :_offset';
	const SQL_SEARCH = 'SELECT * FROM tvod WHERE title LIKE :_query LIMIT :_limit OFFSET :_offset';
	const SQL_GET_ALL_BYGENRE = 'SELECT * FROM vvod WHERE genre_id=:genreid LIMIT :_limit OFFSET :_offset';
	const SQL_SEARCH_BYGENRE = 'SELECT * FROM vvod WHERE genre_id=:genreid AND title LIKE :keyword LIMIT :_limit OFFSET :_offset';
	const SQL_GET = 'SELECT * FROM tvod WHERE vod_id=?';
	const SQL_GET_FREE = 'SELECT * FROM tvod WHERE vod_id=? AND price=0';
	const SQL_PURCHASE = 'INSERT INTO tsubscriber_vod (subscriber_id, room_id, expired_date, rent_duration, vod_id, currency, currency_sign, rent_type, title, purchase_amount, percent_tax, tax) VALUE (?,?,?,?,?,?,?,?,?,?,?,?)';
	const SQL_UPDATE_STREAM = 'UPDATE tvod SET path_stream1=?, url_stream1=?, duration=? WHERE vod_id=?';
	const SQL_UPDATE_POSTER = 'UPDATE tvod SET path_poster=?, url_poster=? WHERE vod_id=?';
	const SQL_UPDATE_RATING = 'UPDATE tvod SET rating_value=?, rating_count=? WHERE vod_id=?';

	static public function getAll(int $offset, int $limit){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMovie::SQL_GET_ALL);

			$stmt->bindValue(':_offset', $offset, PDO::PARAM_INT);
			$stmt->bindValue(':_limit', $limit, PDO::PARAM_INT);

			$stmt->execute( );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function search(int $offset, int $limit, string $query){

		try{

		    $query = "%{$query}%";

			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMovie::SQL_SEARCH);

			$stmt->bindValue(':_offset', $offset, PDO::PARAM_INT);
			$stmt->bindValue(':_limit', $limit, PDO::PARAM_INT);
			$stmt->bindValue(':_query', $query, PDO::PARAM_STR);

			$stmt->execute( );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getAllByGenre(int $genreId, int $offset, int $limit){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMovie::SQL_GET_ALL_BYGENRE);

			$stmt->bindValue(':genreid', $genreId, PDO::PARAM_INT);
			$stmt->bindValue(':_offset', $offset, PDO::PARAM_INT);
			$stmt->bindValue(':_limit', $limit, PDO::PARAM_INT);

			$stmt->execute( );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

    /**
     * cari vod dari keyword & genreid
     *
     * @param int $genreId
     * @param int $offset
     * @param int $limit
     * @param int $keyword
     * @return array|Exception|PDOException
     */
	static public function searchByGenreId(int $genreId, int $offset, int $limit, string $keyword){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMovie::SQL_SEARCH_BYGENRE);

			$stmt->bindValue(':genreid', $genreId, PDO::PARAM_INT);
			$stmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
			$stmt->bindValue(':_offset', $offset, PDO::PARAM_INT);
			$stmt->bindValue(':_limit', $limit, PDO::PARAM_INT);

			$stmt->execute( );

			$rows = $stmt->fetchAll();
			return $rows;

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function get($movieId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMovie::SQL_GET);
			$stmt->execute( [ $movieId] );

			$rows = $stmt->fetchAll();
			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function getFree($movieId){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMovie::SQL_GET_FREE);
			$stmt->execute( [ $movieId] );

			$rows = $stmt->fetchAll();
			if (count($rows)==0) return null;
			return $rows[0];

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function purchase($roomId, $subscriberId, $movieId, $title, $rentDuration, $expDate, $amount, $currency, $currencySign, $percentTax, $tax, $rentType){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMovie::SQL_PURCHASE);
			$stmt->execute( [ $subscriberId, $roomId, $expDate, $rentDuration, $movieId, $currency, $currencySign, $rentType, $title, $amount, $percentTax, $tax  ] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function updateStream(int $movieId, string $pathStream1, string $urlStream1, int $duration){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMovie::SQL_UPDATE_STREAM);
			$stmt->execute( [$pathStream1, $urlStream1, $duration, $movieId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function updatePoster(int $movieId, string $pathPoster, string $urlPoster){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(ModelMovie::SQL_UPDATE_POSTER);
			$stmt->execute( [$pathPoster, $urlPoster, $movieId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}

	static public function updateRating(int $vodId, int $rating, int $count){

		try{
			$pdo = Koneksi::create();
			$stmt = $pdo->prepare(self::SQL_UPDATE_RATING);
			$stmt->execute( [$rating, $count, $vodId] );

			return $stmt->rowCount();

		}catch (PDOException $e){
			Log::writeErrorLn($e->getMessage());
			return $e;
		}
	}
}