<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 6/13/2023
 * Time: 10:22 AM
 */

namespace App\Controllers;

use App\Libraries\DateUtil;
use App\Models\StatModel;

class StatLiveTv extends BaseController
{
    const GROUP_0 = 'LIVETV';

    public function index()
    {
        $mainview = 'statistic/livetv';
        $pageTitle = 'Statistic';

        $dtCurrent = (empty($_GET['_date']) ? date('Y-m-d') : $_GET['_date']);

        $stat = new StatModel();

        $dt1 = $dtCurrent;
        $dt2 = DateUtil::getTomorrow($dtCurrent);
        $date = date('d F Y', strtotime($dt1));

        //Active time
        $dataActiveTimeDaily = array();
        for($hour=0; $hour<24; $hour++){
            $list = $stat->getDistinctSubscriber(self::GROUP_0, "{$hour}:00:00", "{$hour}:59:59", $dt1, $dt2);

            if (sizeof($list)==0){
                $total = 0;
            } else {
                $total = (float) ($list['count']); //second --> hour
            }

            $strHour = sprintf("%02d", $hour);
            $dataActiveTimeDaily[] = [ 'label'=>"$strHour", 'y'=>$total ];
        }
        $dataActiveTimeDaily = json_encode($dataActiveTimeDaily);

        //MONTHLY========================================================================================================

        $dataActiveTimeMonthly = array();

        $dt1 = DateUtil::get1stDayOfMonth($dtCurrent); //tgl satu pada bulan
        $dt2 = DateUtil::getTomorrow($dt1);

        $nextMonth = DateUtil::addMonth($dt1, 1);
        $days = DateUtil::countDays($nextMonth, $dt1); //hitung ada berapa hari dalam periode ini

        for($day=0; $day<$days; $day++){
            $list = $stat->getDistinctSubscriber(self::GROUP_0, "00:00:00", "23:59:59", $dt1, $dt2);

            if (sizeof($list)==0){
                $total = 0;
            } else {
                $total = (float) ($list['count']);
            }

//			$label = DateUtil::getNameOfDays($dt1) .' '. DateUtil::getDayOfMonth($dt1);
            $label = $day+1;
            $dataActiveTimeMonthly[] = [ 'label'=>$label, 'y'=>$total ];

            $dt1 = DateUtil::getTomorrow($dt1);
            $dt2 = DateUtil::getTomorrow($dt2);
        }
        $dataActiveTimeMonthly = json_encode($dataActiveTimeMonthly);


        //YEAR========================================================================================================
        $dataActiveTimeYearly = array();

        $dt1 = DateUtil::get1stDayOfYear($dtCurrent); //tgl satu pada bulan
        $dt2 = DateUtil::addMonth($dt1,1);

        for($month=0; $month<12; $month++){
            $list = $stat->getDistinctSubscriber(self::GROUP_0, "00:00:00", "23:59:59", $dt1, $dt2);

            if (sizeof($list)==0){
                $total = 0;
            } else {
                $total = (float) ($list['count']);
            }

            $label = DateUtil::getMonthName($dt1);
            $dataActiveTimeYearly[] = [ 'label'=>$label, 'y'=>$total ];

            $dt1 = $dt2;
            $dt2 = DateUtil::addMonth($dt1,1);
        }
        $dataActiveTimeYearly = json_encode($dataActiveTimeYearly);


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //Fav channel
        //

        $dt1 = DateUtil::get1stDayOfYear($dtCurrent); //tgl satu pada bulan
        $dt2 = DateUtil::addYear($dt1,1);

        $list = $stat->getSumGroup1(self::GROUP_0, $dt1, $dt2);

        $dataMostWatched = array();
        foreach ($list as $row){
            if ($row['total']>0) $dataMostWatched[] = [ 'label'=>$row['group_1'], 'y'=>(float)($row['total']/3600) ]; //convert to hour
        }
        $dataMostWatched = json_encode($dataMostWatched);

        $monthName = DateUtil::getMonthName($dtCurrent);
        $dayOfMonth = DateUtil::getDayOfMonth($dtCurrent);
        $year = DateUtil::getYear($dtCurrent);

        return view('layout/template', compact('mainview', 'pageTitle', 'dtCurrent', 'date', 'monthName', 'dayOfMonth', 'year', 'dataActiveTimeDaily', 'dayOfMonth', 'dataActiveTimeYearly','dataMostWatched', 'dataActiveTimeMonthly'));
    }

}