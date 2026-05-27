<?php

use App\Enums\BannerTypeEnum;
use App\Enums\BillingPeriodEnum;
use App\Enums\CoachRoleEnum;
use App\Enums\ComplexityLevelEnum;
use App\Enums\DraftStatusEnum;
use App\Enums\EventPricingTypeEnum;
use App\Enums\EventTypeEnum;
use App\Enums\GenderEnum;
use App\Enums\GoalStatusEnum;
use App\Enums\InquiryReasonEnum;
use App\Enums\InquiryStatusEnum;
use App\Enums\InvitationStatusEnum;
use App\Enums\MembershipStatusEnum;
use App\Enums\MenuLocationEnum;
use App\Enums\PageStatusEnum;
use App\Enums\PairingStrategyEnum;
use App\Enums\PayableTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PayoutStatusEnum;
use App\Enums\PlanTierEnum;
use App\Enums\ProfileTypeEnum;
use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\RoleEnum;
use App\Enums\RoundAdvancementTypeEnum;
use App\Enums\ScoringFormatEnum;
use App\Enums\SettingTypeEnum;
use App\Enums\SponsorTagEnum;
use App\Enums\SportCategoryTypeEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\TeamJoinModeEnum;
use App\Enums\TimetableEntryStatusEnum;
use App\Enums\TrainingPricingTypeEnum;

