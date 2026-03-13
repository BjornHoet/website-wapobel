<?php
include '../init.php';

/* =========================
   VALIDATIE
========================= */
if (empty($_POST['contactMessage']) || empty($_POST['contactType'])) {
    echo "No arguments Provided!";
    return false;
}

/* =========================
   DATA
========================= */
$contactTypeRaw = trim($_POST['contactType']);
$messageRaw     = trim($_POST['contactMessage']);

$contactType = htmlspecialchars($contactTypeRaw);
$message     = htmlspecialchars($messageRaw);

/* Menselijke benaming */
$typeLabel = ($contactType === 'suggestie')
    ? 'Suggestie om het programma te verbeteren'
    : 'Vraag of probleem met het programma';

/* =========================
   MAIL HTML
========================= */
$output  = '<div style="background:#f2f2f2; padding:30px 0; font-family:Arial, sans-serif;">';
$output .= '<table width="100%" cellspacing="0" cellpadding="0" style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:6px; overflow:hidden;">';

/* HEADER */
$output .= '
<tr>
    <td>
        <img src="https://wapobel.be/img/email-banner.jpg" alt="Wapobel Banner" width="600" style="width:100%; display:block;">
    </td>
</tr>';

/* CONTENT */
$output .= '<tr><td style="padding:30px; color:#333; font-size:16px; line-height:1.6;">';

$output .= '<p style="margin-top:0;">Hoi Bjorn,</p>';
$output .= '<p>Er werd een nieuwe melding ingediend via <strong>Wapobel</strong>. Hieronder vind je de details:</p>';

/* INFO TABEL */
$output .= '<table width="100%" cellspacing="0" cellpadding="6" style="border-collapse:collapse; margin-top:20px;">';

$rows = [
    "Type melding" => $typeLabel,
    "Gebruiker"   => $userName,
    "Naam"        => $firstName . " " . $lastName
];

foreach ($rows as $label => $value) {
    $output .= '
    <tr>
        <td style="width:40%; font-weight:bold; background:#f7f7f7; border-bottom:1px solid #e6e6e6;">' . $label . '</td>
        <td style="background:#ffffff; border-bottom:1px solid #e6e6e6;">' . $value . '</td>
    </tr>';
}

$output .= '</table>';

/* BERICHT */
$output .= '
<div style="margin-top:25px; padding:15px; background:#f9f9f9; border-radius:6px; border:1px solid #e6e6e6;">
    <p style="margin:0;"><strong>Bericht van gebruiker:</strong></p>
    <p style="white-space:pre-line; margin-top:10px;">' . nl2br($message) . '</p>
</div>';

$output .= '<p style="margin-top:25px;">Met vriendelijke groeten,<br><strong>Het Wapobel Team</strong></p>';
$output .= '</td></tr>';

/* FOOTER */
$output .= '
<tr>
    <td style="text-align:center; padding:20px; background:#fafafa;">
        <img src="https://wapobel.be/img/logo-horizontal-small.png" height="45" alt="Wapobel" style="opacity:0.8;">
    </td>
</tr>';

$output .= '</table></div>';

/* =========================
   MAIL VERZENDEN
========================= */
$to = "info@wapobel.be";
$subject = "Wapobel – " . $typeLabel;

if ($localhost != 'X') {
    sendMailPHPMailer($to, 'Bjorn Hoeterickx', $subject, $output, false);
} else {
    sendMailPHP($to, 'Bjorn Hoeterickx', $subject, $output, false);
}

return true;