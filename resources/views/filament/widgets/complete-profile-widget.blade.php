<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-3">
            <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-6 w-6 text-warning-500" />
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                    Dokončite svoj profil
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Chýba vám telefón, dátum narodenia alebo pohlavie.
                </p>
            </div>
            <x-filament::button
                :href="filament()->getProfileUrl()"
                tag="a"
                size="sm"
                color="warning"
            >
                Upraviť profil
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
