<?php
// Функция для получения имени автора по его ID
function getUserNameById($users, $author_id) {
    foreach ($users as $user) {
        if ($user['id'] === $author_id) {
            return $user['username'];
        }
    }
    return "Неизвестный автор";
}

// Функция для получения названия категории по ее ID
function getCategoryNameById($categories, $category_id) {
    foreach ($categories as $category) {
        if ($category['id'] === $category_id) {
            return $category['name'];
        }
    }
    return "Неизвестная категория";
}
?>