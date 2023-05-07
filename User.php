<?php

class User
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function authenticateToken($token)
    {
        $query = "SELECT id FROM users WHERE token = :token";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Токен найден, пользователь аутентифицирован
            return $row['id'];
        } else {
            // Токен не найден, пользователь не аутентифицирован
            die('Ошибка авторизации. Неверный токен.');
        }
    }
}
