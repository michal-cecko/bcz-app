<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ShieldPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guardName = Utils::getFilamentAuthGuard();

        // Ensure all permissions exist (both standard Shield and custom)
        $this->ensurePermissionsExist($guardName);

        $this->seedRole('ADMIN', $guardName, $this->adminPermissions());
        $this->seedRole('EDITOR', $guardName, $this->editorPermissions());
        $this->seedRole('TEAMADMIN', $guardName, $this->teamAdminPermissions());
        $this->seedRole('COACH', $guardName, $this->coachPermissions());
        $this->seedRole('ATHLETE', $guardName, $this->athletePermissions());
        $this->seedRole('CUSTOMER', $guardName, $this->customerPermissions());

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    protected function ensurePermissionsExist(string $guardName): void
    {
        $models = [
            'Team', 'User', 'Training', 'Exercise', 'ExerciseCategory', 'SportCategory',
            'Payment', 'Membership', 'SubscriptionPlan', 'TeamPayout',
            'Event', 'EventCategory', 'Discipline', 'AthleteCategory',
            'Page', 'Menu', 'Sponsor', 'FaqCategory',
            'EmailTemplate', 'MediaItem', 'Inquiry', 'Setting',
            'TeamSubscription',
        ];

        $actions = ['ViewAny', 'View', 'Create', 'Update', 'Delete', 'Restore', 'ForceDelete', 'ForceDeleteAny', 'RestoreAny', 'Replicate', 'Reorder'];

        foreach ($models as $model) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action}:{$model}", 'guard_name' => $guardName]);
            }
        }

        // Custom "Own" permissions
        $customPermissions = config('filament-shield.custom_permissions', []);
        foreach ($customPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => $guardName]);
        }
    }

    protected function seedRole(string $roleName, string $guardName, array $permissions): void
    {
        $role = Role::where('name', $roleName)->where('guard_name', $guardName)->first();
        if (! $role) {
            return;
        }

        $validPermissions = Permission::where('guard_name', $guardName)
            ->whereIn('name', $permissions)
            ->pluck('name')
            ->toArray();

        $role->syncPermissions($validPermissions);
    }

    /** @return list<string> */
    protected function fullPermissions(string $model): array
    {
        return [
            "ViewAny:{$model}",
            "View:{$model}",
            "Create:{$model}",
            "Update:{$model}",
            "Delete:{$model}",
            "Restore:{$model}",
            "ForceDelete:{$model}",
            "ForceDeleteAny:{$model}",
            "RestoreAny:{$model}",
            "Replicate:{$model}",
            "Reorder:{$model}",
        ];
    }

    /** @return list<string> */
    protected function viewPermissions(string $model): array
    {
        return [
            "ViewAny:{$model}",
            "View:{$model}",
        ];
    }

    /** @return list<string> */
    protected function adminPermissions(): array
    {
        return array_merge(
            // Organizacia
            $this->fullPermissions('Team'),
            $this->fullPermissions('User'),
            // Treningy
            $this->fullPermissions('Training'),
            $this->fullPermissions('Exercise'),
            $this->fullPermissions('ExerciseCategory'),
            $this->fullPermissions('SportCategory'),
            // Financie
            $this->fullPermissions('Payment'),
            $this->fullPermissions('Membership'),
            $this->fullPermissions('SubscriptionPlan'),
            $this->fullPermissions('TeamPayout'),
            // Podujatia
            $this->fullPermissions('Event'),
            $this->fullPermissions('EventCategory'),
            $this->fullPermissions('Discipline'),
            $this->fullPermissions('AthleteCategory'),
            // Content
            $this->fullPermissions('Page'),
            $this->fullPermissions('Menu'),
            $this->fullPermissions('Sponsor'),
            $this->fullPermissions('FaqCategory'),
            // Ostatne
            $this->fullPermissions('EmailTemplate'),
            $this->fullPermissions('MediaItem'),
            $this->fullPermissions('Inquiry'),
            $this->fullPermissions('Setting'),
        );
    }

    /** @return list<string> */
    protected function editorPermissions(): array
    {
        return array_merge(
            // Organizacia
            $this->viewPermissions('Team'),
            ['ViewAny:User', 'View:User'],
            // Treningy
            $this->fullPermissions('Training'),
            $this->fullPermissions('Exercise'),
            $this->fullPermissions('ExerciseCategory'),
            $this->fullPermissions('SportCategory'),
            // Podujatia
            $this->fullPermissions('Event'),
            $this->fullPermissions('EventCategory'),
            $this->fullPermissions('Discipline'),
            $this->fullPermissions('AthleteCategory'),
            // Content
            $this->fullPermissions('Page'),
            $this->fullPermissions('Menu'),
            $this->fullPermissions('Sponsor'),
            $this->fullPermissions('FaqCategory'),
            // Ostatne
            $this->fullPermissions('EmailTemplate'),
            $this->fullPermissions('MediaItem'),
            $this->fullPermissions('Inquiry'),
            $this->fullPermissions('Setting'),
        );
    }

    /** @return list<string> */
    protected function teamAdminPermissions(): array
    {
        return array_merge(
            // Organizacia
            ['ViewAny:Team', 'View:Team', 'Update:Team'],
            ['ViewAny:User', 'View:User'],
            // Treningy — view + create + own update/delete
            ['ViewAny:Training', 'View:Training', 'Create:Training', 'UpdateOwn:Training', 'DeleteOwn:Training'],
            ['ViewAny:Exercise', 'View:Exercise', 'Create:Exercise', 'UpdateOwn:Exercise', 'DeleteOwn:Exercise'],
            $this->viewPermissions('ExerciseCategory'),
            $this->viewPermissions('SportCategory'),
            // Financie
            ['ViewAny:Payment', 'View:Payment', 'Create:Payment'],
            $this->fullPermissions('Membership'),
            $this->viewPermissions('SubscriptionPlan'),
            $this->viewPermissions('TeamPayout'),
            // Podujatia
            $this->fullPermissions('Event'),
            $this->viewPermissions('EventCategory'),
            $this->viewPermissions('Discipline'),
            $this->viewPermissions('AthleteCategory'),
            // Ostatne
            ['ViewAny:EmailTemplate', 'View:EmailTemplate', 'Create:EmailTemplate', 'UpdateOwn:EmailTemplate', 'DeleteOwn:EmailTemplate'],
            ['ViewAny:MediaItem', 'View:MediaItem', 'Create:MediaItem', 'UpdateOwn:MediaItem', 'DeleteOwn:MediaItem'],
            ['ViewAny:Inquiry', 'View:Inquiry', 'Create:Inquiry', 'UpdateOwn:Inquiry', 'DeleteOwn:Inquiry'],
        );
    }

    /** @return list<string> */
    protected function coachPermissions(): array
    {
        return array_merge(
            // Organizacia
            $this->viewPermissions('Team'),
            ['ViewAny:User', 'View:User'],
            // Treningy
            ['ViewAny:Training', 'View:Training', 'Update:Training'],
            $this->fullPermissions('Exercise'),
            $this->fullPermissions('ExerciseCategory'),
            $this->viewPermissions('SportCategory'),
            // Financie
            $this->viewPermissions('Membership'),
            // Podujatia
            $this->viewPermissions('Event'),
            $this->viewPermissions('EventCategory'),
            $this->viewPermissions('Discipline'),
            $this->viewPermissions('AthleteCategory'),
            // Ostatne
            $this->viewPermissions('EmailTemplate'),
            $this->fullPermissions('MediaItem'),
            $this->viewPermissions('Inquiry'),
        );
    }

    /** @return list<string> */
    protected function athletePermissions(): array
    {
        return array_merge(
            // Organizacia
            $this->viewPermissions('Team'),
            ['ViewAny:User', 'View:User'],
            // Treningy
            $this->viewPermissions('Training'),
            $this->viewPermissions('Exercise'),
            $this->viewPermissions('ExerciseCategory'),
            $this->viewPermissions('SportCategory'),
            // Financie
            $this->viewPermissions('Membership'),
            // Podujatia
            $this->viewPermissions('Event'),
            $this->viewPermissions('EventCategory'),
            $this->viewPermissions('Discipline'),
            $this->viewPermissions('AthleteCategory'),
            // Ostatne
            $this->viewPermissions('MediaItem'),
        );
    }

    /** @return list<string> */
    protected function customerPermissions(): array
    {
        return array_merge(
            ['View:User'],
            ['View:Payment'],
            ['View:Membership'],
            $this->viewPermissions('Event'),
        );
    }
}
