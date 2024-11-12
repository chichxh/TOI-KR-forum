<?php
session_start();
require ('getData.php');
require ('JSONFacade.php');


if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $topic = $_POST['topic'];
        $text = $_POST['text'];
        $category_id = $_POST['category'];

        if (empty($topic) || empty($text) || empty($category_id)) {
            echo "Все поля обязательны для заполнения.";
        } else {
            $jsonFacade = new JSONFacade('forum_data.json');
            $jsonFacade->addQuestion($topic, $text, $_SESSION['user_id'], $category_id);
            header("Location: index.php");
            exit();
        }
    }
?>
