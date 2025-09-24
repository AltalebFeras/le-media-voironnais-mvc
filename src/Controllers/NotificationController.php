<?php

namespace src\Controllers;

use src\Abstracts\AbstractController;
use src\Repositories\NotificationRepository;

class NotificationController extends AbstractController
{
    protected $repo;
    public function __construct()
    {
        $this->repo = new NotificationRepository();
    }
    
    public function notificationsCount(): void
    {
        header('Content-Type: application/json');
        http_response_code(200);

        try {
            $idUser = $_SESSION['idUser'] ?? null;
            if (!$idUser) {
                echo json_encode(['success' => true, 'count' => 0]);
                return;
            }
            $count = $this->repo->getUnreadNotificationsCount((int)$idUser);
            echo json_encode(['success' => true, 'count' => (int)$count]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'count' => 0]);
        }
    }

    public function notificationsList(): void
    {
        header('Content-Type: application/json');
        http_response_code(200);

        try {
            $idUser = $_SESSION['idUser'] ?? null;
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limit = isset($_GET['limit']) ? max(1, min(50, (int)$_GET['limit'])) : 10;

            if (!$idUser) {
                echo json_encode(['success' => true, 'items' => [], 'page' => $page, 'hasMore' => false]);
                return;
            }

            $offset = ($page - 1) * $limit;
            $rows = $this->repo->getNotificationsPage((int)$idUser, $limit + 1, $offset);

            $hasMore = count($rows) > $limit;
            if ($hasMore) {
                array_pop($rows);
            }

            $items = array_map(function ($r) {
                return [
                    // include both for compatibility with the frontend
                    'idNotification' => (int)$r['idNotification'],
                    'id' => (int)$r['idNotification'],
                    'title' => (string)$r['title'],
                    'message' => (string)$r['message'],
                    'type' => (string)$r['type'],
                    'isRead' => (int)$r['isRead'],
                    'createdAt' => (string)$r['createdAt'],
                    'url' => $r['url'] ?? null,
                ];
            }, $rows);

            echo json_encode([
                'success' => true,
                'items' => $items,
                'page' => $page,
                'hasMore' => $hasMore
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false, 'items' => [], 'page' => 1, 'hasMore' => false]);
        }
    }

    public function notificationMarkRead(): void
    {
        header('Content-Type: application/json');
        http_response_code(200);

        try {
            $payload = json_decode(file_get_contents('php://input'), true) ?: [];
            $id = isset($payload['id']) ? (int)$payload['id'] : 0;
            $idUser = $_SESSION['idUser'] ?? null;

            if (!$idUser || $id <= 0) {
                echo json_encode(['success' => false]);
                return;
            }

            $ok = $this->repo->markNotificationRead((int)$idUser, $id);
            echo json_encode(['success' => (bool)$ok]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false]);
        }
    }

    public function notificationsMarkAllRead(): void
    {
        header('Content-Type: application/json');
        http_response_code(200);

        try {
            $idUser = $_SESSION['idUser'] ?? null;
            if (!$idUser) {
                echo json_encode(['success' => false]);
                return;
            }
            $affected = $this->repo->markAllNotificationsRead((int)$idUser);
            echo json_encode(['success' => $affected >= 0]);
        } catch (\Throwable $e) {
            echo json_encode(['success' => false]);
        }
    }

    public function displayNotifications(): void
    {
        // Server-render the shell; items are fetched via /notifications/list
        $this->render('user/notifications', ['title' => 'Mes notifications']);
    }
}

