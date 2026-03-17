<?php

namespace App\Models;

use App\Contracts\Linkable;
use App\Enums\MembershipPeriodEnum;
use App\Enums\RoleEnum;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasUuidV7;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Team extends Model implements HasAvatar, HasMedia, Linkable
{
    use HasCreator, HasFactory, HasSlug, HasTranslations, HasUuidV7, InteractsWithMedia, SoftDeletes;

    /** @var list<string> */
    public array $translatable = ['name', 'story', 'achievements'];

    protected $fillable = [
        'name',
        'slug',
        'story',
        'achievements',
        'socials',
        'logo',
        'is_active',
        'membership_enabled',
        'membership_fee_amount',
        'membership_fee_currency',
        'membership_period',
        'membership_description',
        'bank_account_iban',
        'bank_account_name',
        'stripe_connect_account_id',
        'default_locale',
        'contact_email',
        'contact_phone',
        'contact_website',
    ];

    protected function casts(): array
    {
        return [
            'socials' => 'json',
            'is_active' => 'boolean',
            'membership_enabled' => 'boolean',
            'membership_fee_amount' => 'decimal:2',
            'membership_period' => MembershipPeriodEnum::class,
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getFirstMediaUrl('logo') ?: null;
    }

    public function getLinkUrl(): string
    {
        return '/timy/'.$this->slug;
    }

    public function getLinkLabel(): string
    {
        return $this->getTranslation('name', app()->getLocale())
            ?: $this->getTranslation('name', 'sk');
    }

    public static function linkableOptions(): Collection
    {
        return static::query()
            ->where('is_active', true)
            ->get()
            ->mapWithKeys(fn (Team $t) => [$t->id => $t->getLinkLabel()]);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (Team $model) => $model->getTranslation('name', 'sk'))
            ->saveSlugsTo('slug');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(TeamUser::class)
            ->withPivot('role', 'is_active', 'joined_at')
            ->withTimestamps();
    }

    public function membersWithRole(RoleEnum $role): BelongsToMany
    {
        return $this->members()->wherePivot('role', $role->value);
    }

    public function sportCategories(): HasMany
    {
        return $this->hasMany(SportCategory::class);
    }

    public function exerciseCategories(): HasMany
    {
        return $this->hasMany(ExerciseCategory::class);
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Events with event_type=competition belonging to this team.
     */
    public function competitions(): HasMany
    {
        return $this->hasMany(Event::class)->where('event_type', 'competition');
    }

    /**
     * Alias for competitions() — used by subscription limits and team detail views.
     */
    public function organizedCompetitions(): HasMany
    {
        return $this->competitions();
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(TeamSubscription::class);
    }

    public function currentSubscription(): HasOne
    {
        return $this->hasOne(TeamSubscription::class)
            ->where('status', 'active')
            ->latest('created_at');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(TeamPayout::class);
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(TeamJoinRequest::class);
    }

    public function emailTemplates(): HasMany
    {
        return $this->hasMany(EmailTemplate::class);
    }

    public function mediaItems(): HasMany
    {
        return $this->hasMany(MediaItem::class);
    }
}
