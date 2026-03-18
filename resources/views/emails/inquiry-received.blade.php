@extends('emails.layout')

@section('content')
<h1 class="heading-text" style="font-size: 28px; font-weight: 700; color: #1A1A1A; margin: 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Nový dopyt z webu</h1>
<div style="height: 24px;"></div>
<p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Na webovej stránke bol odoslaný nový kontaktný formulár.</p>
<div style="height: 24px;"></div>

{{-- Detail box --}}
<div class="info-box" style="background-color: #F5F5F5; border-radius: 12px; padding: 20px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <p class="heading-text" style="font-size: 14px; font-weight: 700; color: #1A1A1A; margin-bottom: 10px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Detail dopytu</p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        <tr>
            <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Meno:</td>
            <td class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; text-align: right; padding: 4px 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{{ $inquiry->name }}</td>
        </tr>
        <tr>
            <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Email:</td>
            <td class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; text-align: right; padding: 4px 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{{ $inquiry->email }}</td>
        </tr>
        @if($inquiry->phone)
            <tr>
                <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Telefón:</td>
                <td class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; text-align: right; padding: 4px 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{{ $inquiry->phone }}</td>
            </tr>
        @endif
        @if($inquiry->reason)
            <tr>
                <td class="body-text" style="font-size: 13px; color: #999999; padding: 4px 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Typ:</td>
                <td class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; text-align: right; padding: 4px 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{{ $inquiry->reason }}</td>
            </tr>
        @endif
    </table>
</div>

<div style="height: 24px;"></div>

{{-- Message box --}}
<div class="info-box" style="background-color: #F5F5F5; border-radius: 12px; padding: 20px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <p class="body-text" style="font-size: 13px; color: #999999; margin-bottom: 8px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Správa:</p>
    <p class="heading-text" style="font-size: 13px; color: #1A1A1A; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{{ $inquiry->message }}</p>
</div>

<div style="height: 24px;"></div>
<a href="mailto:{{ $inquiry->email }}" style="display: block; text-align: center; background-color: #FF2D2D; color: #FFFFFF; font-size: 14px; font-weight: 700; padding: 14px 24px; border-radius: 12px; text-decoration: none; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">Odpovedať na dopyt</a>
<div style="height: 32px;"></div>
<div class="divider-line" style="height: 1px; background-color: #E5E5E5; margin: 0;"></div>
<div style="height: 24px;"></div>
<p style="font-size: 14px; font-weight: 500; color: #999999; line-height: 1.5; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">BCZ App — Automatické oznámenie</p>
@endsection
