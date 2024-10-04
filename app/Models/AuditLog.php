<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AuditLogAction;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $model_id
 * @property int $model_type
 * @property null|int $user_id
 * @property AuditLogAction $action
 * @property string $context
 * @property array<string, array<string, mixed>> $data
 * @property Carbon $created_at
 * @property-read array<string, scalar> $before
 * @property-read array<string, scalar> $after
 * @property Model $model
 */
final class AuditLog extends Model
{
    const UPDATED_AT = null;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'model_id',
        'model_type',
        'user_id',
        'before',
        'after',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'action' => AuditLogAction::class,
            'data' => 'array',
        ];
    }

    /**
     * @return Attribute<array<string, mixed>, null>
     */
    protected function before(): Attribute
    {
        return new Attribute(
            get: fn (mixed $value, array $attributes): array => $attributes['data']['before'] ?? [],
        );
    }

    /**
     * @return Attribute<array<string, mixed>, null>
     */
    protected function after(): Attribute
    {
        return new Attribute(
            get: fn (mixed $value, array $attributes): array => $attributes['data']['after'] ?? [],
        );
    }

    /**
     * @return MorphTo<Model, self>
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<User, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
