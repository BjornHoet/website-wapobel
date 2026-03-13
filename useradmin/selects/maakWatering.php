<?php
include '../../bin/init.php'; 
global $mysqliLogin;   // SOURCE DB

header('Content-Type: application/json');

// -----------------------------
// 1. Validate input
// -----------------------------
if (!isset($_GET['userId'])) {
    echo json_encode(["success" => false, "message" => "Missing userId"]);
    exit;
}

if (isset($_GET['jaar'])) {
    $jaar = intval($_GET['jaar']);
} else {
    $jaar = intval(date("Y")) + 1;
}

$userId = intval($_GET['userId']);

// Load full user data
$newWateringData = getUserData($userId);

if (!$newWateringData) {
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit;
}

$hoofdId = intval($newWateringData['hoofdWateringId']);
$_SESSION['selectedWateringId'] = $hoofdId;
$dbSuffix = $newWateringData['wapobelDatabase'];

// -----------------------------
// 2. Connect to target "wapobel_beX" DB
// -----------------------------
$database = 'wapobel_be' . $dbSuffix;
require "../../bin/database/connect.php"; 

global $mysqli; // TARGET DB

if (!$mysqli) {
    echo json_encode(["success" => false, "message" => "Cannot connect to target database"]);
    exit;
}

// -----------------------------
// 3. FETCH records FROM SOURCE ($mysqliLogin)
// -----------------------------
$sqlSource = "SELECT * FROM rekeningen WHERE wateringId = ?";
$stmt = $mysqliLogin->prepare($sqlSource);   // correct source DB
$stmt->bind_param("i", $hoofdId);
$stmt->execute();
$result = $stmt->get_result();

$copied = 0;

if ($result->num_rows === 0) {
	// SQL om een nieuwe record toe te voegen
	$sql = "INSERT INTO rekeningen 
			(wateringId, jaar, rekening, omschrijving, positie, overdracht, actief, afgesloten) 
			VALUES 
			(?, ?, 'KAS', '', 0, 0.00, 'X', '')";

	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("ii", $hoofdId, $jaar);

	if ($stmt->execute()) {
		$copied = 1;
	}
}

// -----------------------------
// 4. INSERT INTO TARGET ($mysqli)
// -----------------------------
while ($row = $result->fetch_assoc()) {
    // Verwijder AUTO_INCREMENT PK zodat MySQL zelf een nieuwe ID genereert
    unset($row['rekeningId']);  

    $columns = array_keys($row);
    $values  = array_values($row);

    $colStr = "`" . implode("`, `", $columns) . "`";
    $placeholders = implode(",", array_fill(0, count($values), "?"));

    $sqlInsert = "INSERT INTO rekeningen ($colStr) VALUES ($placeholders)";
    $insertStmt = $mysqli->prepare($sqlInsert);

    // Dynamische bind types
    $types = "";
    foreach ($values as $v) {
        if (is_int($v)) {
            $types .= "i";
        } elseif (is_numeric($v) && strpos($v, ".") !== false) {
            $types .= "d"; // decimals als double
        } else {
            $types .= "s";
        }
    }

    $insertStmt->bind_param($types, ...$values);

    if ($insertStmt->execute()) {
        $copied++;
    }
}

// ---------------------------------------------------------
// 5. COPY FROM standaardposten (SOURCE) → posten (TARGET)
// ---------------------------------------------------------

// First: fetch jaar from the last rekeningen row
// If multiple jaren exist, we take the first one copied.
// If this is not what you want, tell me.

// Fetch standaardposten from source DB
$sqlStandaard = "SELECT hoofdpostId, referentie, omschrijving FROM standaardposten WHERE jaar = ?";
$stmtStd = $mysqliLogin->prepare($sqlStandaard);
$stmtStd->bind_param("i", $jaar);
$stmtStd->execute();
$resStd = $stmtStd->get_result();

$postenCopied = 0;

while ($std = $resStd->fetch_assoc()) {
	$wateringId = $newWateringData['hoofdWateringId'];
	$hoofdpostId = $std['hoofdpostId'];
	$referentie  = $std['referentie'];
	$omschrijving = $std['omschrijving'];

	$raming = 0.00;         // DECIMAL
	$actief = "X";
	$overdrachtPost = "";

	$sqlInsertPost = "
		INSERT INTO posten (
			wateringId, jaar, hoofdpostId, referentie, omschrijving,
			raming, actief, overdrachtPost
		) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
	";

	$insertPost = $mysqli->prepare($sqlInsertPost);

	$insertPost->bind_param(
		"iiissdss",              // FIXED: raming = d (decimal)
		$wateringId,
		$jaar,
		$hoofdpostId,
		$referentie,
		$omschrijving,
		$raming,
		$actief,
		$overdrachtPost
	);

	if ($insertPost->execute()) {
		$postenCopied++;
	}
}

// ---------------------------------------------------------
// 6a. INSERT INTO boekjaren (TARGET DB)
// ---------------------------------------------------------
openNieuwJaar($hoofdId, $jaar);
$boekjarenInserted = 1;

// ---------------------------------------------------------
// 6b. INSERT INTO reserve (TARGET DB)
// ---------------------------------------------------------
openReserve($hoofdId, $jaar, 0.00);
$reserveInserted = 1;

