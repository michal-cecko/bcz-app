@extends('emails.layout')

@section('content')
<h1 class="heading-text" style="font-size: 28px; font-weight: 700; color: #1A1A1A; margin: 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    {{ __('emails.reset_password.greeting', ['name' => $user->first_name ?? $user->name]) }}
</h1>
<div style="height: 24px;"></div>

<p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    {{ __('emails.reset_password.intro') }}
</p>

<div style="height: 32px;"></div>

<table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
    <tr>
        <td style="border-radius: 10px; background-color: #FF3B30;">
            <a href="{{ $resetUrl }}" class="button"
               style="display: inline-block; padding: 14px 32px; color: #FFFFFF; text-decoration: none; font-weight: 700; font-size: 14px; letter-spacing: 0.5px; text-transform: uppercase; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                {{ __('emails.reset_password.cta') }}
            </a>
        </td>
    </tr>
</table>

<div style="height: 24px;"></div>

<p class="body-text" style="font-size: 13px; color: #999999; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    {{ __('emails.reset_password.expire', ['count' => $expireMinutes]) }}<br>
    {{ __('emails.reset_password.fallback') }}<br>
    <span style="color: #555555; word-break: break-all;">{{ $resetUrl }}</span>
</p>

<div style="height: 16px;"></div>

<p class="body-text" style="font-size: 13px; color: #999999; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    {{ __('emails.reset_password.ignore') }}
</p>
@endsection
