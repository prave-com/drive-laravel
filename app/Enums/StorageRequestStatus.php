<?php

namespace App\Enums;

enum StorageRequestStatus: string
{
    case PENDING = '0';
    case APPROVED = '1';
    case REJECTED = '2';
}
