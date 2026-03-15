# Unified Event System — Implementation Plan

## Overview

Merge Event (portfolio reports), organized events (camps, workshops, public trainings), and competitions into a single unified Event model with detail tables. Training stays separate (recurring weekly).

## Architecture

```
Event (base table)
├── event_type: REPORT | ORGANIZED | COMPETITION
├── title, slug, content (mason bricks), dates, location, images, category, team
│
├── hasOne → EventOrganization (when ORGANIZED or COMPETITION)
│   ├── capacity, pricing, registration config
│   ├── registration_form_schema (JSON — form blueprint/definition only)
│   └── hasMany → EventRegistration
│       ├── hasMany → RegistrationFieldValue (normalized submitted values)
│       └── morphMany → Payment (via PayableTypeEnum::EVENT_REGISTRATION)
│
└── hasOne → CompetitionDetail (when COMPETITION)
    ├── belongsToMany → AthleteCategory (pivot)
    ├── belongsToMany → Discipline (pivot)
    ├── belongsToMany → User (judges pivot)
    ├── hasMany → TimetableEntry
    ├── hasMany → CompetitionRound
    │   ├── hasMany → RoundPart → CompetitionResult
    │   └── hasMany → Battle
    └── hasMany → RegistrationFee (per athlete category pricing)
```

## Key Decisions (locked)

| Decision | Choice | Reason |
|----------|--------|--------|
| team_id | Required, NOT NULL | Every event belongs to a team (hard tenant scoped) |
| Field naming | `title` (not `name`) | Standardized across all event types |
| EventCategory | Applies to ALL types including COMPETITION | User wants categorization for competitions too |
| Data migration | Fresh start | No legacy competition data to migrate |
| /sutaze URL | Filtered shortcut to /eventy?typ=sutaz | One archive, filtered |
| CompetitionReport model | REMOVED | Results shown via Mason bricks on event content field |
| Competition child models | STAY as structured data | CompetitionResult, RoundPart, Battle, CompetitionRound — queried by Mason bricks |
| Form submissions | Normalized `RegistrationFieldValue` rows | NOT JSON blob — enables querying, reporting, CSV export |
| Form schema | JSON on EventOrganization | Blueprint/definition only, not submitted data |

## Event Type Behavior

### REPORT (current events — portfolio)
- Title, dates, location, images, category, Mason content
- No registration, no payment, no capacity
- "Where we were / what we did"

### ORGANIZED (camps, workshops, public trainings)
- Everything from REPORT +
- EventOrganization: capacity, pricing (FREE/PAID), registration window
- EventRegistration: users sign up, pay, fill custom form
- RegistrationFieldValue: normalized form responses

### COMPETITION
- Everything from ORGANIZED +
- CompetitionDetail: links to athlete categories, disciplines, judges
- TimetableEntry: schedule with live status
- CompetitionRound → RoundPart → CompetitionResult: scoring system
- Battle: bracket-based rounds
- RegistrationFee: per-category pricing
- After event: Mason bricks render results from these models

## Database Schema

### Extended: `events` table (add columns)

```
event_type          string, default 'report', indexed
place_name          string, nullable
place_address       string, nullable
latitude            decimal(10,7), nullable
longitude           decimal(10,7), nullable
```

Existing columns stay: id, event_category_id, team_id, title, slug, card_description, card_image, date, date_end, country, city, detail_image, content, attendee_count, client, is_published, published_at, timestamps, deleted_at

### New: `event_organizations` table

```
id                      uuid, primary
event_id                uuid, FK → events, cascade, unique
max_capacity            unsigned integer, nullable
pricing_type            string (FREE / PAID)
price_amount            decimal(10,2), nullable
price_currency          string(3), default 'EUR'
registration_form_schema json, nullable (form field definitions only)
registration_opens_at   datetime, nullable
registration_closes_at  datetime, nullable
is_public_registration  boolean, default true
show_countdown          boolean, default false
external_link           string, nullable
timestamps
```

