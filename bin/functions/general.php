<?php
function protectPage() {
	if (loggedIn() === false) {
		header('Location: login.php');
		exit();
		}
	}

function sanitize($data) {
	global $mysqliLogin;
	return $mysqliLogin->real_escape_string($data);
}

function databaseBackup() {
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	global $localhost;
	
	if($localhost !== 'X') {
/* 		// Array of databases and credentials
		$databases = [
			[
				'host' => 'wapobel.be.mysql',
				'user' => 'wapobel_belogin',
				'pass' => 'wapobelLogin123!',
				'db'   => 'wapobel_belogin'
			],
			[
				'host' => 'wapobel.be.mysql',
				'user' => 'wapobel_bewatering1',
				'pass' => 'watering1Wapobel123!',
				'db'   => 'wapobel_bewatering1'
			],
			[
				'host' => 'wapobel.be.mysql',
				'user' => 'wapobel_bewatering2',
				'pass' => 'watering2Wapobel123!',
				'db'   => 'wapobel_bewatering2'
			],
			[
				'host' => 'wapobel.be.mysql',
				'user' => 'wapobel_bewatering3',
				'pass' => 'watering3Wapobel123!',
				'db'   => 'wapobel_bewatering3'
			],
			[
				'host' => 'wapobel.be.mysql',
				'user' => 'wapobel_bewatering4',
				'pass' => 'watering4Wapobel123!',
				'db'   => 'wapobel_bewatering4'
			],
			[
				'host' => 'wapobel.be.mysql',
				'user' => 'wapobel_bewatering5',
				'pass' => 'watering5Wapobel123!',
				'db'   => 'wapobel_bewatering5'
			]	
			// add the rest...
		]; */

		$backupFolder = "../../DBBackup/";
		if (!is_dir($backupFolder)) mkdir($backupFolder, 0755, true);

		foreach ($databases as $config) {
			$mysqli = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);
			if ($mysqli->connect_error) {
				echo "Connection failed for {$config['db']}: " . $mysqli->connect_error . "<br>";
				continue;
			}

			$sqlDump = "-- Backup of {$config['db']} on " . date("Y-m-d H:i:s") . "\n\n";
			$tables = $mysqli->query("SHOW TABLES");

			while ($t = $tables->fetch_array()) {
				$table = $t[0];

				// Table structure
				$create = $mysqli->query("SHOW CREATE TABLE `$table`")->fetch_assoc();
				$sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";
				$sqlDump .= $create["Create Table"] . ";\n\n";

				// Table content
				$rows = $mysqli->query("SELECT * FROM `$table`");
				if ($rows->num_rows > 0) {
					$sqlDump .= "INSERT INTO `$table` VALUES\n";
					$valueLines = [];
					while ($row = $rows->fetch_assoc()) {
						$escaped = array_map(function($v) use ($mysqli) {
							return isset($v) ? "'" . $mysqli->real_escape_string($v) . "'" : "NULL";
						}, $row);
						$valueLines[] = "(" . implode(",", $escaped) . ")";
					}
					$sqlDump .= implode(",\n", $valueLines) . ";\n\n";
				}
			}

			$file = $backupFolder . $config['db'] . "_backup_" . date("Ymd_His") . ".sql";
			file_put_contents($file, $sqlDump);
			$mysqli->close();
		}
	}
}

function asEuro($value) {
  return number_format($value, 2, "," , ".") . ' EUR';
}

//function formatDate($date) {
//	return = date("d/m/Y", strtotime($date));
//}

function outputErrors($errors) {
	$output = array();
	foreach($errors as $error) {
		$output[] = '<li>' . $error . '</li>';
	}
	$outputErrors = '<ul class="list-unstyled" style="margin-left:20px; margin-top:10px;">' . implode('', $output) . '</ul>';
	$_SESSION['outputErrors'] = $outputErrors;
}

function array_msort($array, $cols) {
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
		}
		
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
		}
		
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
			}
		}
		
    return $ret;
	}
	
function deleteDir($path) {
    if (empty($path)) { 
        return false;
		}
    return is_file($path) ?
            @unlink($path) :
            array_map(__FUNCTION__, glob($path.'/*')) == @rmdir($path);
	}

function sendMailPHPMailer($to, $toName, $subject, $body, $bcc = true) {
    $mailSmtp = 'send.one.com';
    $mailUser = 'info@wapobel.be';
    $mailPass = 'wapobelMarieFien01!';
    $mailFrom = 'info@wapobel.be';
    $mailFromName = 'Info Wapobel';    
    
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/PHPMailer-master/src/Exception.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/PHPMailer-master/src/PHPMailer.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/PHPMailer-master/src/SMTP.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // SMTP setup
        $mail->isSMTP();
        $mail->Host       = $mailSmtp;
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailUser;
        $mail->Password   = $mailPass;
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Email addresses
        $mail->setFrom($mailFrom, $mailFromName);
        $mail->addAddress($to, $toName);

        // Optional BCC
        if ($bcc === true) {
            $mail->addBCC('bjorn.hoeterickx@fimar.be');
        }

        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body    = $body;

        $mail->send();
        return true;
        
    } catch (Exception $e) {
        echo $e;
        return false;
    }
}

function sendMailPHP ($to, $toName, $subject, $body, $bcc = true) {
	$mailFrom = 'info@wapobel.be';
	$mailFromName = 'Wapobel';
	
	$headers = "From: $mailFromName <$mailFrom>\r\n";
	$headers .= "Reply-To: " . $to . "\r\n";	
	$headers .= "Organization: $mailFromName\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$headers .= "X-Priority: 3\r\n";
	$headers .= "X-Mailer: PHP". phpversion() ."\r\n";
	if ($bcc === true) {
		$headers .= "Bcc: bjorn.hoeterickx@fimar.be\r\n";
	}
	
	if (mail($to, $subject, $body, $headers)) {
		return true;
	} else {
		return false;
		}	
	}	
?>