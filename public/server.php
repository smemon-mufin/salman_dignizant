<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ProjectSocket implements MessageComponentInterface {
    protected $clients;
    protected $userProjects; // Map conn resourceId -> [userId, projectId]

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->userProjects = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        if (!$data || !isset($data['type'])) return;

        switch ($data['type']) {
            case 'join':
                $userId = $data['userId'];
                $projectId = $data['projectId'];
                $this->userProjects[$from->resourceId] = [$userId, $projectId];
                $this->broadcastOnline($projectId);
                break;

            case 'leave':
                unset($this->userProjects[$from->resourceId]);
                if (isset($data['projectId'])) {
                    $this->broadcastOnline($data['projectId']);
                }
                break;

            case 'status':
                // Broadcast task status change to all in project
                $projectId = $data['projectId'];
                $this->broadcastToProject($projectId, [
                    'type' => 'taskStatus',
                    'taskId' => $data['taskId'],
                    'status' => $data['status'],
                    'userName' => $data['userName'],
                    'taskTitle' => $data['taskTitle']
                ]);
                break;

            case 'comment':
                $projectId = $data['projectId'];
                $this->broadcastToProject($projectId, [
                    'type' => 'taskComment',
                    'taskId' => $data['taskId'],
                    'comment' => $data['comment'],
                    'userName' => $data['userName'],
                    'taskTitle' => $data['taskTitle']
                ]);
                break;

            case 'assign':
                // Send task assignment notification to specific user
                $assignedUserId = $data['assignedUserId'];
                $this->sendToUser($assignedUserId, [
                    'type' => 'taskAssigned',
                    'taskId' => $data['taskId'],
                    'taskTitle' => $data['taskTitle'],
                    'fromUserName' => $data['fromUserName']
                ]);
                break;
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        unset($this->userProjects[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }

    protected function broadcastOnline($projectId) {
        $userList = [];
        foreach ($this->userProjects as $rid => [$userId, $projId]) {
            if ($projId == $projectId) {
                $userList[$userId] = $userId;
            }
        }
        foreach ($this->clients as $client) {
            if (isset($this->userProjects[$client->resourceId]) && $this->userProjects[$client->resourceId][1] == $projectId) {
                $client->send(json_encode([
                    'type' => 'onlineUsers',
                    'users' => array_values($userList)
                ]));
            }
        }
    }

    protected function broadcastToProject($projectId, $msg) {
        foreach ($this->clients as $client) {
            if (isset($this->userProjects[$client->resourceId]) && $this->userProjects[$client->resourceId][1] == $projectId) {
                $client->send(json_encode($msg));
            }
        }
    }

    protected function sendToUser($userId, $msg) {
        foreach ($this->clients as $client) {
            if (isset($this->userProjects[$client->resourceId]) && $this->userProjects[$client->resourceId][0] == $userId) {
                $client->send(json_encode($msg));
            }
        }
    }
}

$server = \Ratchet\Server\IoServer::factory(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer(
            new ProjectSocket()
        )
    ),
    8081
);

$server->run();