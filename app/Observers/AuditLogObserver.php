<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\AuditLogAction;
use App\Models\AuditLog;
use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

final class AuditLogObserver
{
    public function __construct(
        private Request $httpRequest,
    ) {}

    private function handle(Model $model, AuditLogAction $action): void
    {
        $log = new AuditLog;
        $log->model()->associate($model);

        $log->action = $action;

        if (PHP_SAPI === 'cli') {
            // find the name of the currently running Artisan command...
            // In actual scenario, this is probably super slow and not worth it ?
            try {
                $consoleKernel = Container::getInstance()->get(Kernel::class);

                $consoleApplication = (new \ReflectionClass($consoleKernel))
                    ->getProperty('artisan') // property is protected without getter
                    ->getValue($consoleKernel);

                $artisanCommandInstance = (new \ReflectionClass($consoleApplication))
                    ->getParentClass() // Symfony Command
                    ->getProperty('runningCommand') // property is private without getter
                    ->getValue($consoleApplication);

                $commandName = class_basename($artisanCommandInstance);
            } catch (\Throwable) {
                $commandName = '{unknown}';
            }

            // get the full actual CLI command entered in the terminal, which gives the options and arguments
            $args = implode(' ', $_SERVER['argv'] ?? []);

            $log->context = 'cli: '.$commandName." ($args)";

            // could also get the name of the current job, if possible (probably need the same shenanigans as for the Artisan command name)
        } else { // assume web
            $log->user()->associate($this->httpRequest->user());
            $log->context = 'http:'.$this->httpRequest->getUri();
        }

        $data = [];
        if ($action === AuditLogAction::CREATE) {
            $data['after'] = $model->toArray();
        } elseif ($action === AuditLogAction::DELETE) {
            $data['before'] = $model->toArray();
        } elseif ($action === AuditLogAction::UPDATE) {
            // keys are the changed attributes
            // values are the current values
            $data['after'] = $model->getChanges();

            $data['before'] = [];
            foreach (array_keys($data['after']) as $changedAttribute) {
                $data['before'][$changedAttribute] = $model->getOriginal($changedAttribute);
            }
        }

        $hiddenAttributes = $model->getHidden();
        foreach ($hiddenAttributes as $attribute) {
            if (isset($data['before'][$attribute])) {
                $data['before'][$attribute] = '{hidden}';
            }
            if (isset($data['after'][$attribute])) {
                $data['after'][$attribute] = '{hidden}';
            }
        }

        $log->data = $data;

        $log->save();
    }

    // --------------------------------------------------
    public function created(Model $model): void
    {
        $this->handle($model, AuditLogAction::CREATE);
    }

    public function updated(Model $model): void
    {
        $this->handle($model, AuditLogAction::UPDATE);
    }

    public function deleted(Model $model): void
    {
        $this->handle($model, AuditLogAction::DELETE);
    }
}
