@php
    $description = $locked
        ? __('member.membership.continuous_locked_description')
        : ($enabled
            ? __('member.membership.continuous_enabled_description')
            : __('member.membership.continuous_disabled_description'));
@endphp

<div class="fi-section rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 px-4 py-3">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3 min-w-0 flex-1">
            <x-filament::icon icon="heroicon-o-arrow-path" class="h-5 w-5 text-gray-400 shrink-0" />
            <div class="flex flex-col min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ __('member.membership.continuous_section_heading') }}
                    </span>
                    <x-filament::badge :color="$enabled ? 'success' : 'gray'" size="xs">
                        {{ $enabled ? __('member.membership.continuous_status_on') : __('member.membership.continuous_status_off') }}
                    </x-filament::badge>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $description }}</p>
            </div>
        </div>
        <div class="shrink-0">
            @if($enabled)
                <x-filament::button
                    type="button"
                    wire:click="requestDisableContinuousMembership"
                    color="danger"
                    size="sm"
                    icon="heroicon-m-x-mark"
                    :disabled="$locked"
                >
                    {{ __('member.membership.continuous_cancel') }}
                </x-filament::button>
            @else
                <x-filament::button
                    type="button"
                    wire:click="enableContinuousMembership"
                    color="success"
                    size="sm"
                    icon="heroicon-m-check"
                >
                    {{ __('member.membership.continuous_enable') }}
                </x-filament::button>
            @endif
        </div>
    </div>
</div>

@if($showContinuousDisableModal)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        wire:key="continuous-disable-modal"
    >
        <div
            class="fixed inset-0 bg-gray-950/50 dark:bg-gray-950/75"
            wire:click="cancelDisableContinuousMembership"
        ></div>

        <div class="relative w-full max-w-md rounded-xl bg-white dark:bg-gray-900 shadow-xl ring-1 ring-gray-950/5 dark:ring-white/10 p-6">
            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-danger-100 dark:bg-danger-500/20">
                    <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-5 w-5 text-danger-600 dark:text-danger-400" />
                </div>
                <div class="flex flex-col gap-2 flex-1">
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                        {{ __('member.membership.continuous_cancel_modal_heading') }}
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('member.membership.continuous_cancel_modal_description') }}
                    </p>
                </div>
            </div>

            @if($pendingMemberships->isNotEmpty())
                <div class="mt-5 rounded-lg border border-warning-300 dark:border-warning-500/40 bg-warning-50 dark:bg-warning-500/10 p-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model.live="cancelPendingOnDisable"
                            class="mt-0.5 h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 shrink-0"
                        >
                        <div class="flex flex-col gap-2 flex-1">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('member.membership.continuous_cancel_pending_label') }}
                            </span>
                            <ul class="flex flex-col gap-1 text-xs text-gray-600 dark:text-gray-300">
                                @foreach($pendingMemberships as $pm)
                                    <li class="flex items-center gap-2">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-warning-500 shrink-0"></span>
                                        <span>
                                            {{ $pm->season?->name ?? __('member.membership.season_label') }}
                                            —
                                            <span class="font-semibold">{{ number_format((float) $pm->fee_amount, 2) }} {{ $pm->fee_currency }}</span>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </label>
                </div>
            @endif

            <div class="mt-6 flex justify-end gap-2">
                <x-filament::button
                    wire:click="cancelDisableContinuousMembership"
                    color="gray"
                >
                    {{ __('member.membership.continuous_cancel_back') }}
                </x-filament::button>
                <x-filament::button
                    wire:click="confirmDisableContinuousMembership"
                    color="danger"
                >
                    {{ __('member.membership.continuous_cancel_confirm') }}
                </x-filament::button>
            </div>
        </div>
    </div>
@endif