// ---------------------------------------------------------
// 7. UPDATE SOURCE DB: set users.active = 1
// ---------------------------------------------------------
$sqlUpdateUser = "UPDATE users SET active = 1, showNews = 1 WHERE userId = ?";
$stmtUpdate = $mysqliLogin->prepare($sqlUpdateUser);
$stmtUpdate->bind_param("i", $userId);

$updated = 0;
if ($stmtUpdate->execute()) {
    $updated = $stmtUpdate->affected_rows;  // should be 1
}

$expFormat = mktime(date("H"), date("i"), date("s"), date("m") ,date("d")+1, date("Y"));
$expDate = date("Y-m-d H:i:s",$expFormat);
$bytes = random_bytes(21); // 21 bytes × 2 hex chars = 42 characters
$key = bin2hex($bytes);
passwordReset($newWateringData['email'], $key, $expDate);

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
$output .= '<p style="margin-top:0;">Beste ' . $newWateringData['firstName'] . ' ' . $newWateringData['lastName'] . ',</p>';
$output .= '<p>Bedankt om je te registreren bij <strong>Wapobel</strong>. Je aanvraag werd goedgekeurd en je account is nu actief.</p>';
$output .= '<p>Je kan je wachtwoord instellen via onderstaande knop:</p>';
$resetLink = 'https://wapobel.be/bin/login/set-new-password.php?key=' . $key . '&email=' . urlencode($newWateringData['email']) . '&action=reset';

//
// BUTTON
//
$output .= '<p style="text-align:center; margin:30px 0;">';
$output .= '<a href="' . $resetLink . '" target="_blank" style=" background:#007bff; color:#ffffff !important; padding:12px 28px; font-size:16px; text-decoration:none; border-radius:4px; font-weight:bold; display:inline-block;">Wachtwoord instellen</a>';
$output .= '</p>';

// LINK
$output .= '<p>De link blijft <strong>24 uur</strong> geldig. Als de knop niet werkt, kopieer dan deze URL in je browser:</p>';
$output .= '<p style="word-break:break-all; color:#007bff;">' . $resetLink . '</p>';

// EXTRA INFORMATIEBLOK
$output .= '<div style="background:#f9f9f9; padding:20px; border-radius:6px; border:1px solid #e5e5e5; margin-bottom:25px; font-size:15px; line-height:1.6;">';
$output .= '<p style="margin-top:0;"><strong>Belangrijk:</strong></p>';
$output .= '<ul style="padding-left:20px; margin:0;">';
$output .= '<li>Indien je nog geen rekeningen had opgegeven bij de registratie, kan je deze toevoegen in het menu <strong>Rekeningen</strong>. Heb je al wel rekeningen opgegeven, dan kan je de startbedragen ingeven. Dit is het bedrag op <strong>1 januari 2026</strong>.</li>';
$output .= '<li>Standaard is er een <strong>KAS</strong> aangemaakt. Indien je niet werkt met een kas, kan je dit deactiveren in je <strong>Profiel</strong>.</li>';
$output .= '<li>Je open boekjaar is <strong>2026</strong>. Bekijk zeker volgende zaken:
    <ul style="padding-left:18px; margin-top:8px;">
        <li>Je kan door de posten gaan en posten die je niet gebruikt deactiveren.</li>
        <li>Je kan subposten aanmaken.</li>
        <li>Je kan je begrotingscijfers ingeven per post.</li>
        <li>Onder de post <strong>Andere</strong> kan je naast subposten, ook eigen posten toevoegen.</li>
        <li>Je kan starten met boekingen te doen.</li>
    </ul>
</li>';
$output .= '<li>Ben je al geregistreerd op <strong>Billit</strong>? Dan kan je de Billit-integratie activeren in je Profiel. Hiervoor heb je je <strong>API Key</strong> van Billit nodig. Meer informatie vind je in de documentatie binnen Wapobel, via het linkermenu onder <strong>Documenten</strong>.</li>';
$output .= '</ul>';
$output .= '</div>';

$output .= '<p>Heb je vragen? Contacteer ons via <a href="mailto:info@wapobel.be" style="color:#007bff;">info@wapobel.be</a>.</p>';
$output .= '<p>Met vriendelijke groeten,<br><strong>Het Wapobel Team</strong></p>';
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

$body = $output; 
$subject = "Welkom bij Wapobel";
$to = $newWateringData['email'];

if($localhost != 'X')
    $result = sendMailPHPMailer($to, $newWateringData['firstName'] . ' ' . $newWateringData['lastName'], $subject, $output, true);
else {
	$to = 'bjorn.hoeterickx@fimar.be';
    $result = sendMailPHP($to, $newWateringData['firstName'] . ' ' . $newWateringData['lastName'], $subject, $output);
	}

echo json_encode([
    "success" => true,
    "message" => "Records copied successfully",
    "rekeningenCopied" => $copied,
    "postenCopied" => $postenCopied,
    "boekjarenInserted" => $boekjarenInserted,
    "reserveInserted" => $reserveInserted,
    "updatedUser" => $updated
]);

$json = writeAdminUsers();
exit;
