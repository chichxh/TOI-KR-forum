<?php
// Начало сессии
session_start();

require_once ('getData.php');

// Получение экземпляра JSONDataManager
$jsonManager = JSONDataManager::getInstance('forum_data.json');

// Получение данных
$data = $jsonManager->getData();
?>


<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Главная</title>
    </head>
    <body>

        <!-- Начало шапки -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container">
                <a class="navbar-brand" href="index.php">Форум</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Главная</a>
                        </li>
                        <?php
                            if (isset($_SESSION['user_id'])) {
                                echo "<li class='nav-item'>";
                                echo "<a class='nav-link' href='lk.php'>Добро пожаловать, " . htmlspecialchars($_SESSION['username']) . "!</a>";
                                echo "</li>";
                                echo "<li class='nav-item'>";
                                echo "<a class='nav-link' href='logout.php'>Выйти</a>";
                                echo "</li>";
                            }
                            else {
                                echo "<li class='nav-item'>";
                                echo "<a class='nav-link' href='auth.php'>Авторизация</a>";
                                echo "</li>";
                            }
                        ?>
                    </ul> 
                </div>
            </div>
        </nav>
        <!-- Конец шапки -->


        <div class="container mt-5">
            <div class="row">
                <div class="col-8">  
                    <!-- Начало поисковик -->
                    <?php require('search.php'); ?>
                    <!-- Конец поисковик -->

                    <!-- Начало вывода вопросов-->
                    <div class="container mt-5 questions">
                        <h1 class="mb-3">Недавно заданные вопросы</h1>
 
                        <?php
                        foreach ($data['questions'] as $question) {
                            $author_name = getUserNameById($data['users'], $question['author_id']);
                            $category_name = getCategoryNameById($data['categories'], $question['category_id']);
                            echo "<a href='question.php?id=".$question['id']."'>";
                            echo "<div class='d-flex justify-content-between border rounded p-3 mb-3'>";
                            echo "<h4>" . htmlspecialchars($question['topic']) . "</h4>";
                            echo "<div class='text-end'>";
                            echo "<p><strong>Автор:</strong> " . htmlspecialchars($author_name) . "</p>";
                            echo "<p><strong>Категория:</strong> " . htmlspecialchars($category_name) . "</p>";
                            echo "<p><strong>Время:</strong> " . htmlspecialchars($question['timestamp']) . "</p>";
                            echo "</div></div></a>";
                        }
                        ?>  
                    </div>
                    <!-- Конец вывода вопросов -->

                </div>
                <div class="col-4">
                    <!-- Начало форма для ввода вопроса -->  
                    <div class="container border rounded p-3">
                        <?php
                        // Проверка авторизации
                        if (isset($_SESSION['user_id'])) {
                            echo "<h3 class='mb-3'>Введите ваш вопрос</h3>";
                            echo '<form action="add_question.php" method="post">
                                    <div class="mb-3">
                                        <label for="topic" class="form-label">Тема:</label>
                                        <input type="text" id="topic" name="topic" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="text" class="form-label">Текст:</label>
                                        <textarea id="text" name="text" class="form-control" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Категория:</label>
                                        <select id="category" name="category" class="form-select" required>
                                    </div>';
                            foreach ($data['categories'] as $category) {
                                echo '<option value="' . htmlspecialchars($category['id']) . '">' . htmlspecialchars($category['name']) . '</option>';
                            }
                            echo '</select><br>
                                    <button type="submit" class="btn btn-primary">Отправить</button>
                                  </form>';
                        } else {
                            echo "<p><a href='auth.php'>Войдите</a>, чтобы оставить запись</p>";
                        }
                        ?>
                    </div>
                    <!-- Конец форма для ввода вопроса -->
                </div>
            </div>
        </div>
            
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>