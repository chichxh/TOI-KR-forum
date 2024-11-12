<?php
class JSONDataManager {
    private static $instance = null;
    private $data;
    private $jsonFile;

    private function __construct($jsonFile) {
        $this->jsonFile = $jsonFile;
        $this->loadData();
    }

    public static function getInstance($jsonFile) {
        if (self::$instance === null) {
            self::$instance = new self($jsonFile);
        }
        return self::$instance;
    }

    private function loadData() {
        $json_data = file_get_contents($this->jsonFile);
        $this->data = json_decode($json_data, true);
        if ($this->data === null) {
            die("Ошибка при чтении JSON файла.");
        }
    }

    public function getData() {
        return $this->data;
    }

    public function saveData() {
        $json_data = json_encode($this->data, JSON_PRETTY_PRINT);
        if (file_put_contents($this->jsonFile, $json_data) === false) {
            die("Ошибка при сохранении данных в JSON файл.");
        }
    }

    public function addUser($user) {
        $this->data['users'][] = $user;
        $this->saveData();
    }

    public function addQuestion($question) {
        $this->data['questions'][] = $question;
        $this->saveData();
    }

    public function addAnswer($answer) {
        $this->data['answers'][] = $answer;
        $this->saveData();
    }
}
?>
