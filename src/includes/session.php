<?php
declare(strict_types=1);

class Session {
    private static ?Session $instance = null;

    public static function getInstance(): Session{
        if(self::$instance == null){
            self::$instance = new Session();
        }

        return self::$instance;
    }

    public function __construct(){
        session_start();
    }

    public function getUser(){
        return $_SESSION["user"];
    }

    public function login(array $user): void {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'type' => $user['type']
        ];
    }

    public function logout(){
        session_destroy();
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION["user"]);
    }

    public function getUserId(): ?int {
        return $this->isLoggedIn() ? (int)$_SESSION["user"]["id"] : null;
    }
}


?>