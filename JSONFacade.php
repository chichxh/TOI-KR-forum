<?php
class JSONFacade {
    private $jsonManager;

    public function __construct($jsonFile) {
        $this->jsonManager = JSONDataManager::getInstance($jsonFile);
    }

    public function addUser($username, $email, $password, $role) {
        $new_user_id = count($this->jsonManager->getData()['users']) + 1;
        $user = [
            'id' => $new_user_id,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role
        ];
        $this->jsonManager->addUser($user);
    }

    public function addQuestion($topic, $text, $author_id, $category_id) {
        $data = $this->jsonManager->getData();
        $new_question_id = end($data['questions'])['id'] + 1;
        $question = [
            'id' => $new_question_id,
            'topic' => $topic,
            'text' => $text,
            'author_id' => $author_id,
            'timestamp' => date('Y-m-d H:i:s'),
            'category_id' => (int)$category_id
        ];
        $this->jsonManager->addQuestion($question);
    }

    public function addAnswer($question_id, $text, $author_id) {
        $data = $this->jsonManager->getData();
        $new_answer_id = end($data['answers'])['id'] + 1;
        $answer = [
            'id' => $new_answer_id,
            'question_id' => $question_id,
            'text' => $text,
            'author_id' => $author_id,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        $this->jsonManager->addAnswer($answer);
    }

    public function getQuestions() {
        return $this->jsonManager->getData()['questions'];
    }

    public function getAnswers() {
        return $this->jsonManager->getData()['answers'];
    }

    public function getUsers() {
        return $this->jsonManager->getData()['users'];
    }

    public function getCategories() {
        return $this->jsonManager->getData()['categories'];
    }
}
?>
