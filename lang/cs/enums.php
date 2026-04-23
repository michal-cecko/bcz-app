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
    BannerTypeEnum::class => [
        BannerTypeEnum::Topbar->value => 'Horní lišta',
        BannerTypeEnum::Floating->value => 'Plovoucí',
        BannerTypeEnum::Popup->value => 'Popup',
    ],
    InvitationStatusEnum::class => [
        InvitationStatusEnum::Pending->value => 'Čekající',
        InvitationStatusEnum::Accepted->value => 'Přijatá',
        InvitationStatusEnum::Declined->value => 'Odmítnutá',
        InvitationStatusEnum::Expired->value => 'Expirovaná',
    ],
    CoachRoleEnum::class => [
        CoachRoleEnum::MAIN->value => 'Hlavní trenér',
        CoachRoleEnum::SECONDARY->value => 'Asistent trenéra',
    ],
    ComplexityLevelEnum::class => [
        ComplexityLevelEnum::BASIC->value => 'Základní',
        ComplexityLevelEnum::INTERMEDIATE->value => 'Střední',
        ComplexityLevelEnum::ADVANCED->value => 'Pokročilý',
        ComplexityLevelEnum::ELITE->value => 'Elitní',
    ],
    GenderEnum::class => [
        GenderEnum::MALE->value => 'Muž',
        GenderEnum::FEMALE->value => 'Žena',
    ],
    GoalStatusEnum::class => [
        GoalStatusEnum::PLANNED->value => 'Plánovaný',
        GoalStatusEnum::IN_PROGRESS->value => 'Probíhá',
        GoalStatusEnum::ACTIVE->value => 'Aktivní',
        GoalStatusEnum::COMPLETED->value => 'Dokončený',
    ],
    InquiryReasonEnum::class => [
        InquiryReasonEnum::TRAINING->value => 'Trénink',
        InquiryReasonEnum::EXHIBITION->value => 'Vystoupení',
        InquiryReasonEnum::LECTURE->value => 'Přednáška',
        InquiryReasonEnum::WORKSHOP->value => 'Workshop',
        InquiryReasonEnum::COMPETITION->value => 'Soutěž',
        InquiryReasonEnum::OTHER->value => 'Jiné',
    ],
    InquiryStatusEnum::class => [
        InquiryStatusEnum::NEW->value => 'Nový',
        InquiryStatusEnum::IN_PROGRESS->value => 'Probíhá',
        InquiryStatusEnum::RESOLVED->value => 'Vyřešený',
    ],
    BillingPeriodEnum::class => [
        BillingPeriodEnum::MONTHLY->value => 'Měsíčně',
        BillingPeriodEnum::YEARLY->value => 'Ročně',
    ],
    PaymentStatusEnum::class => [
        PaymentStatusEnum::PENDING->value => 'Čeká na platbu',
        PaymentStatusEnum::COMPLETED->value => 'Zaplaceno',
        PaymentStatusEnum::REFUNDED->value => 'Vráceno',
        PaymentStatusEnum::CANCELLED->value => 'Zrušeno',
    ],
    PairingStrategyEnum::class => [
        PairingStrategyEnum::RANDOM->value => 'Náhodně',
        PairingStrategyEnum::SEEDED->value => 'Nasazené (1 vs N, 2 vs N-1)',
    ],
    RoundAdvancementTypeEnum::class => [
        RoundAdvancementTypeEnum::TOP_BY_POINTS->value => 'Nejlepší podle bodů',
        RoundAdvancementTypeEnum::BATTLE_WINNER->value => 'Vítěz battlu',
    ],
    ScoringFormatEnum::class => [
        ScoringFormatEnum::POINTS->value => 'Body',
        ScoringFormatEnum::COACH_DECISION->value => 'Rozhodnutí porotce',
    ],
    SettingTypeEnum::class => [
        SettingTypeEnum::TEXT->value => 'Text',
        SettingTypeEnum::NUMBER->value => 'Číslo',
        SettingTypeEnum::BOOLEAN->value => 'Ano/Ne',
        SettingTypeEnum::SELECT->value => 'Výběr',
        SettingTypeEnum::MULTI_SELECT->value => 'Vícenásobný výběr',
        SettingTypeEnum::TEAM_SELECT->value => 'Výběr týmu',
        SettingTypeEnum::DATE->value => 'Datum',
    ],
    SponsorTagEnum::class => [
        SponsorTagEnum::MAIN_SPONSOR->value => 'Hlavní sponzor',
        SponsorTagEnum::MEDIAL_SPONSOR->value => 'Mediální sponzor',
        SponsorTagEnum::PARTNER->value => 'Partner',
        SponsorTagEnum::SUPPORTER->value => 'Podporovatel',
    ],
    SportCategoryTypeEnum::class => [
        SportCategoryTypeEnum::CALISTHENICS->value => 'Kalistenika',
        SportCategoryTypeEnum::PARKOUR->value => 'Parkour',
    ],
    TimetableEntryStatusEnum::class => [
        TimetableEntryStatusEnum::PENDING->value => 'Čeká',
        TimetableEntryStatusEnum::IN_PROGRESS->value => 'Probíhá',
        TimetableEntryStatusEnum::FINISHED->value => 'Dokončený',
    ],
    TrainingPricingTypeEnum::class => [
        TrainingPricingTypeEnum::FREE->value => 'Zdarma',
        TrainingPricingTypeEnum::PAID->value => 'Placený',
        TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED->value => 'Vyžaduje členství',
    ],
    RegistrationFieldTypeEnum::class => [
        RegistrationFieldTypeEnum::TEXT_INPUT->value => 'Textové pole',
        RegistrationFieldTypeEnum::TEXTAREA->value => 'Textová oblast',
        RegistrationFieldTypeEnum::SELECT->value => 'Výběr',
        RegistrationFieldTypeEnum::MULTI_SELECT->value => 'Vícenásobný výběr',
        RegistrationFieldTypeEnum::DATE_PICKER->value => 'Výběr data',
        RegistrationFieldTypeEnum::YEAR_PICKER->value => 'Výběr roku',
        RegistrationFieldTypeEnum::NUMBER_INPUT->value => 'Číselné pole',
        RegistrationFieldTypeEnum::TIME_PICKER->value => 'Výběr času',
        RegistrationFieldTypeEnum::PHONE->value => 'Telefon',
        RegistrationFieldTypeEnum::EMAIL->value => 'E-mail',
        RegistrationFieldTypeEnum::FILE_INPUT->value => 'Soubor',
        RegistrationFieldTypeEnum::FIRST_NAME->value => 'Jméno',
        RegistrationFieldTypeEnum::LAST_NAME->value => 'Příjmení',
        RegistrationFieldTypeEnum::FULL_NAME->value => 'Celé jméno',
        RegistrationFieldTypeEnum::BIRTH_DATE->value => 'Datum narození',
        RegistrationFieldTypeEnum::GENDER->value => 'Pohlaví',
    ],
    RoleEnum::class => [
        RoleEnum::SUPER_ADMIN->value => 'Super Admin',
        RoleEnum::ADMIN->value => 'Admin',
        RoleEnum::TEAM_ADMIN->value => 'Týmový Admin',
        RoleEnum::COACH->value => 'Trenér',
        RoleEnum::ATHLETE->value => 'Sportovec',
        RoleEnum::EDITOR->value => 'Editor',
        RoleEnum::JUDGE->value => 'Porotce',
        RoleEnum::CUSTOMER->value => 'Zákazník',
    ],
    PaymentMethodEnum::class => [
        PaymentMethodEnum::BANK_TRANSFER->value => 'Bankovní převod',
        PaymentMethodEnum::CASH->value => 'Hotovost',
        PaymentMethodEnum::GOPAY->value => 'GoPay',
    ],
    MembershipStatusEnum::class => [
        MembershipStatusEnum::ACTIVE->value => 'Aktivní',
        MembershipStatusEnum::COMPLETED->value => 'Ukončené',
        MembershipStatusEnum::CANCELLED->value => 'Zrušené',
        MembershipStatusEnum::PENDING->value => 'Čekající',
    ],
    EventTypeEnum::class => [
        EventTypeEnum::Report->value => 'Report',
        EventTypeEnum::Organized->value => 'Organizované',
        EventTypeEnum::Competition->value => 'Soutěž',
    ],
    EventPricingTypeEnum::class => [
        EventPricingTypeEnum::Free->value => 'Zdarma',
        EventPricingTypeEnum::Paid->value => 'Placené',
    ],
    PayableTypeEnum::class => [
        PayableTypeEnum::MEMBERSHIP->value => 'Členství',
        PayableTypeEnum::TRAINING_REGISTRATION->value => 'Registrace na trénink',
        PayableTypeEnum::COMPETITION_REGISTRATION->value => 'Registrace na soutěž',
    ],
    SubscriptionStatusEnum::class => [
        SubscriptionStatusEnum::ACTIVE->value => 'Aktivní',
        SubscriptionStatusEnum::TRIALING->value => 'Zkušební',
        SubscriptionStatusEnum::PAST_DUE->value => 'Po splatnosti',
        SubscriptionStatusEnum::CANCELLED->value => 'Zrušené',
        SubscriptionStatusEnum::COMPLETED->value => 'Ukončené',
    ],
    PlanTierEnum::class => [
        PlanTierEnum::FREE->value => 'Zdarma',
        PlanTierEnum::STARTER->value => 'Starter',
        PlanTierEnum::PRO->value => 'Pro',
        PlanTierEnum::ENTERPRISE->value => 'Enterprise',
    ],
    PayoutStatusEnum::class => [
        PayoutStatusEnum::PENDING->value => 'Čeká na odeslání',
        PayoutStatusEnum::PROCESSING->value => 'Zpracovává se',
        PayoutStatusEnum::COMPLETED->value => 'Odeslaná',
        PayoutStatusEnum::FAILED->value => 'Selhala',
    ],
    PageStatusEnum::class => [
        PageStatusEnum::Draft->value => 'Koncept',
        PageStatusEnum::Published->value => 'Publikovaná',
        PageStatusEnum::Archived->value => 'Archivovaná',
    ],
    RegistrationStatusEnum::class => [
        RegistrationStatusEnum::Pending->value => 'Čekající',
        RegistrationStatusEnum::Approved->value => 'Schválená',
        RegistrationStatusEnum::Rejected->value => 'Zamítnutá',
        RegistrationStatusEnum::Cancelled->value => 'Zrušená',
    ],
    MenuLocationEnum::class => [
        MenuLocationEnum::Header->value => 'Hlavička',
        MenuLocationEnum::FooterDiscover->value => 'Patička — Objevte',
        MenuLocationEnum::FooterPrograms->value => 'Patička — Programy',
        MenuLocationEnum::FooterLegal->value => 'Patička — Právní',
    ],
    TeamJoinModeEnum::class => [
        TeamJoinModeEnum::APPROVAL->value => 'Se schválením',
        TeamJoinModeEnum::OPEN->value => 'Otevřený',
    ],
    DraftStatusEnum::class => [
        DraftStatusEnum::Pending->value => 'Čeká na schválení',
        DraftStatusEnum::Rejected->value => 'Zamítnutý',
    ],
    ProfileTypeEnum::class => [
        ProfileTypeEnum::Coach->value => 'Trenér',
        ProfileTypeEnum::Athlete->value => 'Sportovec',
        ProfileTypeEnum::Judge->value => 'Porotce',
    ],
];
