<?php
include '../bin/init.php';

global $mysqliLogin;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$resultRekening = 1;
	$resultUser = 1;
	$resultWatering = 1;
	
    // Sanitize and collect main form inputs
    $email = $mysqliLogin->real_escape_string($_POST['userEmail']);
    $firstName = $mysqliLogin->real_escape_string($_POST['userFirstName']);
    $lastName = $mysqliLogin->real_escape_string($_POST['userLastName']);
    $wateringPolder = $mysqliLogin->real_escape_string($_POST['userNameWateringPolder']);
    $street = $mysqliLogin->real_escape_string($_POST['userStreet']);
    $houseNumber = $mysqliLogin->real_escape_string($_POST['userHouseNumber']);
    $postalCode = $mysqliLogin->real_escape_string($_POST['userPostalCode']);
    $city = $mysqliLogin->real_escape_string($_POST['userCity']);
    $wateringJaar = $mysqliLogin->real_escape_string($_POST['wateringJaar']);

	// Check if email already exists
	$stmt = $mysqliLogin->prepare("SELECT userId FROM users WHERE email = ?");
	$stmt->bind_param("s", $email);
	$stmt->execute();
	$stmt->store_result();

	if ($stmt->num_rows > 0) {
		// Email bestaat al → terugsturen met foutmelding
		$query = http_build_query($_POST);
		header("Location: index.php?error=email&$query");
		exit();
	}

	$stmt->close();

	$lastWateringId = getLastWateringID();
	$lastWateringId = $lastWateringId + 1;

    // Insert into users table
	$resultWatering = addWatering($lastWateringId, $wateringPolder);
	$resultUser = addUser($email, $firstName, $lastName, $street, $houseNumber, $postalCode, $city, $lastWateringId);

	// Handle accounts input if provided
	if (!empty($_POST['userAccountName']) && !empty($_POST['userAccount'])) {
		addRekeningGlobal($lastWateringId, $wateringJaar, 'KAS', '', '', '0');
		
		$accountNames = $_POST['userAccountName'];
		$accountNumbers = $_POST['userAccount'];

		// Loop through each account and save it
		for ($i = 0; $i < count($accountNames); $i++) {
			$positie = $i + 1;
			$accountName = $mysqliLogin->real_escape_string($accountNames[$i]);
			$accountNumber = $mysqliLogin->real_escape_string($accountNumbers[$i]);

			if (!empty($accountName) && !empty($accountNumber)) {
				$resultRekening = addRekeningGlobal($lastWateringId, $wateringJaar, $accountNumber, $accountName, 0, $positie);
			}
		}
	}

$output  = '<div style="background:#f2f2f2; padding:30px 0; font-family:Arial, sans-serif;">';
$output .= '<table width="100%" cellspacing="0" cellpadding="0" style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:6px; overflow:hidden;">';

//
// HEADER BANNER
//
$output .= '<tr>';
$output .= '<td style="padding:0; margin:0;">';
$output .= '<img src="https://wapobel.be/img/email-banner.jpg" alt="Wapobel Banner" width="600" style="width:100%; display:block; border:none;">';
$output .= '</td>';
$output .= '</tr>';

//
// CONTENT
//
$output .= '<tr><td style="padding:30px; color:#333333; font-size:16px; line-height:1.6;">';
$output .= '<p style="margin-top:0;">Hoi Bjorn,</p>';
$output .= '<p>Er werd een nieuwe registratie uitgevoerd bij <strong>Wapobel</strong>. Hieronder vind je een overzicht van de opgegeven gegevens:</p>';

//
// DATA TABLE
//
$output .= '<table width="100%" cellspacing="0" cellpadding="6" style="border-collapse:collapse; margin-top:20px;">';

$rows = [
    "Naam" => $firstName . " " . $lastName,
    "E-mailadres" => $email,
    "Watering/Polder" => $wateringPolder,
    "Adres" => $street . " " . $houseNumber,
    "Postcode" => $postalCode,
    "Gemeente" => $city,
    "Boekjaar" => $wateringJaar
];

foreach ($rows as $label => $value) {
    $output .= '
        <tr>
            <td style="width:40%; font-weight:bold; background:#f7f7f7; border-bottom:1px solid #e6e6e6;">' . $label . '</td>
            <td style="background:#ffffff; border-bottom:1px solid #e6e6e6;">' . $value . '</td>
        </tr>';
}

$output .= '</table>';

$output .= '<p style="margin-top:25px;">Met vriendelijke groeten,<br><strong>Het Wapobel Team</strong></p>';
$output .= '</td></tr>';

//
// FOOTER LOGO
//
$output .= '<tr>';
$output .= '<td style="text-align:center; padding:20px; background:#fafafa;">';
$output .= '<img src="https://wapobel.be/img/logo-horizontal-small.png" height="45" alt="Wapobel" style="opacity:0.8;">';
$output .= '</td>';
$output .= '</tr>';

$output .= '</table>';
$output .= '</div>';

$subject = "Nieuwe registratie bij Wapobel";
$to = 'info@wapobel.be';

if($localhost != 'X')
    $result = sendMailPHPMailer($to, 'Bjorn Hoeterickx', $subject, $output, false);
else
    $result = sendMailPHP($to, 'Bjorn Hoeterickx', $subject, $output, false);

}

if($resultWatering != 1 || $resultUser != 1 || $resultRekening != '1')
	header("Location: fout.php");
else
	header("Location: succes.php");
exit();
?>