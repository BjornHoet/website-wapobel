<?php
include '../init.php';
global $mysqli;

$error = true;
$colVal = '';
$colIndex = $rowId = 0;

$msg = array('status' => !$error, 'msg' => 'Failed! updation in mysql');

if(isset($_POST)){
    if(isset($_POST['val']) && !empty($_POST['val']) && $error) {
		$colVal = $_POST['val'];
		$error = false;
    } else {
		$colVal = 0;
		$error = false;
    }
    if(isset($_POST['index']) && $_POST['index'] >= 0 &&  $error) {
		$colIndex = $_POST['index'];
		$error = false;
    } else {
		$error = true;
    }
    if(isset($_POST['id']) && $_POST['id'] > 0 && $error) {
		$rowId = $_POST['id'];
		$error = false;
    } else {
		$error = true;
    }
  
    if(!$error) {
		echo $_POST['index'];
		if($_POST['index'] === '0' || $_POST['index'] === '0b') {
			if($_POST['index'] === '0') {
				$sql = "UPDATE boekingen SET omschrijving = '".$colVal."' WHERE boekId='".$_POST['id']."'";
				$result = $mysqli->query($sql);
				}
			if($_POST['index'] === '0b') {
				$sql = "UPDATE boekingen SET nummering = '".$colVal."' WHERE boekId='".$_POST['id']."'";
				$result = $mysqli->query($sql);
				}			
			} else {
			$sql = "UPDATE boekingsbedragen SET bedrag = '".$colVal."' WHERE boekingId='".$_POST['index']."'";
			$result = $mysqli->query($sql);
			}
		$msg = array('status' => !$error, 'msg' => $colIndex);
    }
}

echo json_encode($msg);
exit();
?>