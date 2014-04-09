<?php
require_once('libraries/phpQuery-onefile.php');
$start_time = microtime(true);

$TOTAL_PAGES = 868; //total  # pages for men 1339;. //total for women 
$GENDER = 'female'; // or female

$fp = fopen("stats-{$GENDER}.csv", "w");
fputcsv($fp, array('Overall Rank','Score','Name', '#1 Rank', '#1 Score', '#2 Rank', '#2 Score', '#3 Rank', '#3 Score', '#4 Rank', '#4 Score', '#5 Rank', '#5 Score','#5 Time in seconds'));

for($page =1; $page <= $TOTAL_PAGES; $page++)
{
	$html = get_data($page);
	phpQuery::newDocument($html);

	$table = pq("#lbtable");
	$rows = pq('tr', $table);
	$skip_first = true;

	foreach($rows as $row){
		if($skip_first){
			$skip_first = false;
			continue;
		}
		$cells = pq('td', $row);

		$column_number = 0;
		$row_data = array();
		foreach($cells as $cell) {
			$data = pq($cell)->text();
			$data = process_data($data, $column_number, $row_data);
			$column_number++;
		}
		fputcsv($fp, $row_data);
	}
	echo "Page: {$page}\r\n";
}

fclose ($fp);

$end_time = microtime(true);
$elapsed_time_micro_seconds = ($end_time - $start_time);
echo $elapsed_time_micro_seconds;
echo 'seconds'; die;

/**
* Returns the HTML from games.crossfit.com/leaderboard
* @param int $page Page number of the leader board that is retrieved.
* @return string HTML data
*/
function get_data($page) {
	global $GENDER;
	$gender = strtolower($GENDER);
	if(strcmp('male', $gender) === 0){
		$division = 1;
	}
	else{
		$division = 2;
	}

	$url = "http://games.crossfit.com/scores/leaderboard.php?stage=5&sort=0&page={$page}&division={$division}&region=0&numberperpage=60&competition=0&frontpage=0&expanded=0&year=14&full=1&showtoggles=0&hidedropdowns=0&showathleteac=1&=&is_mobile=0";

	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

/**
* Process each cell of data for a row on the leader board table.
* @param string $data Data in HTML
* @param int $column_number The corresponding column number for the cell that is being processed.
* @param array &$result The resulting array that stores the processed data. Passed by reference so that you dont have to array merge the results later
* @return void
*/
function process_data($data, $column_number, &$result){
	switch($column_number){
		case 1:
			$result[] = $data;
			break;
		case 6:
			preg_match('/([0-9]+) \\(([0-9\\(\\):]+)\\)(.)*/', $data, $matches);
			if(empty($matches)){
				break;
			}
			$result[] = $matches[1];
			$result[] = $matches[2];
			$result[] = hours_to_seconds($matches[2]);
			break;
		default:
			preg_match('/([0-9]+) \\(([0-9\\(\\):]+)\\)(.)*/', $data, $matches);
			if(empty($matches)){
				break;
			}
			$result[] = $matches[1];
			$result[] = $matches[2]; 
			break;
	}
}

/**
* Converts the time HH:mm.:ss to total seconds
* @param string $time Time in the format HH:mm:ss
* @return int seconds
*/
function hours_to_seconds ($time) { 
	/**
	@TODO: convert to REGEX
	**/
	if(empty($time)){
		return 0;
	}
	$time = explode(":", $time);
	$hours = count($time) === 3? $time[0] * 3600 : 0;
	$seconds = 0;
	$minutes = 0;

	if(count($time) === 2){
		$minutes = $time[0] * 60;
		$seconds = $time[1];
	}
	else if(count($time) === 3){
		$minutes = $time[1] * 60;
		$seconds = (int)$time[2];
	}

	return $hours + $minutes + $seconds;
}