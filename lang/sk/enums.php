<?php

use App\Enums\CoachRoleEnum;
use App\Enums\ComplexityLevelEnum;
use App\Enums\GenderEnum;
use App\Enums\GoalStatusEnum;
use App\Enums\InquiryReasonEnum;
use App\Enums\InquiryStatusEnum;
use App\Enums\InvitationStatusEnum;
use App\Enums\MembershipPeriodEnum;
use App\Enums\MembershipStatusEnum;
use App\Enums\PayableTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PayoutStatusEnum;
use App\Enums\PlanTierEnum;
use App\Enums\RegistrationFieldTypeEnum;
use App\Enums\RoleEnum;
use App\Enums\RoundAdvancementTypeEnum;
use App\Enums\ScoringFormatEnum;
use App\Enums\SettingTypeEnum;
use App\Enums\SponsorTagEnum;
use App\Enums\SportCategoryTypeEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\TimetableEntryStatusEnum;
use App\Enums\TrainingPricingTypeEnum;

return [
    InvitationStatusEnum::class => [
        InvitationStatusEnum::Pending->value => 'Čakajúca',
        InvitationStatusEnum::Accepted->value => 'Prijatá',
        InvitationStatusEnum::Declined->value => 'Odmietnutá',
        InvitationStatusEnum::Expired->value => 'Expirovaná',
    ],
    CoachRoleEnum::class => [
        CoachRoleEnum::MAIN->value => 'Hlavný tréner',
        CoachRoleEnum::SECONDARY->value => 'Asistent trénera',
    ],
    ComplexityLevelEnum::class => [
        ComplexityLevelEnum::BASIC->value => 'Základný',
        ComplexityLevelEnum::INTERMEDIATE->value => 'Stredný',
        ComplexityLevelEnum::ADVANCED->value => 'Pokročilý',
        ComplexityLevelEnum::ELITE->value => 'Elitný',
    ],
    GenderEnum::class => [
        GenderEnum::MALE->value => 'Muž',
        GenderEnum::FEMALE->value => 'Žena',
    ],
    GoalStatusEnum::class => [
        GoalStatusEnum::PLANNED->value => 'Plánovaný',
        GoalStatusEnum::IN_PROGRESS->value => 'Prebieha',
        GoalStatusEnum::ACTIVE->value => 'Aktívny',
        GoalStatusEnum::COMPLETED->value => 'Dokončený',
    ],
    InquiryReasonEnum::class => [
        InquiryReasonEnum::TRAINING->value => 'Tréning',
        InquiryReasonEnum::EXHIBITION->value => 'Vystúpenie',
        InquiryReasonEnum::LECTURE->value => 'Prednáška',
        InquiryReasonEnum::WORKSHOP->value => 'Workshop',
        InquiryReasonEnum::COMPETITION->value => 'Súťaž',
        InquiryReasonEnum::OTHER->value => 'Iné',
    ],
    InquiryStatusEnum::class => [
        InquiryStatusEnum::NEW->value => 'Nový',
        InquiryStatusEnum::IN_PROGRESS->value => 'Prebieha',
        InquiryStatusEnum::RESOLVED->value => 'Vyriešený',
    ],
    MembershipPeriodEnum::class => [
        MembershipPeriodEnum::MONTHLY->value => 'Mesačne',
        MembershipPeriodEnum::YEARLY->value => 'Ročne',
    ],
    PaymentStatusEnum::class => [
        PaymentStatusEnum::PENDING->value => 'Čaká na platbu',
        PaymentStatusEnum::COMPLETED->value => 'Zaplatené',
        PaymentStatusEnum::FAILED->value => 'Zlyhalo',
        PaymentStatusEnum::REFUNDED->value => 'Vrátené',
    ],
    RoundAdvancementTypeEnum::class => [
        RoundAdvancementTypeEnum::TOP_BY_POINTS->value => 'Najlepší podľa bodov',
        RoundAdvancementTypeEnum::BATTLE_WINNER->value => 'Víťaz battlu',
    ],
    ScoringFormatEnum::class => [
        ScoringFormatEnum::POINTS->value => 'Body',
        ScoringFormatEnum::COACH_DECISION->value => 'Rozhodnutie porotcu',
    ],
    SettingTypeEnum::class => [
        SettingTypeEnum::TEXT->value => 'Text',
        SettingTypeEnum::NUMBER->value => 'Číslo',
        SettingTypeEnum::BOOLEAN->value => 'Áno/Nie',
        SettingTypeEnum::SELECT->value => 'Výber',
        SettingTypeEnum::MULTI_SELECT->value => 'Viacnásobný výber',
        SettingTypeEnum::TEAM_SELECT->value => 'Výber tímu',
    ],
    SponsorTagEnum::class => [
        SponsorTagEnum::MAIN_SPONSOR->value => 'Hlavný sponzor',
        SponsorTagEnum::MEDIAL_SPONSOR->value => 'Mediálny sponzor',
        SponsorTagEnum::PARTNER->value => 'Partner',
        SponsorTagEnum::SUPPORTER->value => 'Podporovateľ',
    ],
    SportCategoryTypeEnum::class => [
        SportCategoryTypeEnum::CALISTHENICS->value => 'Kalisténia',
        SportCategoryTypeEnum::PARKOUR->value => 'Parkour',
    ],
    TimetableEntryStatusEnum::class => [
        TimetableEntryStatusEnum::PENDING->value => 'Čaká',
        TimetableEntryStatusEnum::IN_PROGRESS->value => 'Prebieha',
        TimetableEntryStatusEnum::FINISHED->value => 'Dokončený',
    ],
    TrainingPricingTypeEnum::class => [
        TrainingPricingTypeEnum::FREE->value => 'Zadarmo',
        TrainingPricingTypeEnum::PAID->value => 'Platený',
        TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED->value => 'Vyžaduje členstvo',
    ],
    RegistrationFieldTypeEnum::class => [
        RegistrationFieldTypeEnum::TEXT_INPUT->value => 'Textové pole',
        RegistrationFieldTypeEnum::TEXTAREA->value => 'Textová oblasť',
        RegistrationFieldTypeEnum::SELECT->value => 'Výber',
        RegistrationFieldTypeEnum::MULTI_SELECT->value => 'Viacnásobný výber',
        RegistrationFieldTypeEnum::DATE_PICKER->value => 'Výber dátumu',
        RegistrationFieldTypeEnum::YEAR_PICKER->value => 'Výber roku',
        RegistrationFieldTypeEnum::NUMBER_INPUT->value => 'Číselné pole',
        RegistrationFieldTypeEnum::TIME_PICKER->value => 'Výber času',
        RegistrationFieldTypeEnum::PHONE->value => 'Telefón',
        RegistrationFieldTypeEnum::EMAIL->value => 'E-mail',
        RegistrationFieldTypeEnum::FILE_INPUT->value => 'Súbor',
    ],
    RoleEnum::class => [
        RoleEnum::SUPER_ADMIN->value => 'Super Admin',
        RoleEnum::OWNER->value => 'Vlastník',
        RoleEnum::ADMIN->value => 'Admin',
        RoleEnum::TEAM_ADMIN->value => 'Tímový Admin',
        RoleEnum::COACH->value => 'Tréner',
        RoleEnum::ATHLETE->value => 'Športovec',
        RoleEnum::EDITOR->value => 'Editor',
        RoleEnum::JUDGE->value => 'Porotca',
        RoleEnum::CUSTOMER->value => 'Zákazník',
    ],
    PaymentMethodEnum::class => [
        PaymentMethodEnum::MANUAL->value => 'Manuálne',
        PaymentMethodEnum::BANK_TRANSFER->value => 'Bankový prevod',
        PaymentMethodEnum::CASH->value => 'Hotovosť',
        PaymentMethodEnum::STRIPE->value => 'Stripe',
    ],
    MembershipStatusEnum::class => [
        MembershipStatusEnum::ACTIVE->value => 'Aktívne',
        MembershipStatusEnum::EXPIRED->value => 'Expirované',
        MembershipStatusEnum::CANCELLED->value => 'Zrušené',
        MembershipStatusEnum::PENDING->value => 'Čakajúce',
    ],
    PayableTypeEnum::class => [
        PayableTypeEnum::MEMBERSHIP->value => 'Členstvo',
        PayableTypeEnum::TRAINING_REGISTRATION->value => 'Registrácia na tréning',
        PayableTypeEnum::COMPETITION_REGISTRATION->value => 'Registrácia na súťaž',
    ],
    SubscriptionStatusEnum::class => [
        SubscriptionStatusEnum::ACTIVE->value => 'Aktívne',
        SubscriptionStatusEnum::TRIALING->value => 'Skúšobné',
        SubscriptionStatusEnum::PAST_DUE->value => 'Po splatnosti',
        SubscriptionStatusEnum::CANCELLED->value => 'Zrušené',
        SubscriptionStatusEnum::EXPIRED->value => 'Expirované',
    ],
    PlanTierEnum::class => [
        PlanTierEnum::FREE->value => 'Zadarmo',
        PlanTierEnum::STARTER->value => 'Starter',
        PlanTierEnum::PRO->value => 'Pro',
        PlanTierEnum::ENTERPRISE->value => 'Enterprise',
    ],
    PayoutStatusEnum::class => [
        PayoutStatusEnum::PENDING->value => 'Čaká na odoslanie',
        PayoutStatusEnum::PROCESSING->value => 'Spracováva sa',
        PayoutStatusEnum::COMPLETED->value => 'Odoslaná',
        PayoutStatusEnum::FAILED->value => 'Zlyhala',
    ],
];
