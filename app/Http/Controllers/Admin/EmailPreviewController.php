<?php

namespace App\Http\Controllers\Admin;

use App\Services\EmailService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class EmailPreviewController
{
    public function show(string $key): Response
    {
        $data = Cache::get("email-preview:{$key}");

        if (! $data) {
            abort(404, 'Náhľad vypršal alebo neexistuje.');
        }

        $brickContent = $data['content'] ?? [];
        $subject = $data['subject'] ?? 'Náhľad e-mailu';
        $teamName = $data['team_name'] ?? null;
        $teamLogoUrl = $data['team_logo_url'] ?? null;
        $teamUrl = $data['team_url'] ?? '#';
        $teamEmail = $data['team_email'] ?? null;
        $teamPhone = $data['team_phone'] ?? null;
        $teamWebsite = $data['team_website'] ?? null;

        $emailBody = EmailService::renderBricks($brickContent);
        $sampleVariables = EmailService::getSampleVariables();
        $emailBody = EmailService::replaceVariables($emailBody, $sampleVariables);
        $subject = EmailService::replaceVariables($subject, $sampleVariables);

        $emailHtml = view('emails.layout', [
            'emailSubject' => $subject,
            'emailBody' => $emailBody,
            'teamName' => $teamName,
            'teamLogoUrl' => $teamLogoUrl,
            'teamUrl' => $teamUrl,
            'teamEmail' => $teamEmail,
            'teamPhone' => $teamPhone,
            'teamWebsite' => $teamWebsite,
        ])->render();

        // Strip dark mode CSS so the JS toggle has full control.
        // Remove @media (prefers-color-scheme: dark) { ... } block (with nested braces).
        $emailHtml = preg_replace(
            '/@media\s*\(prefers-color-scheme:\s*dark\)\s*\{(?:[^{}]*\{[^{}]*\})*[^{}]*\}/s',
            '',
            $emailHtml
        );
        // Force color-scheme to light only to prevent browser auto-darkening.
        $emailHtml = str_replace('color-scheme: light dark', 'color-scheme: light only', $emailHtml);
        $emailHtml = str_replace('content="light dark"', 'content="light only"', $emailHtml);

        return response(view('emails.preview', [
            'subject' => $subject,
            'emailHtml' => $emailHtml,
        ])->render());
    }
}
