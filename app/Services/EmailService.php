<?php

namespace App\Services;

use App\Mail\AdminEmail;
use App\Mason\EmailBricks\EmailButtonBrick;
use App\Mason\EmailBricks\EmailCalloutBrick;
use App\Mason\EmailBricks\EmailDividerBrick;
use App\Mason\EmailBricks\EmailHeadingBrick;
use App\Mason\EmailBricks\EmailImageBrick;
use App\Mason\EmailBricks\EmailRichTextBrick;
use App\Mason\EmailBricks\EmailSpacerBrick;
use App\Models\Team;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /** @var array<string, class-string> */
    protected static array $brickMap = [
        'email-rich-text' => EmailRichTextBrick::class,
        'email-button' => EmailButtonBrick::class,
        'email-divider' => EmailDividerBrick::class,
        'email-heading' => EmailHeadingBrick::class,
        'email-image' => EmailImageBrick::class,
        'email-callout' => EmailCalloutBrick::class,
        'email-spacer' => EmailSpacerBrick::class,
    ];

    /**
     * Render Mason brick content to HTML.
     *
     * @param  array<int, array<string, mixed>>  $brickContent
     */
    public static function renderBricks(array $brickContent): string
    {
        $html = [];

        foreach ($brickContent as $block) {
            if (($block['type'] ?? null) !== 'masonBrick') {
                continue;
            }

            $id = $block['attrs']['id'] ?? null;
            $config = $block['attrs']['config'] ?? [];

            if (blank($id) || ! isset(static::$brickMap[$id])) {
                continue;
            }

            $brickClass = static::$brickMap[$id];
            $rendered = $brickClass::toHtml($config);

            if ($rendered !== null && $rendered !== '') {
                $html[] = $rendered;
            }
        }

        return implode('', $html);
    }

    /**
     * Replace variable placeholders in a string.
     *
     * @param  array<string, string>  $variables
     */
    public static function replaceVariables(string $text, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $text = str_replace('{{'.$key.'}}', $value, $text);
        }

        return $text;
    }

    /**
     * Substitute variables into a QR payment note template and tidy the result.
     *
     * A placeholder that resolves to an empty string leaves the punctuation
     * around it behind, so "{{meno}} {{priezvisko}} - clensky prispevok" would
     * reach the bank app as " - clensky prispevok" for a registration with no
     * name on file. Collapse that back to just the readable part, and return
     * null when nothing but separators is left, so callers hide the note
     * entirely rather than showing a stray dash.
     *
     * @param  array<string, string>  $variables
     */
    public static function renderPaymentNote(string $template, array $variables): ?string
    {
        $note = static::replaceVariables($template, $variables);
        $note = preg_replace('/\s+/u', ' ', $note) ?? $note;
        $note = preg_replace('/(?:\s*[-–—,;:\/|]\s*){2,}/u', ' - ', $note) ?? $note;
        $note = preg_replace('/^[\s\-–—,;:\/|]+|[\s\-–—,;:\/|]+$/u', '', $note) ?? $note;

        return $note !== '' ? $note : null;
    }

    /**
     * Send emails to recipients.
     *
     * @param  array<int, array<string, mixed>>  $brickContent
     * @param  array<int, array{email: string, variables: array<string, string>}>  $recipients
     */
    public static function send(
        string $subject,
        array $brickContent,
        array $recipients,
        ?Team $team = null,
    ): int {
        $baseHtml = static::renderBricks($brickContent);

        $sentEmails = [];
        $count = 0;

        foreach ($recipients as $recipient) {
            $email = $recipient['email'];

            if (in_array($email, $sentEmails, true)) {
                continue;
            }

            $variables = $recipient['variables'] ?? [];
            $personalizedSubject = static::replaceVariables($subject, $variables);
            $personalizedHtml = static::replaceVariables($baseHtml, $variables);

            Mail::to($email)->queue(
                new AdminEmail($personalizedSubject, $personalizedHtml, $team),
            );

            $sentEmails[] = $email;
            $count++;
        }

        return $count;
    }

    /**
     * Get sample variables for preview.
     *
     * @return array<string, string>
     */
    public static function getSampleVariables(): array
    {
        return [
            'meno' => 'Jan Novak',
            'email' => 'jan@priklad.sk',
            'nazov_timu' => 'BCZ Team',
            'nazov_treningu' => 'Trening breakdance',
            'miesto' => 'Sportova hala',
            'cas' => '18:00',
            'kapacita' => '20',
            'nazov_eventu' => 'BCZ Battle',
            'datum_eventu' => '15.04.2026',
        ];
    }
}
