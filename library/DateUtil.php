<?php
class DateUtil {

	const arDayFull = ['Monday','Tuesday','Wednesday','Thrusday','Friday','Saturday','Sunday'];
	const arDayAbbr = ['Mon','Tue','Wed','Thr','Fri','Sat','Sun'];

	/**
	 * Format date to use timezone
	 *
	 * @param $time
	 * @return string
	 */
    public static function formatDate($time){
        $dt = new DateTime($time);
        return $dt->format("Y-m-d H:i:sO");
    }

	public static function getDateStrByHour($duration, $now = null){
		if (is_null($now)) $now = new DateTime();
		$expDate = $now->add(new DateInterval('PT' . $duration . 'H'));
		return $expDate->format('Y-m-d H:i:s');
	}

	public static function getDayOfMonth(string $date){
		return date('j', strtotime($date));
	}

	public static function getNameOfDays(string $date, $isAbbrv=true){
		$dayOfWeek = date('N', strtotime($date));
		if ($isAbbrv) return DateUtil::arDayAbbr[$dayOfWeek-1];
		return DateUtil::arDayFull[$dayOfWeek-1];
	}

	public static function addDay(string $date, int $days){
		return date('Y-m-d', strtotime("+{$days} day", strtotime($date)));
	}
	public static function addMonth(string $date, int $month){
		return date('Y-m-d', strtotime("+{$month} month", strtotime($date)));
	}
	public static function addYear(string $date, int $year){
		return date('Y-m-d', strtotime("+{$year} year", strtotime($date)));
	}

	public static function getYesterday(string $date){
		return date('Y-m-d', strtotime('-1 day', strtotime($date)));
	}

	public static function getTomorrow(string $date){
		return date('Y-m-d', strtotime('+1 day', strtotime($date)));
	}

	/**
	 * Will return a date of monday, from current date
	 *
	 * @param string date format yyyy-mm-dd
	 */
	public static function getMondayFromDate(string $date){
		$dayOfWeek = date('N', strtotime($date));

		//tgl entry adalah hari senin, langsung return
		if ($dayOfWeek==1) return $date;

		$dayOfWeek--;

		return date('Y-m-d', strtotime("-{$dayOfWeek} day", strtotime($date)));
	}

	public static function get1stDayOfMonth(string $date){
		$month = self::getMonth($date);
		$year = self::getYear($date);
		return "{$year}-{$month}-1";
	}

	public static function get1stDayOfYear(string $date){
		$year = self::getYear($date);
		return "{$year}-1-1";
	}

	public static function countDays(string $date1, string $date2) : int{
		$startTimeStamp = strtotime($date1);
		$endTimeStamp = strtotime($date2);

		$timeDiff = abs($endTimeStamp - $startTimeStamp);

		$numberDays = $timeDiff/86400;  // 86400 seconds in one day

		// and you might want to convert to integer
		return intval($numberDays);
	}

	public static function getMonth(string $date) : int{
		return date('n', strtotime($date));
	}
	public static function getYear(string $date) : int{
		return date('Y', strtotime($date));
	}
	public static function getMonthName(string $date){
		return date('F', strtotime($date));
	}
}