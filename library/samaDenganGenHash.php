<?php
	function password($nama,$email){
	$nilai2 = array();
	$nilai1 = array();
	$results="";
	$encodedBytes = null;
	$b1 = array();
	$b2 = array();
	$sigLonger; 

	$expnama = str_split($nama);
	foreach ($expnama as $keynama) {
				$nilainama = unpack('C*',$keynama);
				$implodenama = intval(implode($nilainama));
				array_push($nilai2, $implodenama);
		}

	$expemail = str_split($email);
	foreach ($expemail as $keyemail) {
				$nilaiemail = unpack('C*',$keyemail);
				$implodeemail = intval(implode($nilaiemail));
				array_push($nilai1, $implodeemail);
		}
  	
/*Nilai Email */	$panjang1 = count($nilai1); 	
/*Nilai Nama */		$panjang2 = count($nilai2); 

		
  	if($panjang1 >= $panjang2 ){
  				$sigLonger = true;
  				$result = $panjang1;
  	}
  	else{
  				$sigLonger = false;
  				$result = $panjang2;
  			}

	for($i=0;$i < $result;$i++){
		if($sigLonger){
					$b1 = $nilai1[$i];
					$b2 = $nilai2[$i % $panjang2];
	}else{
					$b1 = $nilai1[$i % $panjang1];
					$b2 = $nilai2[$i];
		}
				$r =  $b1 ^ $b2;
				$results .= chr($r);
	}  
	
	try{
		$md = hash('SHA256',$results, true);
		$encodedBytes = base64_encode($md);
		
		}
	catch(exception $e){
			echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	
	return $encodedBytes;
  }

