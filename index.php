<?php

require_once 'Database.php';
require_once 'User.php';
require_once 'Comment.php';

$db = new Database();
$user = new User($db);
$comment = new Comment($db);

// Получение HTTP-метода и пути из URL
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? '';

// Аутентификация пользователя по токену
$authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = str_replace('Bearer ', '', $authorizationHeader);
$authenticatedUserId = $user->authenticateToken($token);

// Парсинг входных данных из JSON
$input = json_decode(file_get_contents('php://input'), true);

// Обработка запросов
switch ($method) {
    case 'POST':
        if ($path === '/comment') {
            // Создание нового комментария
            $text = $input['text'];
            $parentId = $input['parentId'] ?? null;
            $userId = $authenticatedUserId;

            $commentId = $comment->createComment($text, $parentId, $userId);
            if ($commentId) {
                http_response_code(201);
                echo json_encode(['id' => $commentId]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid parent comment ID']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
        }
        break;

    case 'PUT':
        if (preg_match('/^\/comment\/(\d+)$/', $path, $matches)) {
            // Редактирование существующего комментария
            $commentId = $matches[1];
            $text = $input['text'];
            $userId = $authenticatedUserId;

            $updatedComment = $comment->editComment($commentId, $text, $userId);
            if ($updatedComment) {
                echo json_encode($updatedComment);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid comment ID or insufficient permissions']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
        }
        break;

    case 'GET':
        if (preg_match('/^\/comment\/(\d+)$/', $path, $matches)) {
            // Получение информации о комментарии
            $commentId = $matches[1];

            $commentInfo = $comment->getComment($commentId);
            if ($commentInfo) {
                echo json_encode($commentInfo);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid comment ID']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
        }
        break;

    case 'DELETE':
        if (preg_match('/^\/comment\/(\d+)$/', $path, $matches)) {
            // Удаление комментария
            $commentId = $matches[1];
            $userId = $authenticatedUserId;

            $result = $comment->deleteComment($commentId, $userId);
            if ($result) {
                http_response_code(200);
                echo json_encode(['message' => 'Comment deleted successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid comment ID or insufficient permissions']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Not Found']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}
