<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case ANONYMOUS = 'anonymous';
    case REGULAR = 'regular';
    case WRITER = 'writer';
    case ADMIN = 'admin';
}
