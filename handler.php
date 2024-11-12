<?php
interface Handler {
    public function setNext(Handler $handler): Handler;
    public function handle(array $data): ?array;
}

abstract class AbstractHandler implements Handler {
    private $nextHandler;

    public function setNext(Handler $handler): Handler {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(array $data): ?array {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($data);
        }
        return $data;
    }
}
// Проверка авторизации
class AuthCheckHandler extends AbstractHandler {
    public function handle(array $data): ?array {
        if (!isset($_SESSION['user_id'])) {
            echo "Вы должны быть авторизованы, чтобы оставить ответ.";
            return null;
        }
        return parent::handle($data);
    }
}

// Проверка наличия текста
class AnswerExistenceCheckHandler extends AbstractHandler {
    public function handle(array $data): ?array {
        if (empty($data['answer_text'])) {
            echo "Ответ не может быть пустым.";
            return null;
        }
        return parent::handle($data);
    }
}

// Проверка на запрещенные слова
class BlacklistCheckHandler extends AbstractHandler {
    private $blacklist = ['перейди', 'ссылке'];

    public function handle(array $data): ?array {
        foreach ($this->blacklist as $word) {
            if (stripos($data['answer_text'], $word) !== false) {
                echo "Ответ содержит запрещенные слова.";
                return null;
            }
        }
        return parent::handle($data);
    }
}

// Функция сохранения данных в JSON файл
function saveData($filename, $data) {
    $json_data = json_encode($data, JSON_PRETTY_PRINT);
    if (file_put_contents($filename, $json_data) === false) {
        die("Ошибка при сохранении данных в JSON файл.");
    }
}


// Запись ответа при нажатии кнопки "Отправить"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputData = [
        'answer_text' => $_POST['answer_text'] ?? '',
        'question_id' => $_GET['id'] ?? null,
        'author_id' => $_SESSION['user_id'] ?? null
    ];

    $authCheck = new AuthCheckHandler();
    $answerExistenceCheck = new AnswerExistenceCheckHandler();
    $blacklistCheck = new BlacklistCheckHandler();

    $authCheck->setNext($answerExistenceCheck)->setNext($blacklistCheck);

    $validatedData = $authCheck->handle($inputData);

    if ($validatedData) {
        // Генерация нового ID для ответа
        $new_answer_id = end($data['answers'])['id'] + 1;

        // Создаем новый ответ
        $new_answer = [
            'id' => $new_answer_id,
            'question_id' => $validatedData['question_id'],
            'text' => $validatedData['answer_text'],
            'author_id' => $validatedData['author_id'],
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Добавляем новый ответ в массив ответов
        $data['answers'][] = $new_answer;

        // Сохраняем обновленные данные в JSON файл
        saveData('forum_data.json', $data);

        // Перенаправляем пользователя на ту же страницу, чтобы избежать повторной отправки данных формы при обновлении страницы
        header("Location: question.php?id=" . $validatedData['question_id']);
        exit();
    }
}
?>