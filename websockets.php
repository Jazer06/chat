<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require __DIR__ . '/vendor/autoload.php';


defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

// Инициализация
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
$config = require __DIR__ . '/config/web.php';
$app = new \yii\web\Application($config);


class Chat implements MessageComponentInterface {
    /**
     * @var \SplObjectStorage Хранилище активных подключений клиентов
     */
    protected $clients;

    /**
     * Конструктор класса Chat
     * Инициализирует хранилище клиентов
     */
    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    /**
     * Обработчик нового подключения клиента
     * @param ConnectionInterface $conn Объект соединения с клиентом
     */
    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Новое соединение! ({$conn->resourceId})\n";
    }

    /**
     * Обработчик входящих сообщений от клиентов
     * @param ConnectionInterface $from Клиент-отправитель сообщения
     * @param string $msg Сообщение в формате JSON
     */
    public function onMessage(ConnectionInterface $from, $msg) {

        $data = json_decode($msg, true);

        $username = $data['username'] ?? 'anonymous';
        $message = $data['message'] ?? '';


        $model = new \app\models\ChatMessage();
        $model->username = $username;
        $model->message = $message;
        $model->created_at = time();
        $model->save();


        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'username' => $username,
                'message' => $message
            ]));
        }
    }

    /**
     * Обработчик закрытия соединения клиентом
     * @param ConnectionInterface $conn Закрытое соединение
     */
    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Соединение закрыто ({$conn->resourceId})\n";
    }

    /**
     * Обработчик ошибок соединения
     * @param ConnectionInterface $conn Соединение, в котором произошла ошибка
     * @param \Exception $e Объект исключения
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Ошибка: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Создаем и запускаем WebSocket сервер
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8085 
);


echo "WebSocket-сервер запущен на ws://chat:8085\n";

$server->run();