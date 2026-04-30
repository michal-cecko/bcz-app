@props([
    'wireModel',
    'options' => [],
    'placeholder' => '---',
    'isRequired' => false,
    'multiple' => false,
])

@php
    $optionsJson = json_encode($options, JSON_UNESCAPED_UNICODE);
    $multipleJs = $multiple ? 'true' : 'false';
@endphp

<div
    x-data="prettyPicker({
        options: {{ $optionsJson }},
        multiple: {{ $multipleJs }},
        placeholder: @js($placeholder),
        searchPlaceholder: @js(__('archive.search')),
        emptyLabel: @js(match (app()->getLocale()) { 'cs' => 'Nic nenalezeno', 'en' => 'No results', default => 'Nič nenájdené' }),
    })"
    x-modelable="value"
    wire:model.live="{{ $wireModel }}"
    class="relative"
    @keydown.escape.prevent.stop="close()"
    @click.outside="close()"
>
    {{-- Trigger --}}
    <button
        type="button"
        @click="toggle()"
        :class="open ? 'border-bcz-red' : 'border-[#333333]'"
        class="bg-[#0A0A0A] border rounded-lg min-h-[44px] px-3.5 py-2 text-left text-white text-[14px] focus:border-bcz-red focus:ring-0 outline-none w-full transition-colors flex items-center gap-2 flex-wrap"
    >
        {{-- Empty state --}}
        <template x-if="selected.length === 0">
            <span class="text-[#555555]" x-text="placeholder"></span>
        </template>

        {{-- Single value (multiple=false): plain text --}}
        <template x-if="!multiple && selected.length > 0">
            <span class="text-white" x-text="labelFor(selected[0])"></span>
        </template>

        {{-- Multi-select chips --}}
        <template x-if="multiple && selected.length > 0">
            <div class="flex items-center gap-1.5 flex-wrap">
                <template x-for="value in selected" :key="value">
                    <span class="inline-flex items-center gap-1.5 bg-[#1A1A1A] border border-[#333333] rounded-md pl-2 pr-1 py-0.5 text-[12px] text-white">
                        <span x-text="labelFor(value)"></span>
                        <button
                            type="button"
                            @click.stop="remove(value)"
                            class="hover:bg-[#2A2A2A] rounded p-0.5 transition-colors text-[#888888] hover:text-white"
                            aria-label="Remove"
                        >
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 0 1 1.414 0L10 8.586l4.293-4.293a1 1 0 1 1 1.414 1.414L11.414 10l4.293 4.293a1 1 0 0 1-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 0 1-1.414-1.414L8.586 10 4.293 5.707a1 1 0 0 1 0-1.414Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </span>
                </template>
            </div>
        </template>

        {{-- Chevron --}}
        <svg class="w-4 h-4 ml-auto text-[#888888] transition-transform shrink-0" :class="open && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.06l3.71-3.83a.75.75 0 1 1 1.08 1.04l-4.25 4.39a.75.75 0 0 1-1.08 0L5.21 8.27a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
        </svg>
    </button>

    {{-- Hidden native input for HTML5 required validation --}}
    @if($isRequired)
        <input
            type="text"
            tabindex="-1"
            aria-hidden="true"
            required
            class="absolute opacity-0 w-0 h-0 pointer-events-none"
            :value="selected.length > 0 ? selected.join(',') : ''"
        >
    @endif

    {{-- Dropdown panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        x-cloak
        class="absolute z-50 mt-1 w-full bg-[#0A0A0A] border border-[#333333] rounded-lg shadow-2xl overflow-hidden"
    >
        {{-- Search box --}}
        <div class="border-b border-[#222222] p-2">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-[#555555]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                </svg>
                <input
                    type="text"
                    x-model="search"
                    x-ref="searchInput"
                    :placeholder="searchPlaceholder"
                    class="bg-[#0F0F0F] border border-[#222222] rounded-md h-[36px] pl-8 pr-3 text-white text-[13px] focus:border-bcz-red focus:ring-0 outline-none w-full placeholder-[#555555]"
                >
            </div>
        </div>

        {{-- Options list --}}
        <ul class="max-h-64 overflow-y-auto py-1" role="listbox">
            <template x-for="(label, value) in filteredOptions()" :key="value">
                <li role="option">
                    <button
                        type="button"
                        @click="select(value)"
                        :class="isSelected(value) ? 'bg-[#1A1A1A] text-white' : 'text-[#CCCCCC] hover:bg-[#141414] hover:text-white'"
                        class="flex items-center gap-2 w-full px-3 py-2 text-left text-[13px] transition-colors"
                    >
                        {{-- Multi-select checkbox --}}
                        <template x-if="multiple">
                            <span
                                :class="isSelected(value) ? 'bg-bcz-red border-bcz-red' : 'border-[#333333]'"
                                class="w-4 h-4 border rounded flex items-center justify-center shrink-0 transition-colors"
                            >
                                <svg x-show="isSelected(value)" class="w-3 h-3 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </template>

                        {{-- Single-select check on right when active --}}
                        <span x-text="label" class="flex-1"></span>

                        <template x-if="!multiple && isSelected(value)">
                            <svg class="w-4 h-4 text-bcz-red shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                        </template>
                    </button>
                </li>
            </template>

            <template x-if="Object.keys(filteredOptions()).length === 0">
                <li class="px-3 py-3 text-[13px] text-[#555555] text-center" x-text="emptyLabel"></li>
            </template>
        </ul>
    </div>
</div>
