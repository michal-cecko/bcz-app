<?php

namespace App\Filament\Support;

use App\Services\EmailService;
use Illuminate\Support\HtmlString;

class PaymentNotePreview
{
    /**
     * Helper text for a "payment note" field: the variables it accepts, a live
     * preview rendered with sample values, and a warning when the template uses
     * no variables at all.
     *
     * Prose typed where a template was expected — "clensky prispevok - meno a
     * priezvisko" — reaches the bank app verbatim, and nothing in the form
     * surfaced that until someone scanned a real QR code.
     *
     * @param  array<string, string>  $sampleVariables
     */
    public static function helperText(?string $template, array $sampleVariables): HtmlString
    {
        $available = collect(array_keys($sampleVariables))
            ->map(fn (string $name): string => '{{'.$name.'}}')
            ->implode(', ');

        $lines = ['Dostupné premenné: '.e($available).'. Max 140 znakov (Pay by Square) / 60 znakov (QR Platba).'];

        $template = trim((string) $template);

        if ($template !== '') {
            $lines[] = '<strong>Náhľad:</strong> '.e(EmailService::renderPaymentNote($template, $sampleVariables) ?? '—');

            if (! static::usesAnyVariable($template, array_keys($sampleVariables))) {
                $lines[] = '<span class="text-warning-600 dark:text-warning-400">Poznámka neobsahuje žiadnu premennú — do QR kódu sa prenesie presne tento text.</span>';
            }
        }

        return new HtmlString(implode('<br>', $lines));
    }

    /**
     * @param  list<string>  $names
     */
    protected static function usesAnyVariable(string $template, array $names): bool
    {
        foreach ($names as $name) {
            if (str_contains($template, '{{'.$name.'}}')) {
                return true;
            }
        }

        return false;
    }
}
