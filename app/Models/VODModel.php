<?php
/**
 * Created by PageBuilder
 * Date: 2023-05-10 11:42:27
 */
namespace App\Models;

use App\Libraries\SSP;

class VODModel extends BaseModel
{
    const VIEW = 'vvod';

    const SQL_GET = 'SELECT * FROM tvod WHERE (vod_id=?)';
    const SQL_SSP = 'select `tvod`.`vod_id` AS `vod_id`,`tvod`.`title` AS `title`,group_concat(`tgenre`.`genre` separator \',\') AS `genres`,`tvod`.`description` AS `description`,`tvod`.`rating_value` AS `rating_value`,`tvod`.`rating_count` AS `rating_count`,`tvod`.`image_poster` AS `image_poster`,`tvod`.`url_stream1` AS `url_stream1`,`tvod`.`url_poster` AS `url_poster`,`tvod`.`url_trailer` AS `url_trailer`,`tvod`.`duration` AS `duration`,`tvod`.`price` AS `price`,`tvod`.`year_release` AS `year_release`,`tvod`.`production` AS `production`,`tvod`.`create_date` AS `create_date`,`tvod`.`update_date` AS `update_date` from ((`tvod` join `tvod_genre` on(`tvod_genre`.`vod_id` = `tvod`.`vod_id`)) join `tgenre` on(`tgenre`.`genre_id` = `tvod_genre`.`genre_id`)) group by `tvod`.`vod_id`';
    // const SQL_MODIFY = 'UPDATE tvod SET title=?, description=?, rating_value=?, rating_count=?, url_stream1=?, url_poster=?, url_trailer=?, duration=?, price=?, year_release=?, currency=?, production=?, mpaa_rating=?, lang_code=? WHERE (vod_id=?)';
    // const SQL_MODIFY_GENRE = 'UPDATE tvod_genre SET genre_id=? WHERE vod_id=?';

    protected $table      = 'tvod';
    protected $primaryKey = 'vod_id';
    protected $allowedFields = ['title', 'description', 'rating_value', 'rating_count', 'image_poster', 'url_stream1', 'url_poster', 'url_trailer', 'duration', 'price', 'path_poster', 'path_trailer', 'path_stream1', 'create_date', 'update_date', 'year_release', 'currency', 'production', 'mpaa_rating', 'lang_code'];

    public $errCode;
    public $errMessage;

    public function get($vodId)
    {   
        // OLD
        $r = $this
            ->where('vod_id', $vodId)
            ->find();
        if ($r!=null) return $r[0];

        return null;

        // NEW
        // $r = $this->db->query(self::SQL_GET, [$vodId])->getResult('array');
        // if ($r!=null) return $r[0];

        // return null;


        // LATEST
        // $vodData = $this->db->query(self::SQL_GET, [$vodId])->getResult('array');
        // if ($vodData != null) {
        //     // Fetch the genre_ids from tvod_genre for the given vod_id
        //     $genreIds = $this->db->table('tvod_genre')
        //         ->select('genre_id')
        //         ->where('vod_id', $vodId)
        //         ->get()
        //         ->getResultArray();
    
        //     $vodData[0]['genre'] = array_column($genreIds, 'genre_id');
    
        //     return $vodData[0];
        // }
    
        // return null;
    }


    public function getAll(){
        return $this->findAll();
    }

    public function getFieldList(){
        return ['vod_id', 'title', 'genres', 'description', 'rating_value', 'rating_count', 'image_poster', 'url_stream1', 'url_poster', 'url_trailer', 'duration', 'price', 'year_release', 'production', 'create_date', 'update_date'];
    }

