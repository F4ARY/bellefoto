<?php

require 'mail/Exception.php';
require 'mail/SMTP.php';
require 'mail/PHPMailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function InviaEmail($nomeMittente, $emailDestinatario, $oggetto, $corpo)
{
    $mail = new PHPMailer(TRUE);

    $INI = parse_ini_file('config.ini', true);
    $mailConfig = $INI['mail'];
    $server = $mailConfig["server"];
    $port = $mailConfig["port"];
    $auth = $mailConfig["auth"] != "0";
    $encryption = $mailConfig["encryption"];
    $username = $mailConfig["username"];
    $password = $mailConfig["password"];

    $mail->SMTPDebug = false;
    $mail->isSMTP();
    $mail->Host = $server;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = $encryption;
    $mail->Port = $port;

    $mail->isHTML(true);
    $mail->setFrom($username, $nomeMittente);
    $mail->addAddress($emailDestinatario);

    $mail->Subject = $oggetto;
    $mail->Body = $corpo;

    try {
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

?>