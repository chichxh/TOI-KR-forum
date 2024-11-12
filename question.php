<?php
session_start();

require_once ('getData.php');

// Получение экземпляра JSONDataManager
$jsonManager = JSONDataManager::getInstance('forum_data.json');

// Получение данных
$data = $jsonManager->getData();

// Пример запрещенных слов
$blacklist = ['Переходи', 'ссылке'];

// Проверка авторизации пользователя
function checkAuthorization() {
    return isset($_SESSION['user_id']);
}

require_once ('handler.php');
require_once ('answerRender.php');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Вопрос</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Форум</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Главная</a>
                    </li>
                    <?php if (checkAuthorization()): ?>
                        <li class='nav-item'>
                            <a class='nav-link' href='lk.php'>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['username']); ?>!</a>
                        </li>
                        <li class='nav-item'>
                            <a class='nav-link' href='logout.php'>Выйти</a>
                        </li>
                    <?php else: ?>
                        <li class='nav-item'>
                            <a class='nav-link' href='auth.php'>Авторизация</a>
                        </li>
                    <?php endif; ?>
                </ul> 
            </div>
        </div>
    </nav>
    
    <div class="container mt-5">
        <div class="row">
            <div class="col-8">
                <?php require 'search.php'; ?>
            </div>
        </div>
        <div class="row mt-5">
            <?php
            if (isset($_GET['id'])) {
                $question_id = $_GET['id'];
                $question_found = false;

                // Вывод вопроса
                foreach ($data['questions'] as $question) {
                    if ($question['id'] == $question_id) {
                        $question_found = true;
                        $author_name = getUserNameById($data['users'], $question['author_id']);
                        $category_name = getCategoryNameById($data['categories'], $question['category_id']);
                        echo "<div class='col-12 d-flex justify-content-between border rounded p-3 mb-3'>";
                        echo "<div><h4>" . htmlspecialchars($question['topic']) . "</h4>";
                        echo "<p>{$question['text']}</p></div>";
                        echo "<div class='text-end'>";
                        echo "<p><strong>Автор:</strong> " . htmlspecialchars($author_name) . "</p>";
                        echo "<p><strong>Категория:</strong> " . htmlspecialchars($category_name) . "</p>";
                        echo "<p><strong>Время:</strong> " . htmlspecialchars($question['timestamp']) . "</p>";
                        echo "</div></div>";
                        break;
                    }
                }

                if ($question_found) {
                    echo "<h3>Ответы:</h3>";

                    // Вывод ответов
                    $is_answers_finded = false;
                    $renderer = new ConcreteAnswerRenderer();
                    foreach ($data['answers'] as $answer) {
                        if ($answer['question_id'] == $question_id) {
                            $is_answers_finded = true;
                            $renderer->render($data, $answer);
                        } 
                    }
                    if (!$is_answers_finded) {
                        echo "<p>Будьте первым, кто ответит</p>";
                    }
                    
                    // Форма для ответа
                    if (checkAuthorization()) {
                        echo '<div class="col-6">';
                            echo '<form action="question.php?id=' . htmlspecialchars($question_id) . '" method="post">';
                                echo '<div class="mb-3">';
                                    echo '<label for="answer_text" class="form-label">Ваш ответ:</label>';
                                    echo '<textarea class="form-control" name="answer_text" rows="4" required></textarea><br>';
                                echo '</div>';
                                echo '<input class="btn btn-primary" type="submit" value="Отправить ответ">';
                            echo '</form>';
                        echo '</div>';
                    } else {
                        echo '<p>Пожалуйста, <a href="auth.php">войдите</a>, чтобы оставить ответ.</p>';
                    }
                } else {
                    echo "Вопрос не найден.";
                    echo '<a href="index.php">Вернуться на главную</a>';
                }
            } else {
                echo "Ошибка: Не указан ID вопроса";
                echo '<a href="index.php">Вернуться на главную</a>';
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