### New: `event_registrations` table

```
id                      uuid, primary
event_id                uuid, FK → events, cascade
user_id                 uuid, FK → users, nullable, nullOnDelete
athlete_category_id     uuid, FK → athlete_categories, nullable, nullOnDelete
registration_fee_id     uuid, FK → registration_fees, nullable, nullOnDelete
status                  string, default 'pending'
registered_at           datetime, nullable
weight_in               decimal(5,2), nullable
timestamps
```

### New: `registration_field_values` table

```
id                      uuid, primary
event_registration_id   uuid, FK → event_registrations, cascade
field_key               string (matches key from registration_form_schema)
field_type              string (text, select, email, phone, etc.)
value                   text, nullable
timestamps
```

### New: `competition_details` table

```
id                      uuid, primary
event_id                uuid, FK → events, cascade, unique
timestamps
```

### Repointed: competition child tables (change FK from competition_id to competition_detail_id)

- `timetable_entries` → add competition_detail_id FK
- `competition_rounds` → add competition_detail_id FK
- `registration_fees` → add competition_detail_id FK
- `competition_athlete_category` pivot → add competition_detail_id FK
- `competition_discipline` pivot → add competition_detail_id FK
- `competition_judges` pivot → add competition_detail_id FK

### Dropped tables

- `competition_reports` (replaced by Mason bricks on event content)
- `competitions` (replaced by events + event_organizations + competition_details)
- `competition_registrations` (replaced by event_registrations)

## Models

### New Models

**EventOrganization**
- Traits: HasFactory, HasUuidV7 (HasUlids)
- Relationships: event() BelongsTo, registrations() through Event
- Casts: pricing_type → EventPricingTypeEnum, registration_form_schema → array

**EventRegistration**
- Traits: HasFactory, HasUuidV7
- Relationships: event() BelongsTo, user() BelongsTo, athleteCategory() BelongsTo, registrationFee() BelongsTo, fieldValues() HasMany, payments() MorphMany
- Payment via PayableTypeEnum::EVENT_REGISTRATION

**RegistrationFieldValue**
- Traits: HasFactory, HasUuidV7
- Relationships: eventRegistration() BelongsTo

**CompetitionDetail**
- Traits: HasFactory, HasUuidV7
- Relationships: event() BelongsTo, athleteCategories() BelongsToMany, disciplines() BelongsToMany, judges() BelongsToMany, timetableEntries() HasMany, rounds() HasMany, registrationFees() HasMany

### Updated Models

**Event**
- Add: event_type cast to EventTypeEnum
- Add: organization() HasOne EventOrganization
- Add: competitionDetail() HasOne CompetitionDetail
- Add: registrations() HasMany EventRegistration
- Add: computed status attribute (hidden/countdown/registering/in_progress/upcoming/finished)
- Add: place_name, place_address, latitude, longitude to fillable

**TimetableEntry** — change competition() to competitionDetail(), FK → competition_detail_id
**CompetitionRound** — change competition() to competitionDetail(), FK → competition_detail_id
**RegistrationFee** — change competition() to competitionDetail(), FK → competition_detail_id
**AthleteCategory** — pivot references competition_detail_id
**Discipline** — pivot references competition_detail_id

### Removed Models
- Competition (replaced by Event with event_type)
- CompetitionRegistration (replaced by EventRegistration)
- CompetitionReport (replaced by Mason bricks)

## Enums

### New
- `EventTypeEnum`: REPORT, ORGANIZED, COMPETITION
- `EventPricingTypeEnum`: FREE, PAID

### Updated
- `PayableTypeEnum`: add EVENT_REGISTRATION

### Removed
- CompetitionStatusEnum (status computed on Event model instead)

## Filament Admin

### EventResource — Extended Form

