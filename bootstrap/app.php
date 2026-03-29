<?php

use App\Http\Middleware\SetLocale;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
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
    })
    ->withExceptions(function (Exceptions $exceptions): void {
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
