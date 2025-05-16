<?php
declare(strict_types=1);

require_once __DIR__ . '/../database/userclass.php';

class Session {
    private static ?Session $instance = null;

    public static function getInstance(): Session {
        if (self::$instance == null) {
            self::$instance = new Session();
        }

        return self::$instance;
    }

    public function __construct() {
        session_start();
    }

    public function getUser(): ?User {
        return isset($_SESSION["user"]) ? $_SESSION["user"] : null;
    }

    public function login(User $user): void {
        $_SESSION['user'] = $user;
    }

    public function logout(): void {
        session_destroy();
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION["user"]);
    }

    public function getUserId(): ?int {
        return $this->isLoggedIn() ? $_SESSION["user"]->id : null;
    }

    public function getUserUsername(): ?string {
        return $this->isLoggedIn() ? $_SESSION["user"]->username : null;
    }
}
?>