<?php

return [
    'reset_password' => [
        'subject' => 'Reset Password',
        'greeting' => 'Hi :name,',
        'intro' => 'You are receiving this email because we received a password reset request for your account.',
        'cta' => 'Reset Password',
        'expire' => 'This password reset link will expire in :count minutes.',
        'fallback' => "If the button doesn't work, paste this URL into your browser:",
        'ignore' => 'If you did not request a password reset, no further action is required.',
    ],

    'registration_confirmation' => [
        'subject' => 'Registration confirmed — :title',
        'heading' => 'Thank you for registering',
        'greeting' => 'Hello:name,',
        'body' => 'thank you for registering for the :type :title. We have successfully received your registration.',
        'type' => [
            'training' => 'training',
            'event' => 'event',
        ],
        'payment_heading' => 'Registration payment',
        'payment_body' => 'To complete your registration, please pay the amount below:',
        'payment_amount_label' => 'Amount due',
        'payment_cta' => 'Pay :amount',
        'payment_disclaimer' => 'Clicking the button will redirect you to a secure payment page.',
        'new_user_heading' => 'By the way — we created an account for you',
        'new_user_body' => 'So you can keep track of your registrations, payments and activities, we created an account for you. After signing in you can complete your profile and set a password.',
        'new_user_cta' => 'Sign in',
        'new_user_link_validity' => 'This link is valid for 7 days.',
        'signoff' => 'Thank you,',
        'signature' => 'BCZ Club',
    ],
];
