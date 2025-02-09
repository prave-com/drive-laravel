<?php

namespace App\Enums;

enum PermissionType: string
{
    case READ = '0';
    case READ_WRITE = '1';
}
