<?php
class PECAH_EPG{
    function XMLDATA($dataXML){

error_reporting(0);
$url = $dataXML;
$xml = simplexml_load_file($url);

$attr  = '@attributes';

$json = json_encode($xml);
$array = json_decode($json,TRUE);

//$x= array();
foreach ($array['programme'] as $r){

    $arr[] =  array(
        'channelId' =>$r[$attr]['channel'],
        'start'     =>$r[$attr]['start'],
        'end'       =>$r[$attr]['stop'],
        'program'   =>$r['title'],
        'sinopsis'  =>$r['desc']
    );


}


array_multisort($arr['start'],SORT_ASC,$arr);

$tgl = array();
$item_tgl = array();
$str_tgl="";


for ($i=0; $i<count($arr); $i++){
    $d = $arr[$i];
    $a=substr($d['start'],0,8);
    if($str_tgl != $a){
        if($i>0) {
            array_push($tgl,$item_tgl);
        }
        $item_tgl = array();
        array_push($item_tgl,$d);
        $str_tgl = $a;
    }
    else{
        array_push($item_tgl,$d);

    }

}


$chanel = array();
$item_ch = array();
$str_chid="";
for($ii = 0; $ii<count($tgl); $ii++){
    $dd = $tgl[$ii];

    $sortaa = self::sortir($dd);
    array_multisort($sortaa['channelId'],SORT_ASC,$dd);




    for($x=0; $x<count($dd); $x++){
        $head = $dd[$x];
        $aa = $head['channelId'];
        if($str_chid != $aa){
            if($ii>0){
                array_push($chanel,$item_ch);
            }
            $item_ch = array();
            array_push($item_ch,$head);
            $str_chid = $aa;
        }
        else{
            array_push($item_ch,$head);
        }
    }

}
array_push($chanel,$item_ch);
$final = array();
$item_final = array();
$str_final = "";

for($f=0;$f<count($chanel);$f++){
    $nilai = $chanel[$f];
    for($ff=0;$ff<count($nilai);$ff++){
        $nilai2 = $nilai[$ff];
        $tglhasil = substr($nilai2['start'],0,8);
        if($str_final != $tglhasil){
            if($ff>0){
                array_push($final,$item_final);
            }
            $item_final = array();
            array_push($item_final, $nilai2);
            $str_final = $tglhasil;
        }
        else{
            array_push($item_final,$nilai2);
        }
    }

}

for($end1=0;$end1<count($final);$end1++){
    $finalhasil = $final[$end1];
    for($end2=0;$end2<count($finalhasil);$end2++){
        $finalasli = $finalhasil[$end2];
        //$date = strtotime($finalasli['start']);
        $time = strtotime($finalasli['start']);
        $start = strtotime(substr($finalasli['start'], 8,6));
        $end = strtotime(substr($finalasli['end'], 8,6));
        $date = date('Y-m-d',$time);
        $start = date("H:i:s",$start);
        $end = date("H:i:s",$end);
        $channelId = $finalasli['channelId'];
        $det[] = array('start'=>$start,
            'end'=>$end ,
            'program'=>$finalasli['program'],
            'sinopsis'=>$finalasli['sinopsis']);


    }
    $save[] = array(
        'id'=>$channelId,
        'urlImage1'=>NULL,
        'urlImage1'=>NULL,
        'channelName'=>$channelId,
        'date'=>$date,
        'guides'=>$det);
    unset($det);

}

$tempArr = array();

    foreach($save as $key=>$val) {
        $tempArr['date'][$key] = $val['date'];
        $tempArr['id'][$key] = $val['id'];
    }

array_multisort($tempArr['date'], SORT_ASC, $tempArr['id'], SORT_ASC,$save);

return json_encode($save);


    }
   private static function sortir($array){
        $sortArray = array();
        foreach($array as $person){
            foreach($person as $key=>$value){
                if(!isset($sortArray[$key])){
                    $sortArray[$key] = array();
                }
                $sortArray[$key][] = $value;
            }
        }

        return $sortArray;
    }
}