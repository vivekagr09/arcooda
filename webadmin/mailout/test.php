<?php
include_once 'mailer/htmlMimeMail.php';

$mail = new htmlMimeMail();

$mail->setText("Hello There");

$mail->setReturnPath('wei@josifovski.com');
$mail->setFrom('"Coinopexpress" <wei@josifovski.com>');
$mail->setSubject($subject);
$mail->setHeader('X-Mailer', 'Coinopexpress Mailer');
$mail->setHeader('Return-Path', 'wei@josifovski.com');

echo $mail->send(array("\"test\" <wei@josifovksi2.com>"));
?>