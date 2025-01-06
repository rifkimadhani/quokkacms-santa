<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 1/6/2025
 * Time: 09:39
 */

namespace App\Models;

class VisitorModel extends BaseModel
{
    protected $table = 'tvisitor';
    protected $primaryKey = 'id';
    protected $allowedFields = ['ip_address', 'user_agent', 'page_url', 'visit_date', 'visit_count'];
    protected $returnType = 'array';

    public function addVisitor($page_url)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        // Check if visitor exists for today
        $existing = $this->where([
            'ip_address' => $ip,
            'page_url' => $page_url,
            'DATE(visit_date)' => date('Y-m-d')
        ])->first();

        if ($existing) {
            // Update visit count
            $this->set('visit_count', 'visit_count + 1', false)
                ->where('id', $existing['id'])
                ->update();
            return $existing['id'];
        } else {
            // Add new visit
            $data = [
                'ip_address' => $ip,
                'user_agent' => $userAgent,
                'page_url' => $page_url,
                'visit_date' => date('Y-m-d H:i:s'),
                'visit_count' => 1
            ];
            $this->insert($data);
            return $this->insertID;
        }
    }

    public function getTotalVisits($page_url = null)
    {
        if ($page_url) {
            return $this->where('page_url', $page_url)->selectSum('visit_count')->get()->getRow()->visit_count ?? 0;
        }
        return $this->selectSum('visit_count')->get()->getRow()->visit_count ?? 0;
    }

    public function getTodayVisits($page_url = null)
    {
        $this->where('DATE(visit_date)', date('Y-m-d'));
        if ($page_url) {
            $this->where('page_url', $page_url);
        }
        return $this->selectSum('visit_count')->get()->getRow()->visit_count ?? 0;
    }
}