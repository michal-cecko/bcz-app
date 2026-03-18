<x-filament-widgets::widget>
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <a href="{{ \App\Filament\Pages\MyTrainings::getUrl() }}" class="group flex flex-col items-center gap-3 rounded-xl border border-gray-200 bg-white p-5 text-center transition hover:border-primary-400 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-primary-500">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-50 transition group-hover:bg-primary-100 dark:bg-primary-500/10 dark:group-hover:bg-primary-500/20">
                <x-filament::icon icon="heroicon-o-academic-cap" class="h-6 w-6 text-primary-600 dark:text-primary-400" />
            </div>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Moje tréningy</span>
        </a>

        <a href="{{ \App\Filament\Pages\MemberPayments::getUrl() }}" class="group flex flex-col items-center gap-3 rounded-xl border border-gray-200 bg-white p-5 text-center transition hover:border-warning-400 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-warning-500">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning-50 transition group-hover:bg-warning-100 dark:bg-warning-500/10 dark:group-hover:bg-warning-500/20">
                <x-filament::icon icon="heroicon-o-banknotes" class="h-6 w-6 text-warning-600 dark:text-warning-400" />
            </div>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Platby</span>
        </a>

        <a href="{{ filament()->getProfileUrl() }}" class="group flex flex-col items-center gap-3 rounded-xl border border-gray-200 bg-white p-5 text-center transition hover:border-danger-400 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-danger-500">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-danger-50 transition group-hover:bg-danger-100 dark:bg-danger-500/10 dark:group-hover:bg-danger-500/20">
                <x-filament::icon icon="heroicon-o-user" class="h-6 w-6 text-danger-600 dark:text-danger-400" />
            </div>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Môj profil</span>
        </a>
    </div>
</x-filament-widgets::widget>
