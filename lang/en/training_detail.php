<?php

return [
    'breadcrumb_home' => 'HOME',
    'breadcrumb_trainings' => 'TRAININGS',

    'about_label' => 'ABOUT TRAINING',
    'about_title' => "WHAT AWAITS YOU\nAT THE TRAINING?",

    'details_title' => 'TRAINING DETAILS',
    'detail_category' => 'Category',
    'detail_age_group' => 'Age group',
    'detail_gender' => 'Gender',
    'all_genders' => 'All genders',
    'detail_day' => 'Day',
    'detail_time' => 'Time',
    'detail_place' => 'Location',
    'detail_city' => 'City',
    'detail_price' => 'Price',
    'pricing_free' => 'Free',
    'pricing_membership' => 'Membership required',

    'capacity_label' => 'Current capacity',
    'capacity_spots' => 'spots',
    'capacity_full' => 'Full',
    'training_full_message' => 'No spots are currently available. Please try later or contact the coach.',
    'capacity_remaining' => 'Only :count spot remaining!|Only :count spots remaining!',

    'location_label' => 'LOCATION',
    'location_title' => 'WHERE TO FIND US',
    'location_address' => 'Address',
    'location_meeting_title' => 'Meeting point',
    'location_open_maps' => 'Open in Google Maps',

    'coach_label' => 'COACHING',
    'coach_title' => 'YOUR COACH',

    'gallery_label' => 'FROM TRAINING',
    'gallery_title' => 'GALLERY',
    'gallery_subtitle' => 'See what this training looks like in action',

    'form_label' => 'REGISTRATION',
    'form_title' => 'SIGN UP FOR TRAINING',
    'form_subtitle' => 'Fill in the form and we will get back to you with a confirmation',
    'form_name' => 'First name',
    'form_surname' => 'Last name',
    'form_email' => 'Email',
    'form_email_placeholder' => 'your@email.com',
    'form_phone' => 'Phone',
    'form_phone_placeholder' => '+421 XXX XXX XXX',
    'form_submit' => 'SUBMIT REGISTRATION',
    'form_submitting' => 'SUBMITTING...',
    'file_uploading' => 'Uploading file…',
    'file_upload_idle' => 'Drop a file here or <span class="filepond--label-action">browse</span>',
    'continuous_membership_label' => 'Auto-renew membership',
    'continuous_membership_help' => 'We will remind you to pay the membership fee before each new season starts. You can disable this anytime in your dashboard.',
    'form_consent' => 'By submitting you agree to the processing of personal data.',
    'form_success_title' => 'THANK YOU!',
    'form_success_message' => 'Your registration has been successfully submitted. We will contact you with a confirmation.',

    // Registration states
    'registration_not_yet_open' => 'Registrations are not yet open',
    'registration_closed' => 'Registrations are closed',
    'registration_opens_at' => 'Registration opens on :date',
    'registration_not_eligible_title' => 'Registration Unavailable',
    'registration_not_eligible_message' => 'As an administrator or coach you cannot register for trainings. Registration is for athletes and customers.',

    'already_registered_title' => 'Already registered',
    'already_registered_message' => 'You are already registered for this training.',

    'free_approved_message' => 'Your registration has been automatically approved. We look forward to seeing you!',
    'membership_valid_message' => 'Your membership is valid and registration has been approved. We look forward to seeing you!',

    'membership_needed_title' => 'Membership required',
    'membership_needed_message' => 'This training requires an active team membership. Select a payment method to continue.',

    'payment_needed_title' => 'Training payment',
    'payment_needed_message' => 'Your registration has been received. Payment of :price is required for approval.',

    // State labels
    'state_registered' => 'STATUS: SUCCESSFULLY REGISTERED',
    'state_payment_success' => 'STATUS: PAYMENT SUCCESSFUL',
    'state_membership_needed' => 'STATUS: NO ACTIVE MEMBERSHIP',
    'state_payment_needed' => 'STATUS: PAYMENT PENDING',

    'payment_success_title' => 'Payment received!',
    'payment_success_message' => 'Your payment for the training has been successfully processed. Registration is confirmed, see you at the training!',
    'payment_confirmation_email' => 'Payment confirmation has been sent to your email.',

    'dr_training' => 'Training',
    'dr_date' => 'Date',
    'dr_location' => 'Location',
    'dr_amount' => 'Amount',
    'dr_payment_method' => 'Payment method',
    'dr_membership' => 'Membership',
    'membership_active' => 'Active',
    'membership_not_required' => 'Not required',

    // Payment methods
    'payment_method_label' => 'Payment method',
    'payment_gopay' => 'Card payment',
    'payment_gopay_desc' => 'Instant payment via GoPay',
    'payment_bank_transfer' => 'Bank transfer',
    'payment_bank_transfer_desc' => 'QR code and IBAN after selection',
    'payment_cash' => 'Cash',
    'payment_cash_desc' => 'Pay on site or contact the team',
    'payment_cash_instructions' => 'Cash payment can be made on site or contact the team for more information.',

    // Bank transfer details
    'bank_payment_details' => 'Payment details',
    'bank_iban' => 'IBAN:',
    'bank_variable_symbol' => 'Variable symbol:',
    'bank_amount' => 'Amount:',
    'bank_recipient' => 'Recipient:',
    'bank_message' => 'Note:',
    'bank_message_value' => 'Membership :season',
    'bank_instructions_title' => 'Payment instructions',
    'bank_instruction_1' => '1. Scan the QR code or enter the details in your internet banking',
    'bank_instruction_2' => '2. Enter the correct variable symbol for payment identification',
    'bank_instruction_3' => '3. Membership will be activated after the payment is credited (1–3 business days)',
    'bank_instruction_3_registration' => '3. Registration will be confirmed after the payment is credited (1–3 business days)',
    'bank_scan_qr' => 'Scan QR',

    // Cash payment details
    'cash_instructions_title' => 'Cash payment instructions',
    'cash_amount_label' => 'Amount to pay:',
    'cash_step_1' => 'Come to the nearest training with cash',
    'cash_step_2' => 'Hand the payment to the coach before or after training',
    'cash_step_3' => 'The coach will confirm receipt and membership will be activated',
    'cash_step_1_registration' => 'Prepare the cash in the given amount',
    'cash_step_2_registration' => 'Hand the payment to the organizer on site',
    'cash_step_3_registration' => 'Your registration will be confirmed after payment is received',
    'cash_warning' => 'Membership will be active only after payment is confirmed by the coach.',
    'cash_warning_registration' => 'Registration will be confirmed only after payment is received.',

    // Payment buttons
    'pay_button' => 'Pay :price',
    'show_payment_details' => 'Show payment details',
    'contact_team' => 'Contact team',
    'payment_auto_approve_note' => 'After payment you will be automatically registered.',

    // Season info
    'season_remaining' => 'for the rest of the season',
    'season_prorated_note' => 'Prorated fee for the remaining months of the current season',

    // Validation errors
    'error_email_exists' => 'An account with this email already exists. <a href="/login" class="underline text-bcz-red hover:text-red-400">Log in</a>.',
    'error_phone_exists' => 'This phone number is already assigned to another account.',

    'days' => [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ],
];
