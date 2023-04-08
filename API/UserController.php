<?php

namespace Niwee\Trident\API;

use Niwee\Trident\Core\Auth;

final class UserController extends ApiController
{
    public function __construct()
    {
        $this->auth = new Auth();
    }

    public function login()
    {
        $this->auth->login();
    }

    public function logout()
    {
        $this->auth->logout();
    }
}
