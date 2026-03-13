<?php
function getNews() {
	global $mysqli;
	$sql = "SELECT * FROM news WHERE active = '1' ORDER BY volgnr DESC";
	$result = $mysqli->query($sql);
	
	return $result;
	}

function getYearsShows() {
	global $mysqli;
	$sql = "SELECT DISTINCT jaar FROM shows WHERE hide = '0' ORDER BY jaar DESC";
	$result = $mysqli->query($sql);
	
	return $result;
	}

function getShows($year) {
	$start = $year . '0101';
	$end = $year . '1231';
	
	global $mysqli;
	$sql = "SELECT * FROM shows WHERE datum >= '$start' AND datum <= '$end' AND hide = '0' ORDER BY datum DESC";
	$result = $mysqli->query($sql);
	
	return $result;
	}
	
function getPastShows() {
	$start = '00010101';
	$end = date('Ymd');
	
	global $mysqli;
	$sql = "SELECT * FROM shows WHERE datum >= '$start' AND datum < '$end' AND hide = '0' ORDER BY datum DESC";
	$result = $mysqli->query($sql);
	
	return $result;
	}
	
function getUpcomingShows() {
	$start = date('Ymd');
	$end =  '99991231';
	
	global $mysqli;
	$sql = "SELECT * FROM shows WHERE datum >= '$start' AND datum <= '$end' AND hide = '0' ORDER BY datum ASC";
	$result = $mysqli->query($sql);
	
	return $result;
	}	

function getVideoHeaders() {
	global $mysqli;
	$sql = "SELECT * FROM video_headers ORDER BY volgnr DESC";
	$result = $mysqli->query($sql);
	
	return $result;
	}
	
function getVideos($header) {
	global $mysqli;
	$sql = "SELECT * FROM videos WHERE header = '$header' AND active = '1' ORDER BY volgnr DESC";
	$result = $mysqli->query($sql);
	
	return $result;
	}

function getAllDirs(){
	$path = 'photos';
	$result = array();
	
	$dir = new DirectoryIterator($path);
	foreach ($dir as $fileinfo) {
		if ($fileinfo->isDir() && !$fileinfo->isDot()) {
			$directory = $fileinfo->getFilename();
			array_push($result, $directory);
		}
	}

	sort($result);	
	
	return $result;
}
	
function getPhotos($dir) {
	$path = '../../photos/' . $dir;
	
	$dh  = opendir($path);
	while (false !== ($filename = readdir($dh))) {
		$files[] = $filename;
	}
	$images = preg_grep ('/\.jpg$/i', $files);
	
	sort($images);
	
	return $images;
}
?>