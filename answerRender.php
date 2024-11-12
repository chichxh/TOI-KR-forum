<?php
abstract class AnswerRenderer {
    // Template method
    public function render($data, $answer) {
        if ($this->checkAnswerExistence($data, $answer) &&
            $this->checkForBlacklistWords($answer['text'])) {
            $this->renderAnswer($data, $answer);
        } else {
            $this->handleInvalidAnswer();
        }
    }

    // Метод для проверки существования ответа
    protected function checkAnswerExistence($data, $answer) {
        return isset($answer);
    }

    // Метод для проверки на наличие запрещенных слов
    protected function checkForBlacklistWords($text) {
        $blacklist = ['запрещенное', 'слово'];
        foreach ($blacklist as $word) {
            if (stripos($text, $word) !== false) {
                return false;
            }
        }
        return true;
    }

    // Абстрактный метод для вывода ответа
    abstract protected function renderAnswer($data, $answer);

    // Метод для обработки неверных ответов
    protected function handleInvalidAnswer() {
        echo "<p>Ответ содержит запрещенные слова или не существует.</p>";
    }
}

//конкретный класс для вывода ответа
class ConcreteAnswerRenderer extends AnswerRenderer {
    protected function renderAnswer($data, $answer) {
        $answer_author_name = getUserNameById($data['users'], $answer['author_id']);
        echo "<div class='row ps-3 mb-4'>";
        echo "<div class='col-8'>";
        echo "<p>{$answer['text']}</p>";
        echo "</div>";
        echo "<div class='col-4 text-end'>";
        echo "<p class='answer'> Автор ответа:" . htmlspecialchars($answer_author_name) . "</p>";
        echo "<p>Когда ответили: {$answer['timestamp']}</p>";
        echo "</div>";
        echo "</div>";
        echo "<hr>";
    }
}

?>