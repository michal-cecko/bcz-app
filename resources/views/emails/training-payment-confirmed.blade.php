@extends('emails.layout')

@section('content')
    <h1 class="heading-text" style="font-size: 28px; font-weight: 700; color: #1A1A1A; margin: 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Platba potvrdená</h1>

    <div style="height: 24px;"></div>

    <p class="heading-text" style="font-size: 14px; font-weight: 600; color: #1A1A1A; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Dobrý deň, {{ $user->first_name ?? $user->name }},</p>

    <div style="height: 16px;"></div>

    <p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Vaša platba za tréning <strong>{{ $trainingTitle }}</strong> bola úspešne prijatá a spracovaná.</p>

    <div style="height: 20px;"></div>

    <div style="background-color: #F0FDF4; border: 1px solid rgba(34, 197, 94, 0.25); border-radius: 12px; padding: 16px;">
        <p style="font-size: 14px; font-weight: 600; color: #166534; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0 0 8px 0;">Platba prijatá</p>
        <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', sans-serif;">Tréning</td>
                <td class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; padding: 4px 0; text-align: right; font-family: 'DM Sans', sans-serif;">{{ $trainingTitle }}</td>
            </tr>
        </table>
    </div>

    <div style="height: 28px;"></div>

    <a href="{{ url('/') }}" target="_blank" class="btn-cta" style="display: block; text-align: center; background-color: #FF2D2D; color: #FFFFFF !important; font-size: 14px; font-weight: 700; padding: 14px 24px; border-radius: 12px; text-decoration: none; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Zobraziť detail tréningu</a>

    <div style="height: 32px;"></div>

    <div class="divider-line" style="height: 1px; background-color: #E5E5E5;"></div>

    <div style="height: 24px;"></div>

    <p style="font-size: 14px; font-weight: 500; color: #999999; line-height: 1.5; font-family: 'DM Sans', sans-serif;">Ďakujeme,<br>BCZ Club</p>
@endsection