    public function add($value)
    {
        try {
            $sanitizedValue = [];
            foreach ($value as $key => $val) {
                if (is_array($val)) {
                    // If the value is an array, recursively sanitize its elements
                    $sanitizedValue[$key] = $this->sanitizeArray($val);
                } else {
                    // Sanitize the value using htmlentities
                    $sanitizedValue[$key] = htmlentities($val, ENT_QUOTES, 'UTF-8');
                }
            }

            // Insert into tvod table
            $vodData = [
                'title' => $sanitizedValue['title'],
                'description' => $sanitizedValue['description'],
                'rating_value' => $sanitizedValue['rating_value'],
                'rating_count' => $sanitizedValue['rating_count'],
                'url_poster' => $sanitizedValue['url_poster'],
                'url_stream1' => $sanitizedValue['url_stream1'],
                'url_trailer' => $sanitizedValue['url_trailer'],
                'duration' => $sanitizedValue['duration'],
                'currency' => $sanitizedValue['currency'],
                'price' => $sanitizedValue['price'],
                'year_release' => $sanitizedValue['year_release'],
                'production' => $sanitizedValue['production'],
                'mpaa_rating' => $sanitizedValue['mpaa_rating'],
                'lang_code' => $sanitizedValue['lang_code']
            ];
            parent::insert($vodData);

            // Get the inserted vod_id
            $vodId = $this->db->insertID();

            // Get the genre_ids from the tgenre table
            $genreIds = [];
            foreach ($sanitizedValue['genre'] as $genre) {
                // Cast the genre ID to an integer using (int)
                $genreIds[] = (int) $genre;
            }

            // Insert into tvod_genre table for each genre_id
            foreach ($genreIds as $genreId) {
                $vodGenreData = [
                    'vod_id' => $vodId,
                    'genre_id' => $genreId
                ];
                $this->db->table('tvod_genre')->insert($vodGenreData);
            }
        } catch (\Exception $e) {
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return 0;
        }

        return $this->db->affectedRows();
    }

