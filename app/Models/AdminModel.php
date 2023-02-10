<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 2/9/2023
 * Time: 1:59 PM
 */

namespace App\Models;

class AdminModel extends BaseModel
{
    protected $table      = 'tadmin';
    protected $primaryKey = 'user_id';

    /**
     * get 1 user
     *
     * @param $username
     * @return array|null|object
     */
    public function get($username){
        $r = $this->where('username', $username)->find();
        if ($r!=null) return $r[0];
        return null;
    }


}