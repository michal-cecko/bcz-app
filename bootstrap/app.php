<?php

use App\Http\Middleware\RedirectToHomePanel;
use App\Http\Middleware\SetLocale;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Sentry\Laravel\Integration;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileUnacceptableForCollection;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'gopay/notify',
        ]);
        $middleware->web(append: [
            SetLocale::class,
        ]);

        // Run the panel-routing redirect before authentication so a teamless
        // user on the admin panel is bounced to the customer panel instead of
        // being 403'd by canAccessPanel. Filament's Authenticate extends the
        // Illuminate one, so anchoring before that parent governs the sort slot.
        $middleware->prependToPriorityList(
            before: AuthenticatesRequests::class,
            prepend: RedirectToHomePanel::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);

        // Bot/scanner traffic occasionally POSTs a forged Livewire snapshot
        // update targeting an internal Filament-locked property (e.g. the
        // login page's `discoveredSchemaNames`). Livewire is correctly
        // rejecting the tampered request — this is not an app bug, so don't
        // let it spam Sentry. See filamentphp/filament#18949.
        $exceptions->dontReport(CannotUpdateLockedPropertyException::class);

        // A Filament `EditRecord` page's `DeleteAction` legitimately sets
        // `$this->record = null` after deletion (Filament\Resources\Pages\
        // Concerns\InteractsWithRecord::afterActionCalled()) so Livewire
        // doesn't try to dehydrate a model that no longer exists. If that
        // exact (checksum-valid) snapshot is replayed by a follow-up
        // Livewire update — a duplicate submit, a queued network retry, a
        // bfcache-resurrected tab — Livewire's own typed-property guard in
        // HandleComponents::hydrateProperties() deliberately skips
        // rehydrating `$record` back to `null`, leaving it *uninitialized*
        // instead. The next read of `$this->record` then throws a raw
        // `Error` rather than a catchable exception. This is Livewire
        // correctly bouncing a stale/replayed request, not an app bug — no
        // app code reads or writes `$record` directly.
        $exceptions->dontReportWhen(
            fn (Throwable $e) => $e instanceof Error
                && str_contains($e->getMessage(), '::$record must not be accessed before initialization')
        );

        $exceptions->renderable(function (FileUnacceptableForCollection $e) {
            preg_match('/mime: ([^,`]+)/', $e->getMessage(), $matches);
            $mime = $matches[1] ?? null;

            $typeLabel = match ($mime) {
                'application/pdf' => 'PDF',
                'application/zip' => 'ZIP',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word',
                'text/plain' => 'textový súbor',
                default => $mime ? strtoupper(str($mime)->afterLast('/')->toString()) : 'tento typ súboru',
            };

            Notification::make()
                ->danger()
                ->title('Nepovolený typ súboru')
                ->body("Súbor typu {$typeLabel} nie je možné nahrať. Skúste prosím iný formát.")
                ->send();

            return back();
        });
    })->create();
