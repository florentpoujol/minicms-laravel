<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

final readonly class BlogController
{
    public function __construct(
        private Factory $viewFactory
    ) {}

    /**
     * Route: GET /
     */
    public function blog(): View
    {
        return $this->viewFactory->make('blog');
    }
}
