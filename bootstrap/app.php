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
