<?php
class User {
    protected $id;
    protected $username;
    protected $password;
    protected $email;
    protected $created_at;

    public function __construct() {
        $this->created_at = date('Y-m-d H:i:s');
    }

    // Getters and setters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getCreatedAt() { return $this->created_at; }

    public function setUsername($username) { $this->username = $username; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { $this->password = password_hash($password, PASSWORD_DEFAULT); }
}