@extends('emails.layout')

@php
    $greetingName = $user?->first_name ? ', '.$user->first_name : '';
    $kindLabel = __('emails.registration_confirmation.type.'.$registrationKind);
@endphp

@section('content')
<h1 class="heading-text" style="font-size: 28px; font-weight: 700; color: #1A1A1A; margin: 0; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{{ __('emails.registration_confirmation.heading') }}</h1>
<div style="height: 24px;"></div>
<p class="heading-text" style="font-size: 14px; font-weight: 600; color: #1A1A1A; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{{ __('emails.registration_confirmation.greeting', ['name' => $greetingName]) }}</p>
<div style="height: 16px;"></div>
<p class="body-text" style="font-size: 14px; color: #555555; line-height: 1.7; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{!! __('emails.registration_confirmation.body', ['type' => '<strong>'.$kindLabel.'</strong>', 'title' => '<strong>'.e($registrationTitle).'</strong>']) !!}</p>

@if(!empty($paymentAmount) && !empty($paymentUrl))
<div style="height: 24px;"></div>
<div class="divider-line" style="height: 1px; background-color: #E5E5E5; margin: 0;"></div>
<div style="height: 24px;"></div>

<div class="info-box" style="background-color: #F5F5F5; border-radius: 12px; padding: 24px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <p class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; margin-bottom: 6px;">{{ __('emails.registration_confirmation.payment_heading') }}</p>
    <p class="body-text" style="font-size: 13px; color: #555555; line-height: 1.6; margin-bottom: 16px;">{{ __('emails.registration_confirmation.payment_body') }}</p>
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 16px;">
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #E0E0E0;">
                <span class="body-text" style="font-size: 13px; color: #888888; font-family: 'DM Sans', sans-serif;">{{ __('emails.registration_confirmation.payment_amount_label') }}</span>
            </td>
            <td align="right" style="padding: 8px 0; border-bottom: 1px solid #E0E0E0;">
                <span class="info-box-amount" style="font-size: 18px; font-weight: 700; color: #22C55E; font-family: 'DM Sans', sans-serif;">{{ $paymentAmount }}</span>
            </td>
        </tr>
    </table>
    <a href="{{ $paymentUrl }}" class="btn-cta" style="display: block; text-align: center; background-color: #FF2D2D; color: #FFFFFF; font-size: 14px; font-weight: 700; padding: 14px 24px; border-radius: 12px; text-decoration: none; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{{ __('emails.registration_confirmation.payment_cta', ['amount' => $paymentAmount]) }}</a>
    <div style="height: 8px;"></div>
    <p style="font-size: 11px; color: #999999; text-align: center; font-family: 'DM Sans', sans-serif;">{{ __('emails.registration_confirmation.payment_disclaimer') }}</p>
</div>
@endif

@if(!empty($customContent))
<div style="height: 24px;"></div>
<div class="divider-line" style="height: 1px; background-color: #E5E5E5; margin: 0;"></div>
<div style="height: 24px;"></div>
{!! $customContent !!}
@endif

@if($isNewUser)
<div style="height: 32px;"></div>
<div class="divider-line" style="height: 1px; background-color: #E5E5E5; margin: 0;"></div>
<div style="height: 24px;"></div>
<div class="info-box" style="background-color: #F5F5F5; border-radius: 12px; padding: 20px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <p class="heading-text" style="font-size: 13px; font-weight: 600; color: #1A1A1A; margin-bottom: 8px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{{ __('emails.registration_confirmation.new_user_heading') }}</p>
    <p class="body-text" style="font-size: 13px; color: #555555; line-height: 1.6; margin-bottom: 16px; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{{ __('emails.registration_confirmation.new_user_body') }}</p>
    <a href="{{ $magicUrl ?: url('/login') }}" class="btn-cta" style="display: block; text-align: center; background-color: #FF2D2D; color: #FFFFFF; font-size: 14px; font-weight: 700; padding: 14px 24px; border-radius: 12px; text-decoration: none; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{{ __('emails.registration_confirmation.new_user_cta') }}</a>
    <div style="height: 8px;"></div>
    <p style="font-size: 11px; color: #999999; text-align: center; font-family: 'DM Sans', sans-serif;">{{ __('emails.registration_confirmation.new_user_link_validity') }}</p>
</div>
@endif

<div style="height: 32px;"></div>
<div class="divider-line" style="height: 1px; background-color: #E5E5E5; margin: 0;"></div>
<div style="height: 24px;"></div>
<p style="font-size: 14px; font-weight: 500; color: #999999; line-height: 1.5; font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">{{ __('emails.registration_confirmation.signoff') }}<br>{{ __('emails.registration_confirmation.signature') }}</p>
@endsection
