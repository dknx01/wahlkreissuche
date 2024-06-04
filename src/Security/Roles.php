<?php

namespace App\Security;

interface Roles
{
    public const USER = 'ROLE_USER';
    public const ADMIN = 'ROLE_ADMIN';
    public const SUPERVISOR = 'ROLE_SUPERVISOR';
}
