<?php
use Rapulo\Core\ORM;

class Migration_create_users_table {
    public function up() {
        $pdo = (new ORM('migrations'))->getPdo();
        $pdo->exec("
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function down() {
        $pdo = (new ORM('migrations'))->getPdo();
        $pdo->exec("DROP TABLE IF EXISTS users");
    }
}