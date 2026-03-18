@extends('emails.layout')

@section('content')
    <h1 class="heading-text" style="font-size: 28px; font-weight: 700; color: #1A1A1A; margin: 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Uvoľnilo sa miesto!</h1>

    <div style="height: 24px;"></div>

    <p class="heading-text" style="font-size: 14px; font-weight: 600; color: #1A1A1A; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Dobrý deň, {{ $user->first_name ?? $user->name }},</p>

    <div style="height: 16px;"></div>

    <p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Na tréningu <strong>{{ $trainingTitle }}</strong> sa uvoľnilo miesto. Boli ste na čakacej listine a teraz máte možnosť sa zaregistrovať.</p>

    <div style="height: 20px;"></div>

    <div style="background-color: #EFF6FF; border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 12px; padding: 16px;">
        <p style="font-size: 14px; color: #1E40AF; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0;">Miesto je rezervované na obmedzený čas. Ak sa nezaregistrujete, bude ponúknuté ďalšiemu záujemcovi.</p>
    </div>

    <div style="height: 28px;"></div>

    <a href="{{ $trainingUrl }}" target="_blank" style="display: block; text-align: center; background-color: #FF2D2D; color: #FFFFFF !important; font-size: 14px; font-weight: 700; padding: 14px 24px; border-radius: 12px; text-decoration: none; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Zaregistrovať sa na tréning</a>

    <div style="height: 16px;"></div>

    <p style="font-size: 12px; color: #999999; text-align: center; font-family: 'DM Sans', sans-serif;">Rezervácia vyprší o 24 hodín.</p>

    <div style="height: 32px;"></div>

    <div class="divider-line" style="height: 1px; background-color: #E5E5E5;"></div>

    <div style="height: 24px;"></div>

    <p style="font-size: 14px; font-weight: 500; color: #999999; line-height: 1.5; font-family: 'DM Sans', sans-serif;">Ďakujeme,<br>BCZ App</p>
@endsection