return [
    'boolean' => [
        'yes' => 'Yes',
        'no' => 'No',
    ],
    BannerTypeEnum::class => [
        BannerTypeEnum::Topbar->value => 'Top Bar',
        BannerTypeEnum::Floating->value => 'Floating',
        BannerTypeEnum::Popup->value => 'Popup',
    ],
    InvitationStatusEnum::class => [
        InvitationStatusEnum::Pending->value => 'Pending',
        InvitationStatusEnum::Accepted->value => 'Accepted',
        InvitationStatusEnum::Declined->value => 'Declined',
        InvitationStatusEnum::Expired->value => 'Expired',
    ],
    CoachRoleEnum::class => [
        CoachRoleEnum::MAIN->value => 'Head Coach',
        CoachRoleEnum::SECONDARY->value => 'Assistant Coach',
    ],
    ComplexityLevelEnum::class => [
        ComplexityLevelEnum::BASIC->value => 'Basic',
        ComplexityLevelEnum::INTERMEDIATE->value => 'Intermediate',
        ComplexityLevelEnum::ADVANCED->value => 'Advanced',
        ComplexityLevelEnum::ELITE->value => 'Elite',
    ],
    GenderEnum::class => [
        GenderEnum::MALE->value => 'Male',
        GenderEnum::FEMALE->value => 'Female',
    ],
    GoalStatusEnum::class => [
        GoalStatusEnum::PLANNED->value => 'Planned',
        GoalStatusEnum::IN_PROGRESS->value => 'In Progress',
        GoalStatusEnum::ACTIVE->value => 'Active',
        GoalStatusEnum::COMPLETED->value => 'Completed',
    ],
    InquiryReasonEnum::class => [
        InquiryReasonEnum::TRAINING->value => 'Training',
        InquiryReasonEnum::EXHIBITION->value => 'Exhibition',
        InquiryReasonEnum::LECTURE->value => 'Lecture',
        InquiryReasonEnum::WORKSHOP->value => 'Workshop',
        InquiryReasonEnum::COMPETITION->value => 'Competition',
        InquiryReasonEnum::OTHER->value => 'Other',
    ],
    InquiryStatusEnum::class => [
        InquiryStatusEnum::NEW->value => 'New',
        InquiryStatusEnum::IN_PROGRESS->value => 'In Progress',
        InquiryStatusEnum::RESOLVED->value => 'Resolved',
    ],
    BillingPeriodEnum::class => [
        BillingPeriodEnum::MONTHLY->value => 'Monthly',
        BillingPeriodEnum::YEARLY->value => 'Yearly',
    ],
    PaymentStatusEnum::class => [
        PaymentStatusEnum::PENDING->value => 'Pending',
        PaymentStatusEnum::COMPLETED->value => 'Paid',
        PaymentStatusEnum::REFUNDED->value => 'Refunded',
        PaymentStatusEnum::CANCELLED->value => 'Cancelled',
    ],
    PairingStrategyEnum::class => [
        PairingStrategyEnum::RANDOM->value => 'Random',
        PairingStrategyEnum::SEEDED->value => 'Seeded (1 vs N, 2 vs N-1)',
    ],
    RoundAdvancementTypeEnum::class => [
        RoundAdvancementTypeEnum::TOP_BY_POINTS->value => 'Top by Points',
        RoundAdvancementTypeEnum::BATTLE_WINNER->value => 'Battle Winner',
    ],
    ScoringFormatEnum::class => [
        ScoringFormatEnum::POINTS->value => 'Points',
        ScoringFormatEnum::COACH_DECISION->value => 'Judge Decision',
    ],
    SettingTypeEnum::class => [
        SettingTypeEnum::TEXT->value => 'Text',
        SettingTypeEnum::NUMBER->value => 'Number',
        SettingTypeEnum::BOOLEAN->value => 'Yes/No',
        SettingTypeEnum::SELECT->value => 'Select',
        SettingTypeEnum::MULTI_SELECT->value => 'Multi Select',
        SettingTypeEnum::TEAM_SELECT->value => 'Team Select',
        SettingTypeEnum::DATE->value => 'Date',
    ],
    SponsorTagEnum::class => [
        SponsorTagEnum::MAIN_SPONSOR->value => 'Main Sponsor',
        SponsorTagEnum::MEDIAL_SPONSOR->value => 'Media Sponsor',
        SponsorTagEnum::PARTNER->value => 'Partner',
        SponsorTagEnum::SUPPORTER->value => 'Supporter',
    ],
    SportCategoryTypeEnum::class => [
        SportCategoryTypeEnum::CALISTHENICS->value => 'Calisthenics',
        SportCategoryTypeEnum::PARKOUR->value => 'Parkour',
    ],
    TimetableEntryStatusEnum::class => [
        TimetableEntryStatusEnum::PENDING->value => 'Pending',
        TimetableEntryStatusEnum::IN_PROGRESS->value => 'In Progress',
        TimetableEntryStatusEnum::FINISHED->value => 'Finished',
    ],
    TrainingPricingTypeEnum::class => [
        TrainingPricingTypeEnum::FREE->value => 'Free',
        TrainingPricingTypeEnum::PAID->value => 'Paid',
        TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED->value => 'Membership Required',
    ],
    RegistrationFieldTypeEnum::class => [
        RegistrationFieldTypeEnum::TEXT_INPUT->value => 'Text Input',
        RegistrationFieldTypeEnum::TEXTAREA->value => 'Text Area',
        RegistrationFieldTypeEnum::CHECKBOX->value => 'Checkbox',
        RegistrationFieldTypeEnum::SELECT->value => 'Select',
        RegistrationFieldTypeEnum::MULTI_SELECT->value => 'Multi Select',
        RegistrationFieldTypeEnum::DATE_PICKER->value => 'Date Picker',
        RegistrationFieldTypeEnum::YEAR_PICKER->value => 'Year Picker',
        RegistrationFieldTypeEnum::NUMBER_INPUT->value => 'Number Input',
        RegistrationFieldTypeEnum::TIME_PICKER->value => 'Time Picker',
        RegistrationFieldTypeEnum::PHONE->value => 'Phone',
        RegistrationFieldTypeEnum::EMAIL->value => 'Email',
        RegistrationFieldTypeEnum::FILE_INPUT->value => 'File',
        RegistrationFieldTypeEnum::FIRST_NAME->value => 'First Name',
        RegistrationFieldTypeEnum::LAST_NAME->value => 'Last Name',
        RegistrationFieldTypeEnum::FULL_NAME->value => 'Full Name',
        RegistrationFieldTypeEnum::BIRTH_DATE->value => 'Date of Birth',
        RegistrationFieldTypeEnum::GENDER->value => 'Gender',
        RegistrationFieldTypeEnum::CATEGORY->value => 'Category',
    ],
    RoleEnum::class => [
        RoleEnum::SUPER_ADMIN->value => 'Super Admin',
        RoleEnum::ADMIN->value => 'Admin',
        RoleEnum::TEAM_ADMIN->value => 'Team Admin',
        RoleEnum::COACH->value => 'Coach',
        RoleEnum::ATHLETE->value => 'Athlete',
        RoleEnum::EDITOR->value => 'Editor',
        RoleEnum::CUSTOMER->value => 'Customer',
    ],
    PaymentMethodEnum::class => [
        PaymentMethodEnum::BANK_TRANSFER->value => 'Bank Transfer',
        PaymentMethodEnum::CASH->value => 'Cash',
        PaymentMethodEnum::GOPAY->value => 'GoPay',
    ],
    MembershipStatusEnum::class => [
        MembershipStatusEnum::ACTIVE->value => 'Active',
        MembershipStatusEnum::COMPLETED->value => 'Completed',
        MembershipStatusEnum::CANCELLED->value => 'Cancelled',
        MembershipStatusEnum::PENDING->value => 'Pending',
    ],
    EventTypeEnum::class => [
        EventTypeEnum::Report->value => 'Report',
        EventTypeEnum::Organized->value => 'Organized',
        EventTypeEnum::Competition->value => 'Competition',
    ],
    EventPricingTypeEnum::class => [
        EventPricingTypeEnum::Free->value => 'Free',
        EventPricingTypeEnum::Paid->value => 'Paid',
    ],
    PayableTypeEnum::class => [
        PayableTypeEnum::MEMBERSHIP->value => 'Membership',
        PayableTypeEnum::TRAINING_REGISTRATION->value => 'Training Registration',
        PayableTypeEnum::COMPETITION_REGISTRATION->value => 'Competition Registration',
    ],
    SubscriptionStatusEnum::class => [
        SubscriptionStatusEnum::ACTIVE->value => 'Active',
        SubscriptionStatusEnum::TRIALING->value => 'Trial',
        SubscriptionStatusEnum::PAST_DUE->value => 'Past Due',
        SubscriptionStatusEnum::CANCELLED->value => 'Cancelled',
        SubscriptionStatusEnum::COMPLETED->value => 'Completed',
    ],
    PlanTierEnum::class => [
        PlanTierEnum::FREE->value => 'Free',
        PlanTierEnum::STARTER->value => 'Starter',
        PlanTierEnum::PRO->value => 'Pro',
        PlanTierEnum::ENTERPRISE->value => 'Enterprise',
    ],
    PayoutStatusEnum::class => [
        PayoutStatusEnum::PENDING->value => 'Pending',
        PayoutStatusEnum::PROCESSING->value => 'Processing',
        PayoutStatusEnum::COMPLETED->value => 'Sent',
        PayoutStatusEnum::FAILED->value => 'Failed',
    ],
    PageStatusEnum::class => [
        PageStatusEnum::Draft->value => 'Draft',
        PageStatusEnum::Published->value => 'Published',
        PageStatusEnum::Archived->value => 'Archived',
    ],
    RegistrationStatusEnum::class => [
        RegistrationStatusEnum::Pending->value => 'Pending',
        RegistrationStatusEnum::Approved->value => 'Approved',
        RegistrationStatusEnum::Rejected->value => 'Rejected',
        RegistrationStatusEnum::Cancelled->value => 'Cancelled',
    ],
    MenuLocationEnum::class => [
        MenuLocationEnum::Header->value => 'Header',
        MenuLocationEnum::FooterDiscover->value => 'Footer — Discover',
        MenuLocationEnum::FooterPrograms->value => 'Footer — Programs',
        MenuLocationEnum::FooterLegal->value => 'Footer — Legal',
    ],
    TeamJoinModeEnum::class => [
        TeamJoinModeEnum::APPROVAL->value => 'With Approval',
        TeamJoinModeEnum::OPEN->value => 'Open',
    ],
    DraftStatusEnum::class => [
        DraftStatusEnum::Pending->value => 'Pending Approval',
        DraftStatusEnum::Rejected->value => 'Rejected',
    ],
    ProfileTypeEnum::class => [
        ProfileTypeEnum::Coach->value => 'Coach',
        ProfileTypeEnum::Athlete->value => 'Athlete',
    ],
];
