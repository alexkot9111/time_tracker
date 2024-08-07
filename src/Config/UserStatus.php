<?php

namespace App\Config;

enum UserStatus: int
{
    case STATUS_NOT_ACTIVE = 0;
    case STATUS_ACTIVE = 1;
    case STATUS_EMAIL_SENT = 2;
}