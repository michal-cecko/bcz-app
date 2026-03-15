# Training Capacity & Notification System

## Overview
Implement waitlist notifications, capacity protection, and cancellation notifications for trainings.

## Feature 1: Capacity Decrease Protection

**Goal:** Cannot reduce `max_capacity` below current active registration count.

### Tasks:
1. **Add validation rule to TrainingForm** (`TrainingForm.php`)
   - On `max_capacity` field, add closure validation rule
   - Compare new value against `$record->registrations()->where('status', 'approved')->count()`
   - Error message: `"Kapacita nemôže byť nižšia ako počet aktuálnych registrácií (:count)."`
   - Only validate on edit (not create)

### Files:
- `app/Filament/Resources/Trainings/Schemas/TrainingForm.php`

---

## Feature 2: Cancellation Notification

**Goal:** When admin deletes or sets registration to rejected, the user receives notification: "Vaša registrácia na tréning XXX bola zrušená." (mail + database). Later: when user cancels, admin gets Filament panel notification (bell icon) and registration status changes to `cancelled`.

### Tasks:
1. **Create notification class** `TrainingRegistrationCancelled`
   - Channels: `mail`, `database`
   - Mail: subject "Zrušenie registrácie na tréning", body with training title (use user locale) + optional reason
   - Database: structured data for Filament notification bell + reason

2. **Hook into RegistrationsRelationManager**
   - On `DeleteAction` -> add modal with optional `reason` textarea ("Dôvod zrušenia") before deleting
   - Send notification to user if `user_id` exists, include reason if provided
   - On status change to rejected (EditAction): same flow with optional reason
   - Also trigger waitlist check (Feature 3) after deletion

3. **Later: User cancellation with reason**
   - Frontend cancel button shows optional reason textarea ("Dôvod zrušenia")
   - Admin receives Filament panel notification with user name + reason
   - Registration status changes to `cancelled`, reason stored

4. **Migration: add `cancellation_reason` to `training_registrations`**
   - `text`, nullable — stores reason from either admin or user side

### Files:
- `database/migrations/xxxx_add_cancellation_reason_to_training_registrations_table.php` (new)
- `app/Models/TrainingRegistration.php` (add to fillable)
- `app/Notifications/TrainingRegistrationCancelled.php` (new)
- `app/Filament/Resources/Trainings/RelationManagers/RegistrationsRelationManager.php`

---

## Feature 3: Waitlist Notification (notify_on_available)

**Goal:** When a spot opens on a full training, notify ALL users on the waitlist — race to register, first come first served. No `waitlisted` registration status needed.

### Approach:
- Separate `training_waitlist` table (not a registration status)
- Frontend: "Tréning je plný" message + "Upozorniť ma keď sa uvoľní miesto" button
- When spot opens: blast `TrainingSpotAvailable` notification to ALL waitlisted users
- Users must register normally after being notified
- `notify_on_available` toggle on training controls whether the "notify me" button appears

### Tasks:

1. **Migration: `training_waitlist` table**
   - `id` (auto-increment)
   - `training_id` (FK -> trainings, cascadeOnDelete)
   - `user_id` (FK -> users, cascadeOnDelete)
   - `created_at` timestamp
   - Unique constraint on `(training_id, user_id)`

2. **Model: `TrainingWaitlist`**
   - Relationships: belongsTo Training, belongsTo User
   - No factory needed initially

3. **Add relationships**
   - `Training::waitlistUsers(): BelongsToMany` (through training_waitlist)
   - `Training::waitlistEntries(): HasMany`
   - Helper: `Training::isFull(): bool`

4. **Create notification: `TrainingSpotAvailable`**
   - Channels: `mail`, `database`
   - Mail: "Uvoľnilo sa miesto na tréning XXX" with link to training detail
   - Database: structured data for dashboard
   - Notify ALL users on waitlist for that training

5. **Create `TrainingCapacityService`**
   - `isFull(Training $training): bool`
   - `notifyWaitlist(Training $training): void` — sends TrainingSpotAvailable to all waitlist users, then clears the waitlist
   - `handleSpotFreed(Training $training): void` — checks if training has `notify_on_available`, was previously full, now has space -> calls notifyWaitlist

6. **Trigger: Registration deleted (dashboard)**
   - In RegistrationsRelationManager DeleteAction `->after()`: call `TrainingCapacityService::handleSpotFreed()`

7. **Trigger: Capacity increased (dashboard)**
   - TrainingObserver `updating` event: if `max_capacity` increased and was previously full -> show confirmation toggle "Upozorniť používateľov na čakacom zozname?" before save
   - OR simpler: always call `handleSpotFreed()` on capacity increase (only notifies if there are waitlist entries)

8. **Frontend: "Notify me" button** (can be done later with frontend registration)
   - On training detail page, if full + `notify_on_available` is true
   - Button/form to add authenticated user to `training_waitlist`
   - Show "Ste na čakacom zozname" if already signed up

9. **Dashboard: Waitlist relation manager** (optional)
   - Show waitlisted users on training edit page
   - Allow manual removal from waitlist

### Files:
- `database/migrations/xxxx_create_training_waitlist_table.php` (new)
- `app/Models/TrainingWaitlist.php` (new)
- `app/Models/Training.php` (add relationships + isFull helper)
- `app/Notifications/TrainingSpotAvailable.php` (new)
- `app/Services/TrainingCapacityService.php` (new)
- `app/Observers/TrainingObserver.php` (new)
- `app/Providers/AppServiceProvider.php` (register observer)
- `app/Filament/Resources/Trainings/RelationManagers/RegistrationsRelationManager.php`
- Frontend views (later)

---

## Implementation Order

1. **Feature 1** (Capacity protection) — standalone validation, immediate value
2. **Feature 2** (Cancellation notification) — one notification class + RM hooks
3. **Feature 3** (Waitlist) — migration, model, service, observer, notification, RM integration
4. **Feature 3 frontend** (later) — "notify me" button on training detail page

## Notes
- All notification text in Slovak, respecting user's `locale` preference
- Database notifications enable Filament panel bell icon
- Waitlist is cleared after blast notification (users re-join if they miss it)
- No waitlisted registration status — keeps registration flow clean
