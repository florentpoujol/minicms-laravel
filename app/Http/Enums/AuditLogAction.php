<?php

declare(strict_types=1);

namespace App\Http\Enums;

enum AuditLogAction: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
}
