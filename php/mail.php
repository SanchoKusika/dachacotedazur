<?php

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

	$mail_to = "specialist@example.com"; // Почта специалиста, куда будет отправлено письмо
	$email_from = strip_tags(trim($_POST['email'])); // Email отправителя (клиента)
	$name_from = strip_tags(trim($_POST['first-name'])) . " " . strip_tags(trim($_POST['last-name'])); // Имя и фамилия отправителя
	$subject = "Новый запрос на услугу: " . strip_tags(trim($_POST['service'])); // Тема письма, включающая выбранную услугу
	

    // Формируем текст письма
    $message = "Вам пришло новое сообщение с сайта: <br><br>\n" .
        "<strong>Имя:</strong> " . strip_tags(trim($_POST['first-name'])) . "<br>\n" .
        "<strong>Фамилия:</strong> " . strip_tags(trim($_POST['last-name'])) . "<br>\n" .
        "<strong>Телефон:</strong> " . strip_tags(trim($_POST['telephone'])) . "<br>\n" .
        "<strong>Город:</strong> " . strip_tags(trim($_POST['city'])) . "<br>\n" .
        "<strong>Услуга:</strong> " . strip_tags(trim($_POST['service'])) . "<br>\n" .
        "<strong>Регион:</strong> " . strip_tags(trim($_POST['region'])) . "<br>\n" .
        "<strong>Email:</strong> " . strip_tags(trim($_POST['email'])) . "<br>\n" .
        "<strong>Сообщение:</strong> " . strip_tags(trim($_POST['message'])) . "<br>\n";

    // Формируем тему письма
    $subject = "=?utf-8?B?" . base64_encode($subject) . "?=";

    // Формируем заголовки письма
    $headers = "MIME-Version: 1.0" . PHP_EOL .
        "Content-Type: text/html; charset=utf-8" . PHP_EOL .
        "From: " . "=?utf-8?B?" . base64_encode($name_from) . "?=" . "<" . $email_from . ">" .  PHP_EOL .
        "Reply-To: " . $email_from . PHP_EOL;

    // Отправляем письмо
    $mailResult = mail($mail_to, $subject, $message, $headers);

    if ($mailResult) {
        $response = [
            'status' => true,
            'message' => 'Сообщение успешно отправлено'
        ];
    } else {
        $response = [
            'status' => false,
            'message' => 'Произошла ошибка при отправке письма'
        ];
    }

    echo json_encode($response);

} else {
    $response = [
        'status' => false,
        'message' => []
    ];

    if (empty(trim($_POST['first-name']))) {
        $response['message'][] = "Поле 'Имя' не может быть пустым.";
    }

    if (empty(trim($_POST['last-name']))) {
        $response['message'][] = "Поле 'Фамилия' не может быть пустым.";
    }

    if (empty(trim($_POST['telephone']))) {
        $response['message'][] = "Поле 'Телефон' не может быть пустым.";
    }

    if (empty(trim($_POST['city']))) {
        $response['message'][] = "Поле 'Город' не может быть пустым.";
    }

    if (empty(trim($_POST['service']))) {
        $response['message'][] = "Поле 'Услуга' не может быть пустым.";
    }

    if (empty(trim($_POST['region']))) {
        $response['message'][] = "Поле 'Регион' не может быть пустым.";
    }

    if (empty(trim($_POST['email']))) {
        $response['message'][] = "Поле 'Email' не может быть пустым.";
    }

    if (!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        $response['message'][] = "Введите верный формат 'Email'.";
    }

    if (empty(trim($_POST['message']))) {
        $response['message'][] = "Поле 'Сообщение' не может быть пустым.";
    }

    echo json_encode($response);
}