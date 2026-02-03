<?php
// -------- CONFIG --------
$TO = "contact@mbcarsdzrouiba.com";
$SITE = "MB CARS DZ";
$LOGO = "https://mbcarsdzrouiba.com/assets/logo.png"; // adapte si besoin

// -------- UTIL --------
function val($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : ""; }
function esc($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$name   = esc(val('name'));
$phone  = esc(val('phone'));
$email  = esc(val('email'));
$date   = esc(val('date'));
$time   = esc(val('time'));
$flight = esc(val('flight'));
$car    = esc(val('car'));
$place  = esc(val('place'));
$msg    = nl2br(esc(val('msg')));
$source = esc(val('source'));           // ex: “modal-carte” ou “form-aeroport”

// Sujet encodé UTF-8
$subject = "🚗 Nouvelle demande — $SITE";
$subject_mime = "=?UTF-8?B?".base64_encode($subject)."?=";

// -------- Corps texte (fallback) --------
$text = "Nouvelle demande — $SITE\n\n"
      . "Nom: $name\n"
      . "Téléphone: $phone\n"
      . "Email: $email\n"
      . "Date d'arrivée: $date\n"
      . "Heure: $time\n"
      . "Vol/Terminal: $flight\n"
      . "Modèle souhaité: $car\n"
      . "Lieu de remise: $place\n"
      . "Message: ".strip_tags($msg)."\n"
      . "Source: $source\n";

// -------- Corps HTML --------
$html = "
<!doctype html>
<html lang='fr'>
<head>
<meta charset='utf-8'>
<meta name='x-apple-disable-message-reformatting'>
<title>Nouvelle demande</title>
</head>
<body style='margin:0;background:#0f1115;color:#e8ecf2;font-family:Inter,Arial,sans-serif'>
  <table role='presentation' width='100%' cellspacing='0' cellpadding='0' style='background:#0f1115;padding:24px 0'>
    <tr>
      <td align='center'>
        <table role='presentation' width='600' cellspacing='0' cellpadding='0' style='width:100%;max-width:600px;background:#11151b;border:1px solid #1b2130;border-radius:14px;overflow:hidden'>
          <tr>
            <td style='background:#0b0e14;padding:18px 20px;border-bottom:3px solid #D89C15'>
              <table width='100%'>
                <tr>
                  <td align='left'>
                    <img src='$LOGO' alt='$SITE' style='height:40px'>
                  </td>
                  <td align='right' style='color:#FFD77A;font-weight:800'>Nouvelle demande</td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td style='padding:20px'>
              <h2 style='margin:0 0 14px;font-size:20px;color:#fff'>Détails client</h2>
              <table width='100%' cellspacing='0' cellpadding='0' style='border-collapse:collapse'>
                <tr><td style='padding:8px 0;color:#cfd6e3'>Nom</td><td style='padding:8px 0;color:#fff;font-weight:700'>$name</td></tr>
                <tr><td style='padding:8px 0;color:#cfd6e3'>Téléphone</td><td style='padding:8px 0;color:#fff;font-weight:700'>$phone</td></tr>
                <tr><td style='padding:8px 0;color:#cfd6e3'>Email</td><td style='padding:8px 0;color:#fff;font-weight:700'>$email</td></tr>
              </table>

              <h2 style='margin:22px 0 14px;font-size:20px;color:#fff'>Trajet & véhicule</h2>
              <table width='100%' cellspacing='0' cellpadding='0'>
                <tr><td style='padding:8px 0;color:#cfd6e3'>Arrivée</td><td style='padding:8px 0;color:#fff;font-weight:700'>$date à $time</td></tr>
                <tr><td style='padding:8px 0;color:#cfd6e3'>Vol / Terminal</td><td style='padding:8px 0;color:#fff;font-weight:700'>$flight</td></tr>
                <tr><td style='padding:8px 0;color:#cfd6e3'>Modèle souhaité</td><td style='padding:8px 0;color:#fff;font-weight:700'>$car</td></tr>
                <tr><td style='padding:8px 0;color:#cfd6e3'>Lieu de remise</td><td style='padding:8px 0;color:#fff;font-weight:700'>$place</td></tr>
              </table>

              <h2 style='margin:22px 0 10px;font-size:20px;color:#fff'>Message</h2>
              <div style='padding:12px;border:1px solid #232837;border-radius:10px;background:#0b0e14;color:#e8ecf2'>
                ".($msg ?: "<span style='opacity:.7'>—</span>")."
              </div>

              <div style='margin-top:18px;color:#9fb1c7;font-size:13px'>Source: $source</div>
            </td>
          </tr>

          <tr>
            <td style='background:linear-gradient(135deg,#FFE08A,#D89C15);height:6px'></td>
          </tr>
        </table>

        <div style='color:#94a3b8;font-size:12px;margin-top:12px'>
          $SITE • Notification automatique
        </div>
      </td>
    </tr>
  </table>
</body>
</html>
";

// -------- Envoi multipart (HTML + texte) --------
$boundary = "==MB-".md5(uniqid(time(), true));
$headers  = "From: $SITE <noreply@".$_SERVER['SERVER_NAME'].">\r\n";
$headers .= "Reply-To: ".($email ?: "noreply@".$_SERVER['SERVER_NAME'])."\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"; charset=UTF-8\r\n";
$body  = "--$boundary\r\n";
$body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
$body .= $text."\r\n\r\n";
$body .= "--$boundary\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
$body .= $html."\r\n\r\n";
$body .= "--$boundary--";

if(mail($TO, $subject_mime, $body, $headers)){
  echo "OK";
} else {
  echo "ERROR";
}
