<?php
function startOfWeek($date)
{
	global $DB;
	$remainder = computeDay($date);
	if ($remainder == 0)
	{
		// occurs when saturday
		$remainder = 7;
	}
	$day_array = dayOfWeek($remainder);
	$start = $day_array["Start"];


	$year = substr($date,0,4);
	$month = substr($date,4,2);
	$day = substr($date,6,2);

		//subtract to get start
		$statement = "SELECT DATE_SUB('";
		$statement .= $year;
		$statement .= "-";
		$statement .= $month;
		$statement .= "-";
		$statement .= $day;
		$statement .= "', ";
		$statement .= "INTERVAL ";
		$statement .= $start;
		$statement .= " DAY);";
		$start_of_week = $DB->query_first($statement);

		return $start_of_week[0];

}
function endOfWeek($date)
{
	global $DB;
	$remainder = computeDay($date);
	$day_array = dayOfWeek($remainder);
	$end = $day_array["End"];

	$year = substr($date,0,4);
	$month = substr($date,4,2);
	$day = substr($date,6,2);

	if($end == 0)
	{
		$statement .= $year;
		$statement .= "-";
		$statement .= $month;
		$statement .= "-";
		$statement .= $day;
		return $statement;
	}
	else
	{
		//add to get end
		$statement = "SELECT DATE_ADD('";
		$statement .= $year;
		$statement .= "-";
		$statement .= $month;
		$statement .= "-";
		$statement .= $day;
		$statement .= "', ";
		$statement .= "INTERVAL ";
		$statement .= $end;
		$statement .= " DAY);";
		$end_of_week = $DB->query_first($statement);

		return $end_of_week[0];
	}
}
function computeDay($date)
{
	//Step 1 get last two digits of year
	$year = substr($date,2,2);

	//Step 2 divide by 4 drop remainder
	$noRemainder = dropRemainder($year, 4);

	//Step 3 add to day to remainder
	$day = substr($date,6,2);
	$runningTotal = $noRemainder + $day;

	//Step 4 add months Code to $runningTotal
	$month = substr($date,4,2);
	$runningTotal = $runningTotal + returnCode($month);

	//Step5 if jan or feb of leap year substract 1
	if($year % 4 == 0 && (strcmp($month, "01") == 0 || strcmp($month, "02") == 0))
	{
		$runningTotal = $runningTotal - 1;
	}
	//Step 6 add 6
	$runningTotal = $runningTotal + 6;

	//Step 7 add year to total
	$runningTotal = $runningTotal + $year;

	//Step 8 Divide by 7 and take the remainder.
	$remainder = $runningTotal % 7;

	//Step 9 obtain day from array
	//$day_array = dayOfWeek($remainder);
	//return $day_array["Name"];

	return $remainder;
}
function dayOfWeek($day_code)
{
	//start subtract //end add
	$day_array = array(
						"1" =>  array("Name" => "Sunday", "Start" => "0", "End" => "6"),
						"2" =>  array("Name" => "Monday", "Start" => "1", "End" => "5"),
						"3" =>  array("Name" => "Tuesday", "Start" => "2", "End" => "4"),
						"4" =>  array("Name" => "Wednesday", "Start" => "3", "End" => "3"),
						"5" =>  array("Name" => "Thursday", "Start" => "4", "End" => "2"),
						"6" =>  array("Name" => "Friday", "Start" => "5", "End" => "1"),
						"7" =>  array("Name" => "Saturday", "Start" => "6", "End" => "0")
						);
	return $day_array[$day_code];
}
function returnCode($month)
{
	$month_array = array(
					"01" => array("Code" => "1", "Name" => "January", "Days" => "31"),
					"02" => array("Code" => "4", "Name" => "February", "Days" => "28"),
					"03" => array("Code" => "4", "Name" => "March", "Days" => "31"),
					"04" => array("Code" => "0", "Name" => "April", "Days" => "30"),
					"05" => array("Code" => "2", "Name" => "May", "Days" => "31"),
					"06" => array("Code" => "5", "Name" => "June", "Days" => "30"),
					"07" => array("Code" => "0", "Name" => "July", "Days" => "31"),
					"08" => array("Code" => "3", "Name" => "August", "Days" => "31"),
					"09" => array("Code" => "6", "Name" => "September", "Days" => "30"),
					"10" => array("Code" => "1", "Name" => "October", "Days" => "31"),
					"11" => array("Code" => "4", "Name" => "November", "Days" => "30"),
					"12" => array("Code" => "6", "Name" => "December", "Days" => "31")
					);
	return $month_array[$month]["Code"];
}
function dropRemainder($divedend, $divisor)
{
	$remainder = $divedend % $divisor;
	$divedend =  $divedend - $remainder;
	$answer = $divedend / $divisor;
	return $answer;
}
?>