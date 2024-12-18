<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './vendor/autoload.php'; // Composer

if (
    !empty(trim($_POST['first-name'])) &&
    !empty(trim($_POST['last-name'])) &&
    !empty(trim($_POST['telephone'])) &&
    !empty(trim($_POST['city'])) &&
    !empty(trim($_POST['service'])) &&
    !empty(trim($_POST['region'])) &&
    !empty(trim($_POST['email'])) &&
    filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) &&
    !empty(trim($_POST['message']))
) {
    $firstName = strip_tags(trim($_POST['first-name']));
    $lastName = strip_tags(trim($_POST['last-name']));
    $telephone = strip_tags(trim($_POST['telephone']));
    $city = strip_tags(trim($_POST['city']));
    $service = strip_tags(trim($_POST['service']));
    $region = strip_tags(trim($_POST['region']));
    $email = strip_tags(trim($_POST['email']));
    $messageContent = strip_tags(trim($_POST['message']));

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.timeweb.ru';
        $mail->SMTPAuth = true;
        $mail->Username = 'service@dachacotedazur.com'; // SMTP email
        $mail->Password = '22k07a03p'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS
        $mail->Port = 587;

        $mail->setFrom('service@dachacotedazur.com', 'dachacotedazur');
        $mail->addAddress('sanchezzkusika@gmail.com');

        $subject = "Nouvelle demande de service : " . $service;
        $mail->Subject = $subject;

        // Указание URL файла
        $templateFile = 'https://dachacotedazur.com/php/email-template.html';

        // Инициализация CURL для загрузки файла
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $templateFile);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Следовать редиректам
        $templateContent = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Проверка успешности загрузки
        if ($httpCode !== 200 || $templateContent === false) {
            echo json_encode([
                'status' => false,
                'message' => 'Не удалось загрузить HTML-шаблон.',
            ]);
            exit;
        }

        // Вставка данных в шаблон
        $templateContent = str_replace(
            ['$firstName', '$lastName', '$telephone', '$city', '$service', '$region', '$email', '$messageContent'],
            [$firstName, $lastName, $telephone, $city, $service, $region, $email, $messageContent],
            $templateContent
        );

        $mail->isHTML(true);
        $mail->Body = $templateContent;

        $mail->send();

        echo json_encode([
            'status' => true,
            'message' => 'Сообщение успешно отправлено',
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => false,
            'message' => "Ошибка при отправке письма: {$mail->ErrorInfo}",
        ]);
    }
} else {
    $response = [
        'status' => false,
        'message' => [],
    ];

    if (empty(trim($_POST['first-name']))) {
        $response['message'][] = "Le champ 'Prénom' ne peut pas être vide.";
    }

    if (empty(trim($_POST['last-name']))) {
        $response['message'][] = "Le champ 'Nom' ne peut pas être vide.";
    }

    if (empty(trim($_POST['telephone']))) {
        $response['message'][] = "Le champ 'Téléphone' ne peut pas être vide.";
    }

    if (empty(trim($_POST['city']))) {
        $response['message'][] = "Le champ 'Ville' ne peut pas être vide.";
    }

    if (empty(trim($_POST['service']))) {
        $response['message'][] = "Le champ 'Service' ne peut pas être vide.";
    }

    if (empty(trim($_POST['region']))) {
        $response['message'][] = "Le champ 'Région' ne peut pas être vide.";
    }

    if (empty(trim($_POST['email']))) {
        $response['message'][] = "Le champ 'Email' ne peut pas être vide.";
    }

    if (!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        $response['message'][] = "Veuillez entrer un format valide pour 'Email'.";
    }

    if (empty(trim($_POST['message']))) {
        $response['message'][] = "Le champ 'Message' ne peut pas être vide.";
    }

    echo json_encode($response);
}
