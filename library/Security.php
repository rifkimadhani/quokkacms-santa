<?php
class Security{

    public static function randomString(int $panjang) : string {
        $karakter = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz~!@#$%^&*()_+`=-[]{}|;':\",.<>?/";
        $string = '';
        for($i = 0; $i < $panjang; $i++){
            $pos = rand(0,strlen($karakter)-1);
            $string .= $karakter[$pos];
        }

        return $string;
    }

    public static function random(int $panjang) : string {
		$karakter = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
		$string = '';
		for($i = 0; $i < $panjang; $i++){
			$pos = rand(0,strlen($karakter)-1);
			$string .= $karakter[$pos];
		}
		
		return $string;
	}

    public static function randomNumeric(int $panjang) : string {
        $karakter = "012345678901234567890123456789";
        $string = '';
        for($i = 0; $i < $panjang; $i++){
            $pos = rand(0,strlen($karakter)-1);
            $string .= $karakter[$pos];
        }

        return $string;
    }

    function generateApiKey(){
		return md5(uniqid(rand(), true));
	}
	
	public static function genHash($username, $password){
		$arUsername = unpack('C*', $username);
		$arPassword = unpack('C*', $password);
		
		//		var_dump($arPassword);
		//echo "<br>";
		
		$lenUsername = count($arUsername);
		$lenPassword = count($arPassword);
		
		$results = "";
		
		if ($lenUsername>=$lenPassword) {
			$usernameLongger = true;
			$loopCount = $lenUsername ;
		}else {
			$usernameLongger = false;
			$loopCount = $lenPassword;
		}
		
		for($idx=0; $idx<$loopCount; $idx++){
			if ($usernameLongger){
				$b1 = $arUsername[$idx+1];
				$b2 = $arPassword[($idx % $lenPassword)+1];
			} else {
				$b1 = $arUsername[($idx % $lenUsername)+1];
				$b2 = $arPassword[$idx+1];
			}
			$result = $b1 ^ $b2;
			//echo $idx . "->>" . $b1 . "^" . $b2 . " = " . $result ."<br>";
			$results .=  chr($result);
		}
		
		$base64 = base64_encode(hash('sha256', $results, true));
		
		return $base64;
	}

	/** Merge to string and return
	 * @param $string1
	 * @param $string2
	 * @return string --> merged string
	 */
	public static function mergeString($string1, $string2) : string {
		$arUsername = unpack('C*', $string1);
		$arPassword = unpack('C*', $string2);

		$lenUsername = count($arUsername);
		$lenPassword = count($arPassword);

		$result = "";

		if ($lenUsername>=$lenPassword) {
			$usernameLongger = true;
			$loopCount = $lenUsername ;
		}else {
			$usernameLongger = false;
			$loopCount = $lenPassword;
		}

		for($idx=0; $idx<$loopCount; $idx++){
			if ($usernameLongger){
				$b1 = $arUsername[$idx+1];
				$b2 = $arPassword[($idx % $lenPassword)+1];
			} else {
				$b1 = $arUsername[($idx % $lenUsername)+1];
				$b2 = $arPassword[$idx+1];
			}
			$merged = $b1 ^ $b2;
			$result .=  chr($merged);
		}

		return $result;
	}


    /**
     * @param $ar1
     * @param $ar2
     * @return string
     */
    public static function xor64($ar1, $ar2){
        $len1 = strlen($ar1);
        $len2 = strlen($ar2);

        //return null apabila len1 / len2 0
        if ($len1==0) return null;
        if ($len2==0) return null;

        $ar1 = unpack('C*', $ar1);
        $ar2 = unpack('C*', $ar2);

        $result = "";

        for($idx=0; $idx<64; $idx++){
            $b1 = $ar1[($idx % $len1)+1]; //array starts dari 1
            $b2 = $ar2[($idx % $len2)+1];

            $merged = $b1 ^ $b2;
            $result .=  chr($merged);
        }

        return $result;
    }
}


