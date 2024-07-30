<?php

namespace App\Config;

enum UserRole: string
{
    case ROLE_ADMIN = 'admin';
    case ROLE_USER = 'user';
}