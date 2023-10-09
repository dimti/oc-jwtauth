<?php

namespace Vdomah\JWTAuth\Classes;

use Tymon\JWTAuth\JWTAuth;

class OctoberJWTAuth extends JWTAuth
{
    public function getJwtUser(int $userId)
    {
        if (!$this->auth->byId($userId)) {
            return false;
        }

        return $this->user();
    }
}
