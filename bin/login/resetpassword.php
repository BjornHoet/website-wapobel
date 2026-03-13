<?php
include '../init.php';

if (empty($_POST) === false) {
	$userEmail = $_POST['userEmail'];

	if (empty($userEmail) === true) {
			$errors[] = 'Gelieve een gebruiker en wachtwoord in te geven';
			} else 
			if (user_exists_mail($userEmail) === false) {
				$errors[] = 'Deze gebruiker bestaat niet';
			} else 
			if (user_active_mail($userEmail) === false) {
				$errors[] = 'Deze gebruiker is niet actief';
			} else {
				$errors[] = 'Een tijdelijk wachtwoord is verzonden naar je e-mail adres. Volg de procedure beschreven in de e-mail.';
				outputErrors($errors);
				$expFormat = mktime(date("H")+2, date("i")+15, date("s"), date("m") ,date("d"), date("Y"));
				$expDate = date("Y-m-d H:i:s",$expFormat);
				$bytes = random_bytes(21); // 21 bytes × 2 hex chars = 42 characters
				$key = bin2hex($bytes);
					
				passwordReset($userEmail, $key, $expDate);

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
				$output .= '<p style="margin-top:0;">Beste gebruiker,</p>';
				$output .= '<p>Je hebt een aanvraag gedaan om een nieuw wachtwoord in te stellen.<br>';
				$output .= 'Je kan je wachtwoord opnieuw instellen via onderstaande knop:</p>';
				$resetLink = 'https://wapobel.be/bin/login/reset-password.php?key=' . $key . '&email=' . urlencode($userEmail) . '&action=reset';

				//
				// BUTTON
				//
				$output .= '<p style="text-align:center; margin:30px 0;">';
				$output .= '<a href="' . $resetLink . '" target="_blank" style=" background:#007bff; color:#ffffff !important; padding:12px 28px; font-size:16px; text-decoration:none; border-radius:4px; font-weight:bold; display:inline-block;">Wachtwoord instellen</a>';
				$output .= '</p>';

				$output .= '<p>De link blijft <strong>15 minuten</strong> geldig. Als de knop niet werkt, kopieer dan deze URL in je browser:</p>';
				$output .= '<p style="word-break:break-all; color:#007bff;">' . $resetLink . '</p>';
				$output .= '<p>Heb je geen wachtwoord reset aangevraagd, dan is er verder geen actie nodig en kan je deze e-mail negeren.</p>';
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
				$subject = "Wapobel - Wachtwoord reset";
				$to = $userEmail;
				
				if($localhost != 'X')
					$result = sendMailPHPMailer($to, '', $subject, $output, true);
				else {
					$to = 'bjorn.hoeterickx@fimar.be';
					$result = sendMailPHP($to, '', $subject, $output);
					}

/*				$fromserver = "info@wapobel.be"; 

				$headers = "From: Info Wapobel <info@wapobel.be>\r\n"; // This is the email address the generated message will be from. We recommend using something like noreply@yourdomain.com.
				$headers .= "Reply-To: $userEmail\r\n";	
				$headers .= "Organization: FiMar\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8\r\n";
				$headers .= "X-Priority: 3\r\n";
				$headers .= "X-Mailer: PHP". phpversion() ."\r\n";
				
				mail($to,$subject,$body,$headers); */

				header('Location: index.php');
				exit();
				}

		outputErrors($errors);				
		header('Location: forgot-password.php');
		exit();	
		}
	else {
		outputErrors($errors);
		header('Location: forgot-password.php');
		exit();		
	}
?>