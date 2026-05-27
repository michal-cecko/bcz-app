<x-filament::section>
    <div class="flex flex-col items-center gap-4 py-6 text-center">
        <x-filament::icon
            icon="heroicon-o-user-group"
            class="h-12 w-12 text-gray-400 dark:text-gray-500"
        />

        <div class="space-y-1">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                {{ __('member.membership.no_team_heading') }}
            </h3>
            <p class="mx-auto max-w-md text-sm text-gray-500 dark:text-gray-400">
                {{ __('member.membership.no_team_description') }}
            </p>
        </div>

        <x-filament::button
            tag="a"
            :href="route('teams.index')"
            icon="heroicon-o-user-group"
        >
            {{ __('member.membership.no_team_cta') }}
        </x-filament::button>
    </div>
</x-filament::section>