**Base tab (always visible):**
- Event type selector (REPORT / ORGANIZED / COMPETITION) — live(), affects visibility of other tabs
- Title tabs (SK/EN/CS)
- Slug (disabled)
- Content tabs with Mason bricks (SK/EN/CS)
- Category selector (EventCategory — for all types)
- Location: place_name, place_address, country, city, latitude, longitude
- Dates: date, date_end
- Images: card_image, detail_image
- Publishing: is_published, published_at, team selector
- Report-specific: attendee_count, client (visible when REPORT)

**Organization tab (ORGANIZED + COMPETITION):**
- max_capacity
- pricing_type (FREE/PAID) — live()
- price_amount, price_currency (visible when PAID)
- registration_opens_at, registration_closes_at
- is_public_registration, show_countdown
- external_link
- Registration form schema builder (repeater with field config)

**Competition tab (COMPETITION only):**
- Athlete categories checkboxes
- Disciplines checkboxes

**Relation managers (conditional):**
- EventRegistrationsRelationManager (ORGANIZED + COMPETITION)
- TimetableRelationManager (COMPETITION)
- RoundsRelationManager (COMPETITION)
- JudgesRelationManager (COMPETITION)

### Removed Resources
- CompetitionResource → merged into EventResource
- CompetitionReportResource → removed

## Frontend

### Event Detail View — conditional by event_type

**REPORT:**
- Hero + Mason content (current behavior)

**ORGANIZED:**
- Hero with price tag + registration status badge
- Info panel: price, dates, capacity (X/Y), registration button
- Registration form (Livewire component) with payment step
- Mason content

**COMPETITION:**
- Hero with registration status
- Info panel: dates, location, registration window
- Timetable (live with status)
- Registration form with athlete category + fee selection
- Mason content (results bricks after event ends)

### Events Archive
- Filter toggle: Vsetky / Nase akcie (ORGANIZED) / Kde sme boli (REPORT) / Sutaze (COMPETITION)
- Cards show: price, capacity, registration status for ORGANIZED/COMPETITION
- Upcoming organized events highlighted

### Routes
- `/eventy` — unified archive (all types, filterable)
- `/eventy/{event:slug}` — unified detail (all types)
- `/sutaze` — filtered shortcut (events archive with typ=sutaz)
- Remove `/timy/{team}/sutaze/...` (team-nested competition routes)

### Livewire Components
- `EventRegistrationForm` — shared for ORGANIZED + COMPETITION
- Update `EventsArchive` — add event_type filter

## Mason Bricks (future phase)

New bricks for displaying competition data from DB:
- `competition-results` — select competition event, renders results per round/category
- `competition-brackets` — battle bracket visualization
- `competition-timetable` — live timetable display

These bricks query CompetitionDetail → Rounds → Results models and render them. Admin selects which competition to show. Same pattern as events-showcase brick.

## Implementation Waves

### Wave 1: Database & Models
1. Migrations (7 files): extend events, create new tables, repoint FKs, drop old tables
2. New enums: EventTypeEnum, EventPricingTypeEnum
3. New models: EventOrganization, EventRegistration, RegistrationFieldValue, CompetitionDetail
4. Update existing models: Event, TimetableEntry, CompetitionRound, RegistrationFee, etc.
5. Update PayableTypeEnum
6. Factories for all new models

### Wave 2: Filament Admin
1. Extend EventResource form (conditional tabs)
2. Move/adapt relation managers from CompetitionResource
3. Remove CompetitionResource, CompetitionReportResource

### Wave 3: Frontend
1. Update event detail view (conditional sections)
2. Add registration Livewire component
3. Update events archive (type filter)
4. Update routes (/sutaze shortcut, remove old competition routes)

### Wave 4: Mason Bricks
1. competition-results brick
2. competition-brackets brick
3. competition-timetable brick

### Wave 5: Cleanup
1. Remove old models (Competition, CompetitionRegistration, CompetitionReport)
2. Remove old controllers, routes, views
3. Drop old tables
4. Update seeders
