@extends('emails.layout')

@section('content')
@if($isNewUser)
    <h1 class="heading-text" style="font-size: 28px; font-weight: 700; color: #1A1A1A; margin: 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Váš účet bol vytvorený</h1>
    <div style="height: 24px;"></div>
    <p class="heading-text" style="font-size: 14px; font-weight: 600; color: #1A1A1A; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Dobrý deň{{ $user->name ? ', ' . $user->first_name : '' }},</p>
    <div style="height: 16px;"></div>
    <p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Váš účet bol vytvorený na základe registrácie na <strong>{{ $registrationType }}</strong>: <strong>{{ $registrationTitle }}</strong>.</p>
    <div style="height: 16px;"></div>
    <p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Po prihlásení si doplňte profil — nastavte si heslo, pridajte osobné údaje a nahrajte profilovú fotku.</p>
    <div style="height: 24px;"></div>

    {{-- Feature box --}}
    <div class="info-box" style="background-color: #F5F5F5; border-radius: 12px; padding: 20px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <p class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; margin-bottom: 10px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Čo vám aplikácia ponúka:</p>
        <p class="body-text" style="font-size: 13px; color: #555555; line-height: 2; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
            &#128170; Prehľad tréningov a registrácia na termíny<br>
            &#128179; Platby za tréningy a členské poplatky<br>
            &#127942; Podujatia, súťaže a tímové aktivity<br>
            &#128100; Správa profilu a členstva v tímoch
        </p>
    </div>

    <div style="height: 24px;"></div>
    <a href="{{ $magicUrl ?: url('/login') }}" style="display: block; text-align: center; background-color: #FF2D2D; color: #FFFFFF; font-size: 14px; font-weight: 700; padding: 14px 24px; border-radius: 12px; text-decoration: none; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Prihlásiť sa a dokončiť profil</a>
    <div style="height: 12px;"></div>
    <p style="font-size: 12px; color: #999999; text-align: center; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Tento odkaz je platný 7 dní.</p>
@else
    <h1 class="heading-text" style="font-size: 28px; font-weight: 700; color: #1A1A1A; margin: 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Potvrdenie registrácie</h1>
    <div style="height: 24px;"></div>
    <p class="heading-text" style="font-size: 14px; font-weight: 600; color: #1A1A1A; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Dobrý deň{{ $user->name ? ', ' . $user->first_name : '' }},</p>
    <div style="height: 16px;"></div>
    <p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Vaša registrácia na <strong>{{ $registrationType }}</strong>: <strong>{{ $registrationTitle }}</strong> bola úspešne prijatá.</p>
    <div style="height: 16px;"></div>
    <p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Prihláste sa do aplikácie pre zobrazenie detailov a správu vašich registrácií.</p>
    <div style="height: 24px;"></div>
    <a href="{{ url('/login') }}" style="display: block; text-align: center; background-color: #FF2D2D; color: #FFFFFF; font-size: 14px; font-weight: 700; padding: 14px 24px; border-radius: 12px; text-decoration: none; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Prihlásiť sa</a>
@endif

<div style="height: 32px;"></div>
<div class="divider-line" style="height: 1px; background-color: #E5E5E5; margin: 0;"></div>
<div style="height: 24px;"></div>
<p style="font-size: 14px; font-weight: 500; color: #999999; line-height: 1.5; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Ďakujeme,<br>BCZ App</p>
@endsection
