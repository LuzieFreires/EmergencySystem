<?php

class Auth {
    private $db;
    private $table = 'users';

    public function __construct($db) {
        $this->db = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register($username, $password, $email) {
        try {
            $query = "INSERT INTO {$this->table} (username, password, email)
                      VALUES (:username, :password, :email)";
            $stmt = $this->db->prepare($query);

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function login($username, $password) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_OBJ);

                if (password_verify($password, $user->password)) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['last_activity'] = time();
                    return $user;
                }
            }
            return false;
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            throw new Exception('Login failed');
        }
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function getLoggedInUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        try {
            $query = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_OBJ) : null;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
}
