<?php

class User {
    private $userId;
    private $login;
    private $password;
    private $role;

    public function __construct($userId, $login, $password, $role) {
        $this->userId = $userId;
        $this->login = $login;
        $this->password = $password;
        $this->role = $role;
    }

    // Геттеры
    public function getUserId() {
        return $this->userId;
    }

    public function getLogin() {
        return $this->login;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getRole() {
        return $this->role;
    }

    // Сеттеры
    public function setLogin($login) {
        $this->login = $login;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setRole($role) {
        $this->role = $role;
    }
}
?>
