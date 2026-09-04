@extends('emails.layout')

@section('content')
<h1 class="heading-text" style="font-size: 28px; font-weight: 700; color: #1A1A1A; margin: 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Vitaj v BCZ App, {{ $user->first_name ?? $user->name }}!</h1>
<div style="height: 24px;"></div>

<p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    Bol ti vytvorený účet v systéme BCZ Club. Cez odkaz nižšie sa môžeš prvýkrát prihlásiť a nastaviť si heslo.
</p>

<div style="height: 32px;"></div>

<table role="presentation" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
    <tr>
        <td style="border-radius: 10px; background-color: #FF3B30;">
            <a href="{{ $magicUrl }}" class="button"
               style="display: inline-block; padding: 14px 32px; color: #FFFFFF; text-decoration: none; font-weight: 700; font-size: 14px; letter-spacing: 0.5px; text-transform: uppercase; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                Prihlásiť sa a nastaviť heslo
            </a>
        </td>
    </tr>
</table>

<div style="height: 24px;"></div>

<p class="body-text" style="font-size: 13px; color: #999999; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    Odkaz je platný 7 dní. Ak tlačidlo nefunguje, skopíruj túto adresu do prehliadača:<br>
    <span style="color: #555555; word-break: break-all;">{{ $magicUrl }}</span>
</p>

@if(!empty($membershipPayment))
    @php
        $membership = $membershipPayment->payable;
        $payoutIban = $membership?->getPayoutIban();
        $payoutName = $membership?->getPayoutRecipientName();
        $paymentNote = $membership?->getQrPaymentNote();
    @endphp

    <div style="height: 32px;"></div>
    <div class="divider-line" style="height: 1px; background-color: #E5E5E5;"></div>
    <div style="height: 24px;"></div>

    <h2 class="heading-text" style="font-size: 20px; font-weight: 700; color: #1A1A1A; margin: 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Členské v klube</h2>

    <div style="height: 12px;"></div>

    <p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        Tréning, na ktorý si sa prihlásil, je pre členov klubu. Členské môžeš uhradiť hneď — naskenuj QR kód v bankovej aplikácii alebo použi údaje vedľa neho.
    </p>

    <div style="height: 20px;"></div>

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            @if($qrCodeImage)
                <td valign="top" width="132" style="padding-right: 16px;">
                    <div style="width: 116px; height: 116px; background-color: #FFFFFF; border: 1px solid #E5E5E5; border-radius: 12px; padding: 8px;">
                        <img src="{{ $message->embedData($qrCodeImage, 'qr-clenske.png', 'image/png') }}" alt="QR platba" width="100" height="100" style="display: block; width: 100%; height: auto;" />
                    </div>
                </td>
            @endif
            <td valign="top">
                <div class="info-box" style="background-color: #F5F5F5; border-radius: 12px; padding: 20px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                    <p class="heading-text" style="font-size: 14px; font-weight: 600; color: #1A1A1A; margin: 0 0 12px 0; font-family: 'DM Sans', sans-serif;">Detail platby</p>
                    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                        @if($membership?->season?->name)
                            <tr>
                                <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', sans-serif;">Sezóna</td>
                                <td class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; padding: 4px 0; text-align: right; font-family: 'DM Sans', sans-serif;">{{ $membership->season->name }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', sans-serif;">Suma</td>
                            <td style="font-size: 13px; font-weight: 700; color: #FF2D2D; padding: 4px 0; text-align: right; font-family: 'DM Sans', sans-serif;">{{ number_format((float) $membershipPayment->amount, 2, ',', ' ') }} {{ $membershipPayment->currency }}</td>
                        </tr>
                        @if($payoutIban)
                            <tr>
                                <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', sans-serif;">IBAN</td>
                                <td class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; padding: 4px 0; text-align: right; font-family: 'DM Sans', sans-serif;">{{ $payoutIban }}</td>
                            </tr>
                        @endif
                        @if($membershipPayment->formattedVariableSymbol())
                            <tr>
                                <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', sans-serif;">Variabilný symbol</td>
                                <td style="font-size: 13px; font-weight: 700; color: #FF2D2D; padding: 4px 0; text-align: right; font-family: 'DM Sans', sans-serif;">{{ $membershipPayment->formattedVariableSymbol() }}</td>
                            </tr>
                        @endif
                        @if($payoutName)
                            <tr>
                                <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', sans-serif;">Príjemca</td>
                                <td class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; padding: 4px 0; text-align: right; font-family: 'DM Sans', sans-serif;">{{ $payoutName }}</td>
                            </tr>
                        @endif
                        @if($paymentNote)
                            <tr>
                                <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', sans-serif;">Poznámka</td>
                                <td class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; padding: 4px 0; text-align: right; font-family: 'DM Sans', sans-serif;">{{ $paymentNote }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </td>
        </tr>
    </table>

    @if($membershipPaymentUrl)
        <div style="height: 20px;"></div>

        <a href="{{ $membershipPaymentUrl }}" target="_blank" class="btn-cta" style="display: block; text-align: center; background-color: #FF2D2D; color: #FFFFFF !important; font-size: 14px; font-weight: 700; padding: 14px 24px; border-radius: 12px; text-decoration: none; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Zaplatiť členské</a>
    @endif
@endif
@endsection
