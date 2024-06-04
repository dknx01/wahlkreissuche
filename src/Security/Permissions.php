<?php

namespace App\Security;

interface Permissions
{
    public const USER_FULLY_ACTIVE = 'fully_active';
    public const ACTIVE = 'active';
    public const VERIFIED = 'verified';
    public const USER_ADMIN = 'admin';

    public const USER_SUPERVISOR = 'supervisor';
}
