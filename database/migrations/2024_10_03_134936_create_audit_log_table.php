<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();

            // can't use the *morphs* methods here because all assumes that the *_type column is a varchar
            $table->unsignedSmallInteger('model_type'); // 2 bytes, allow for 65535 different models
            $table->unsignedBigInteger('model_id');

            $table->foreignIdFor(User::class)
                ->nullable()
                ->constrained('users');

            $table->json('data');

            $table->timestamp('created_at', 2)->useCurrent(); // use 2 more bytes (so 6), so that it can be precise to the millisecond

            // this single index allow both to search logs by model + date and to search the model associated to one particular log
            $table->index(['model_id', 'model_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
