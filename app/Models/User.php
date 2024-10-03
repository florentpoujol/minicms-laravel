<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property null|string $remember_token
 * @property Carbon $email_verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection<Post> $posts
 * @property Collection<AuditLog> $auditLogsAsPerpetrator
 * @property Collection<AuditLog> $auditLogs
 */
final class User extends Authenticatable
{
    use HasAuditLogTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return HasMany<Post>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    /**
     * @return HasMany<AuditLog>
     */
    public function auditLogsAsPerpetrator(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
