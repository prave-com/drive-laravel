<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPERADMIN = '0';
    case ADMIN = '1';
    case USER = '2';
}
