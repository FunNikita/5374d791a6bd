<?php

class Comment
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createComment($text, $parentId, $userId)
    {
        $conn = $this->db->getConnection();

        try {
            // Проверяем, существует ли комментарий с указанным родительским ID
            if ($parentId) {
                $parentCommentQuery = "SELECT id FROM comments WHERE id = :parentId";
                $parentCommentStmt = $conn->prepare($parentCommentQuery);
                $parentCommentStmt->bindParam(':parentId', $parentId, PDO::PARAM_INT);
                $parentCommentStmt->execute();

                $parentComment = $parentCommentStmt->fetch();
                if (!$parentComment) {
                    return false; // Родительский комментарий не существует
                }
            }

            // Вставляем новый комментарий в базу данных
            $query = "INSERT INTO comments (text, user_id, parent_id) VALUES (:text, :userId, :parentId)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':text', $text, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':parentId', $parentId, PDO::PARAM_INT);
            $stmt->execute();

            $commentId = $conn->lastInsertId();
            return $commentId;
        } catch (PDOException $e) {
            die('Error creating comment: ' . $e->getMessage());
        }
    }

    public function editComment($id, $text, $userId)
    {
        $conn = $this->db->getConnection();

        try {
            // Проверяем, существует ли комментарий с указанным ID
            $commentQuery = "SELECT * FROM comments WHERE id = :id";
            $commentStmt = $conn->prepare($commentQuery);
            $commentStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $commentStmt->execute();

            $comment = $commentStmt->fetch();
            if (!$comment) {
                return false; // Комментарий не существует
            }

            // Проверяем, является ли пользователь владельцем комментария
            if ($comment['user_id'] != $userId) {
                return false; // Недостаточно прав для редактирования комментария
            }

            // Обновляем текст комментария
            $query = "UPDATE comments SET text = :text WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':text', $text, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return $this->getComment($id); // Возвращаем измененный комментарий
        } catch (PDOException $e) {
            die('Error editing comment: ' . $e->getMessage());
        }
    }

    public function getComment($id)
    {
        $conn = $this->db->getConnection();

        try {
            // Получаем информацию о комментарии
            $query = "SELECT * FROM comments WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $comment = $stmt->fetch();
            if (!$comment) {
                return false; // Комментарий не существует
            }

            // Возвращаем информацию о комментарии
            return $comment;
        } catch (PDOException $e) {
            die('Error getting comment: ' . $e->getMessage());
        }
    }

    public function deleteComment($id, $userId)
    {
        $conn = $this->db->getConnection();

        try {
            // Проверяем, существует ли комментарий с указанным ID
            $commentQuery = "SELECT * FROM comments WHERE id = :id";
            $commentStmt = $conn->prepare($commentQuery);
            $commentStmt->bindParam(':id', $id, PDO::PARAM_INT);
            $commentStmt->execute();

            $comment = $commentStmt->fetch();
            if (!$comment) {
                return false; // Комментарий не существует
            }

            // Проверяем, является ли пользователь владельцем комментария
            if ($comment['user_id'] != $userId) {
                return false; // Недостаточно прав для удаления комментария
            }

            // Удаляем комментарий
            $query = "DELETE FROM comments WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            return true; // Успешное удаление комментария
        } catch (PDOException $e) {
            die('Error deleting comment: ' . $e->getMessage());
        }
    }
}
