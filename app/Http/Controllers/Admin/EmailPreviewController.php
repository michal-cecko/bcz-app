<?php

namespace App\Http\Controllers\Admin;

use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EmailPreviewController
{
    public function show(string $key): \Illuminate\Http\Response
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

        $emailHtml = view('emails.admin-email', [
            'emailSubject' => $subject,
            'emailBody' => $emailBody,
            'teamName' => $teamName,
            'teamLogoUrl' => $teamLogoUrl,
            'teamUrl' => $teamUrl,
            'teamEmail' => $teamEmail,
            'teamPhone' => $teamPhone,
            'teamWebsite' => $teamWebsite,
        ])->render();

        return response(view('emails.preview', [
            'subject' => $subject,
            'emailHtml' => $emailHtml,
        ])->render());
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $key = \Illuminate\Support\Str::random(32);

        Cache::put("email-preview:{$key}", [
            'subject' => $request->input('subject', ''),
            'content' => $request->input('content', []),
            'team_name' => $request->input('team_name'),
            'team_logo_url' => $request->input('team_logo_url'),
            'team_url' => $request->input('team_url', '#'),
            'team_email' => $request->input('team_email'),
            'team_phone' => $request->input('team_phone'),
            'team_website' => $request->input('team_website'),
        ], now()->addMinutes(30));

        return response()->json([
            'url' => route('admin.email-preview', $key),
        ]);
    }
}
