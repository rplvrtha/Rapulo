<?php
use Rapulo\Core\ORM;

class UserSeed {
    public function run() {
        ORM::table('users')->create([
            'username' => 'admin',
            'password' => password_hash('password', PASSWORD_DEFAULT),
        ]);
    }
}