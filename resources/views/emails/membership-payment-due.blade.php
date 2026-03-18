@extends('emails.layout')

@section('content')
    <h1 class="heading-text" style="font-size: 28px; font-weight: 700; color: #1A1A1A; margin: 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Platba za členstvo</h1>

    <div style="height: 24px;"></div>

    <p class="heading-text" style="font-size: 14px; font-weight: 600; color: #1A1A1A; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Dobrý deň, {{ $user->first_name ?? $user->name }},</p>

    <div style="height: 16px;"></div>

    <p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Bola vám vystavená platba za členstvo v klube. Nižšie nájdete detaily platby.</p>

    <div style="height: 20px;"></div>

    <div class="info-box" style="background-color: #F5F5F5; border-radius: 12px; padding: 20px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <p class="heading-text" style="font-size: 14px; font-weight: 600; color: #1A1A1A; margin: 0 0 12px 0; font-family: 'DM Sans', sans-serif;">Detail platby</p>
        <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', sans-serif;">Tím</td>
                <td class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; padding: 4px 0; text-align: right; font-family: 'DM Sans', sans-serif;">{{ $teamName }}</td>
            </tr>
            <tr>
                <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', sans-serif;">Sezóna</td>
                <td class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; padding: 4px 0; text-align: right; font-family: 'DM Sans', sans-serif;">{{ $seasonName }}</td>
            </tr>
            <tr>
                <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', sans-serif;">Suma</td>
                <td style="font-size: 13px; font-weight: 700; color: #FF2D2D; padding: 4px 0; text-align: right; font-family: 'DM Sans', sans-serif;">{{ $feeAmount }} {{ $feeCurrency }}</td>
            </tr>
            <tr>
                <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', sans-serif;">Splatnosť</td>
                <td style="font-size: 13px; font-weight: 700; color: #FF2D2D; padding: 4px 0; text-align: right; font-family: 'DM Sans', sans-serif;">{{ $paymentDeadline }}</td>
            </tr>
        </table>
    </div>

    <div style="height: 20px;"></div>

    <div style="background-color: #EFF6FF; border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 12px; padding: 16px;">
        <p style="font-size: 14px; color: #1E40AF; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0;">Váš tím umožňuje platbu v hotovosti. Ak preferujete tento spôsob, kontaktujte svojho trénera alebo správcu klubu.</p>
    </div>

    <div style="height: 28px;"></div>

    <a href="{{ url('/admin') }}" target="_blank" style="display: block; text-align: center; background-color: #FF2D2D; color: #FFFFFF !important; font-size: 14px; font-weight: 700; padding: 14px 24px; border-radius: 12px; text-decoration: none; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Prejsť na platbu</a>

    <div style="height: 16px;"></div>

    <p style="font-size: 12px; color: #999999; text-align: center; font-family: 'DM Sans', sans-serif;">Platbu uhraďte do termínu splatnosti.</p>

    <div style="height: 32px;"></div>

    <div class="divider-line" style="height: 1px; background-color: #E5E5E5;"></div>

    <div style="height: 24px;"></div>

    <p style="font-size: 14px; font-weight: 500; color: #999999; line-height: 1.5; font-family: 'DM Sans', sans-serif;">Ďakujeme,<br>BCZ App</p>
@endsection
