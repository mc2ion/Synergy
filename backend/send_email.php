<?php
require_once "PHPMailer/class.phpmailer.php";
$mail               = new PHPMailer();
$mail->IsSMTP();                                     // set mailer to use SMTP
$mail->Host         = "smtp.gmail.com";              // Servidor de salida
$mail->SMTPSecure   = 'ssl';
$mail->SMTPAuth     = true;
$mail->Username     = "infoeventoplus@gmail.com";    //SMTP username
$mail->Password     = "3v3nt0s.";                    //SMTP password
$mail->Port         = 465;
$mail->CharSet      = 'utf-8';
$mail->SMTPDebug    = 1;

function send_email($subject, $name, $newPass, $to, $bodyKey="password"){
    global $mail;

    if ($bodyKey == "password") {
            $body = "<table style='width:500px;'>";
            $body .= "<tr>
                        <td>Hola $name,<td>
                    </tr>";
            $body .= "<tr><td>Hemos recibido una solicitud para reestablecer la contraseña de su cuenta.</td></tr>";
            $body .= "<tr><td style='padding:10px 0px;'>Su nueva contraseña es: <b>$newPass</b></td></tr>";
            $body .= "<tr><td>Recuerde cambiar su contraseña una vez que ingresa al sistema, dándole clic
                              en su usuario (barra superior derecha) - \"Cambiar contraseña\".</td></tr>";
            $body .= "</table>";
     }

    $mail->From = "infoeventoplus@gmail.com";
    $mail->FromName = "Eventos+";
    $mail->AddAddress($to, $name);

    $mail->IsHTML(true);                                  // set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $body;

    if(!$mail->Send())
    {
       return 0;
    }
    return 1;
}
?>
