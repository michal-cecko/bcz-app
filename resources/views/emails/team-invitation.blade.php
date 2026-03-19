@extends('emails.layout')

@section('content')
<h1 class="heading-text" style="font-size: 28px; font-weight: 700; color: #1A1A1A; margin: 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Pozvánka do tímu</h1>
<div style="height: 24px;"></div>
<p class="heading-text" style="font-size: 14px; font-weight: 600; color: #1A1A1A; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Dobrý deň,</p>
<div style="height: 16px;"></div>
<p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Boli ste pozvaný/á do tímu <strong>{{ $invitation->team->getTranslation('name', 'sk') }}</strong> v aplikácii BCZ Club.</p>
<div style="height: 16px;"></div>
<p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Ak ešte nemáte vytvorený účet, bude pre vás automaticky vytvorený. Po prihlásení si nastavte heslo a doplňte profil.</p>
<div style="height: 24px;"></div>
<a href="{{ $acceptUrl }}" class="btn-cta" style="display: block; text-align: center; background-color: #FF2D2D; color: #FFFFFF; font-size: 14px; font-weight: 700; padding: 14px 24px; border-radius: 12px; text-decoration: none; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Prijať pozvánku</a>
<div style="height: 12px;"></div>
<p style="font-size: 12px; color: #999999; text-align: center; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Tento odkaz je platný do {{ $invitation->expires_at->format('d.m.Y') }}.</p>
<div style="height: 32px;"></div>
<div class="divider-line" style="height: 1px; background-color: #E5E5E5; margin: 0;"></div>
<div style="height: 24px;"></div>
<p style="font-size: 14px; font-weight: 500; color: #999999; line-height: 1.5; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Ďakujeme,<br>BCZ App</p>
@endsection
