<?php
session_start();

require_once ('getData.php');

// Получение экземпляра JSONDataManager
$jsonManager = JSONDataManager::getInstance('forum_data.json');

// Получение данных
$data = $jsonManager->getData();

// // Функция для проверки пользователя
function authenticate($email, $password, $users) {
    foreach ($users as $user) {
        if ($user['email'] === $email && password_verify($password, $user['password'])) {
            return $user;
        }
    }
    return null;
}

// Проверка, что данные были отправлены методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if ($email && $password) {
        $user = authenticate($email, $password, $data['users']);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit();
        } else {
            echo "Неверный email или пароль.";
        }
    } else {
        echo "Пожалуйста, введите email и пароль.";
    }
} else {
    echo "Неправильный метод запроса.";
}
?>
