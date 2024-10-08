<?php

declare(strict_types=1);

use App\Jobs\RunSendPostPublishedCommandJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new RunSendPostPublishedCommandJob)->everyFiveMinutes();
