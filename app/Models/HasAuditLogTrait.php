<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property Collection<AuditLog> $auditLogs
 */
trait HasAuditLogTrait
{
    /**
     * @return MorphMany<AuditLog>
     */
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'model');
    }
}
