<?php
namespace Rapulo\Features\Auth;

use Rapulo\Core\ORM;

class UserModel extends ORM
{
    public function __construct()
    {
        parent::__construct('users');
    }
}