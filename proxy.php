<?php
class JSONDataManagerProxy {
    private $jsonManager;
    private $userRole;

    public function __construct($jsonFile, $userRole) {
        $this->jsonManager = JSONDataManager::getInstance($jsonFile);
        $this->userRole = $userRole;
    }

    public function getData() {
        return $this->jsonManager->getData();
    }

    public function saveData() {
        if ($this->userRole !== 'admin') {
            die("У вас нет прав для выполнения этой операции.");
        }
        $this->jsonManager->saveData();
    }

    public function addUser($user) {
        if ($this->userRole !== 'admin') {
            die("У вас нет прав для выполнения этой операции.");
        }
        $this->jsonManager->addUser($user);
    }

    public function addQuestion($question) {
        if ($this->userRole !== 'admin' && $this->userRole !== 'user') {
            die("У вас нет прав для выполнения этой операции.");
        }
        $this->jsonManager->addQuestion($question);
    }

    public function addAnswer($answer) {
        if ($this->userRole !== 'admin' && $this->userRole !== 'user') {
            die("У вас нет прав для выполнения этой операции.");
        }
        $this->jsonManager->addAnswer($answer);
    }
}
?>