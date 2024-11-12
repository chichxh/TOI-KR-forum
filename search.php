<?php 
// Общий интерфейс для всех типов данных
interface Searchable {
    public function getText(): string;
    public function getRelevance(array $keywords): int;
}

// Класс для данных вопросов
class Question implements Searchable {
    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function getText(): string {
        return $this->data['text'];
    }

    public function getRelevance(array $keywords): int {
        return $this->calculateRelevance($keywords, $this->getText());
    }

    private function calculateRelevance(array $keywords, string $text): int {
        $relevance = 0;
        foreach ($keywords as $keyword) {
            $relevance += substr_count(strtolower($text), strtolower($keyword));
        }
        return $relevance;
    }
}

// Класс для данных ответов
class Answer implements Searchable {
    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function getText(): string {
        return $this->data['text'];
    }

    public function getRelevance(array $keywords): int {
        return $this->calculateRelevance($keywords, $this->getText());
    }

    private function calculateRelevance(array $keywords, string $text): int {
        $relevance = 0;
        foreach ($keywords as $keyword) {
            $relevance += substr_count(strtolower($text), strtolower($keyword));
        }
        return $relevance;
    }
}

// Класс для данных пользователей
class User implements Searchable {
    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function getText(): string {
        return $this->data['username'];
    }

    public function getRelevance(array $keywords): int {
        return $this->calculateRelevance($keywords, $this->getText());
    }

    private function calculateRelevance(array $keywords, string $text): int {
        $relevance = 0;
        foreach ($keywords as $keyword) {
            $relevance += substr_count(strtolower($text), strtolower($keyword));
        }
        return $relevance;
    }
}

// Фабрика для создания объектов
class SearchableFactory {
    public static function create($type, $data): Searchable {
        switch ($type) {
            case 'question':
                return new Question($data);
            case 'answer':
                return new Answer($data);
            case 'user':
                return new User($data);
            default:
                throw new Exception("Unknown type: $type");
        }
    }
}

// Класс для выполнения поиска по ключевым словам
class SearchEngine {
    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function search($query) {
        $keywords = $this->extractKeywords($query);
        $results = [];

        foreach ($this->data['questions'] as $question) {
            $searchable = SearchableFactory::create('question', $question);
            $relevance = $searchable->getRelevance($keywords);
            if ($relevance > 0) {
                $results[] = ['type' => 'question', 'data' => $question, 'relevance' => $relevance];
            }
        }

        foreach ($this->data['answers'] as $answer) {
            $searchable = SearchableFactory::create('answer', $answer);
            $relevance = $searchable->getRelevance($keywords);
            if ($relevance > 0) {
                $results[] = ['type' => 'answer', 'data' => $answer, 'relevance' => $relevance];
            }
        }

        foreach ($this->data['users'] as $user) {
            $searchable = SearchableFactory::create('user', $user);
            $relevance = $searchable->getRelevance($keywords);
            if ($relevance > 0) {
                $results[] = ['type' => 'user', 'data' => $user, 'relevance' => $relevance];
            }
        }

        usort($results, function ($a, $b) {
            return $b['relevance'] - $a['relevance'];
        });

        return $results;
    }

    private function extractKeywords($query) {
        return explode(' ', $query);
    }
}

require_once ('getData.php');

// Получение экземпляра JSONDataManager
$jsonManager = JSONDataManager::getInstance('forum_data.json');

// Получение данных
$data = $jsonManager->getData();

require_once ('getInfoById.php');

// Результаты поиска
$searchResults = [];
if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $searchEngine = new SearchEngine($data);
    $searchResults = $searchEngine->search($query);
}
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
    
        <div class="container mt-5">
            <!-- Форма поиска -->
            <div class="row">
                <div class="col-12">
                    <form method="get" action="search.php">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="query" placeholder="Введите слово или фразу для поиска" aria-label="Поиск" aria-describedby="button-search">
                            <button class="btn btn-outline-secondary" type="submit" id="button-search">Поиск</button>
                        </div>
                    </form>
                </div>
            </div>
    
            <!-- Вывод результатов -->
            <div class="row mt-5">
                <?php if (!empty($searchResults)): ?>
                    <h3>Результаты поиска:</h3>
                    <?php foreach ($searchResults as $result): ?>
                        <div class="col-12 border rounded p-3 mb-3">
                            <?php if ($result['type'] == 'question'): ?>
                                <h4>Вопрос: <?php echo htmlspecialchars($result['data']['topic']); ?></h4>
                                <p><?php echo htmlspecialchars($result['data']['text']); ?></p>
                                <p>Автор: <?php echo htmlspecialchars(getUserNameById($data['users'], $result['data']['author_id'])); ?></p>
                            <?php elseif ($result['type'] == 'answer'): ?>
                                <h4>Ответ на вопрос ID <?php echo htmlspecialchars($result['data']['question_id']); ?></h4>
                                <p><?php echo htmlspecialchars($result['data']['text']); ?></p>
                                <p>Автор: <?php echo htmlspecialchars(getUserNameById($data['users'], $result['data']['author_id'])); ?></p>
                            <?php elseif ($result['type'] == 'user'): ?>
                                <h4>Пользователь: <?php echo htmlspecialchars($result['data']['username']); ?></h4>
                                <p>Email: <?php echo htmlspecialchars($result['data']['email']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php elseif (isset($_GET['query'])): ?>
                    <p>Результаты не найдены.</p>
                <?php endif; ?>
                <!-- <div class="col-12 mt-3">
                    <a href="index.php" class="btn btn-primary">На главную</a>
                </div> -->
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>