    /**
     * The function recursively sanitizes an array by applying htmlentities to its elements.
     * 
     * @param array The input array that needs to be sanitized.
     * 
     * @return a sanitized array where all the values have been sanitized using htmlentities function.
     */
    private function sanitizeArray($array)
    {
        $sanitizedArray = [];
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                // If the value is an array, recursively sanitize its elements
                $sanitizedArray[$key] = $this->sanitizeArray($val);
            } else {
                // Sanitize the value using htmlentities
                $sanitizedArray[$key] = htmlentities($val, ENT_QUOTES, 'UTF-8');
            }
        }
        return $sanitizedArray;
    }
    

    /**
     * update dgn cara PDO, karena dgn cara ci4 tdk ada rowCount, shg tdk tahu apakah update berhasil atau tdk
     *
     * @param $id
     * @param $name
     * @param $status
     * @return \PDOException|\Exception|int => 0/1 = count update, -1 = pdo exception
     */
    
    // public function modify($value)
    // {
    //     $this->errCode = '';
    //     $this->errMessage = '';

    //     $vodId = $value['vod_id'];

    //     $title = htmlentities($value['title'], ENT_QUOTES, 'UTF-8');
    //     $genre = (int) $value['genre']; // Cast genre_id to integer
    //     // $genre = htmlentities($value['genre'], ENT_QUOTES, 'UTF-8');
    //     $description = htmlentities($value['description'], ENT_QUOTES, 'UTF-8');
    //     $ratingValue = $value['rating_value'];
    //     $ratingCount = $value['rating_count'];
    //     $urlPoster = htmlentities($value['url_poster'], ENT_QUOTES, 'UTF-8');
    //     $urlStream1 = htmlentities($value['url_stream1'], ENT_QUOTES, 'UTF-8');
    //     $urlTrailer = htmlentities($value['url_trailer'], ENT_QUOTES, 'UTF-8');
    //     $duration = $value['duration'];
    //     $currency = htmlentities($value['currency'], ENT_QUOTES, 'UTF-8');
    //     $price = htmlentities($value['price'], ENT_QUOTES, 'UTF-8');
    //     $yearRelease = $value['year_release'];
    //     $production = htmlentities($value['production'], ENT_QUOTES, 'UTF-8');
    //     $mpaaRating = htmlentities($value['mpaa_rating'], ENT_QUOTES, 'UTF-8');
    //     $langCode = htmlentities($value['lang_code'], ENT_QUOTES, 'UTF-8');

    //     try {
    //         $pdo = $this->openPdo();

    //         $pdo->beginTransaction();

    //         $stmt = $pdo->prepare(self::SQL_MODIFY);
    //         $stmt->execute([$title, $description, $ratingValue, $ratingCount, $urlStream1, $urlPoster, $urlTrailer, $duration, $price, $yearRelease, $currency, $production, $mpaaRating, $langCode, $vodId]);
            
    //         $stmt_genre = $pdo->prepare(self::SQL_MODIFY_GENRE);
    //         $stmt->execute([$genre, $vodId]);

    //         $pdo->commit();

    //         return $stmt->rowCount();
    //     } catch (\PDOException $e) {
    //         log_message('error', json_encode($e));
    //         $this->errCode = $e->getCode();
    //         $this->errMessage = $e->getMessage();
    //         $pdo->rollBack();
    //         return -1;
    //     }
    // }

    public function modify($vodId, $value)
    {
        try {
            $sanitizedValue = [];
            foreach ($value as $key => $val) {
                if (is_array($val)) {
                    // If the value is an array, recursively sanitize its elements
                    $sanitizedValue[$key] = $this->sanitizeArray($val);
                } else {
                    // Sanitize the value using htmlentities
                    $sanitizedValue[$key] = htmlentities($val, ENT_QUOTES, 'UTF-8');
                }
            }

            // Update tvod table
            $vodData = [
                'title' => $sanitizedValue['title'],
                'description' => $sanitizedValue['description'],
                'rating_value' => $sanitizedValue['rating_value'],
                'rating_count' => $sanitizedValue['rating_count'],
                'url_poster' => $sanitizedValue['url_poster'],
                'url_stream1' => $sanitizedValue['url_stream1'],
                'url_trailer' => $sanitizedValue['url_trailer'],
                'duration' => $sanitizedValue['duration'],
                'currency' => $sanitizedValue['currency'],
                'price' => $sanitizedValue['price'],
                'year_release' => $sanitizedValue['year_release'],
                'production' => $sanitizedValue['production'],
                'mpaa_rating' => $sanitizedValue['mpaa_rating'],
                'lang_code' => $sanitizedValue['lang_code']
            ];
            parent::update($vodId, $vodData);

            // Delete existing genre associations
            $this->db->table('tvod_genre')->where('vod_id', $vodId)->delete();

            // Get the genre_ids from the tgenre table
            $genreIds = [];
            foreach ($sanitizedValue['genre'] as $genre) {
                // Cast the genre ID to an integer using (int)
                $genreIds[] = (int) $genre;
            }

            // Insert into tvod_genre table for each genre_id
            foreach ($genreIds as $genreId) {
                $vodGenreData = [
                    'vod_id' => $vodId,
                    'genre_id' => $genreId
                ];
                $this->db->table('tvod_genre')->insert($vodGenreData);
            }
        } catch (\Exception $e) {
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return 0;
        }

        return $this->db->affectedRows();
    }



    public function remove($vodId){
        try {
            // Delete from tvod_genre table
            $this->db->table('tvod_genre')->where('vod_id', $vodId)->delete();
    
            // Delete from tvod table
            $r = $this->where('vod_id', $vodId)->delete();
    
            return $this->db->affectedRows();
        } catch (\Exception $e) {
            $this->errCode = $e->getCode();
            $this->errMessage = $e->getMessage();
            return 0;
        }
    }

    /**
     * di pakai utk datatables.js
     *
     * @return mixed
     */
    public function getSsp()
    {
        return $this->_getSspCustom(self::SQL_SSP, $this->getFieldList());
    }
}
