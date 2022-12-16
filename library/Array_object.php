<?php
class Array_object{
	public static function arrayKeObject($array)
	{
		$object = new stdClass();
		if (is_array($array))
		{
			foreach ($array as $kolom=>$isi)
			{
				$kolom = strtolower(trim($kolom));
				$object->$kolom = $isi;
			}
		}
		return $object;
	}

	public static function objectKeArray($object)
	{
		$array = array();
		if (is_object($object)) {
			$array = get_object_vars($object);
		}
		return $array;
	}

	function cmp($a, $b)
{
    return strcmp($a->start, $b->start);
}
}

?>