<div>
 {{-- Filters --}}
 <div class="flex flex-wrap gap-3 mb-8">
 <select wire:model.live="typeFilter"class="bg-[#111111] border border-[#222222] text-white text-sm px-4 py-2.5 focus:border-bcz-red focus:ring-0 outline-none">
 <option value="">{{ __('archive.all_types') }}</option>
 <option value="report">{{ __('archive.type_report') }}</option>
 <option value="organized">{{ __('archive.type_organized') }}</option>
 <option value="competition">{{ __('archive.type_competition') }}</option>
 </select>
 <select wire:model.live="categoryFilter"class="bg-[#111111] border border-[#222222] text-white text-sm px-4 py-2.5 focus:border-bcz-red focus:ring-0 outline-none">
 <option value="">{{ __('archive.all_categories') }}</option>
 @foreach($eventCategories as $category)
 <option value="{{ $category->id }}">{{ $category->getTranslation('title', app()->getLocale()) }}</option>
 @endforeach
 </select>
 <span class="flex items-center text-[#888888] text-sm ml-auto">{{ $events->total() }} {{ trans_choice('archive.event_count', $events->total()) }}</span>
 </div>

 @if($events->isEmpty())
 <div class="text-center py-20">
 <p class="text-[#666666] text-lg">{{ __('archive.no_events') }}</p>
 </div>
 @else
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 @foreach($events as $event)
 <a href="{{ route('event.show', $event) }}"wire:navigate class="bg-[#111111] overflow-hidden flex flex-col group hover:ring-1 hover:ring-[#333333] transition-all">
 <div class="h-[180px] bg-[#1A1A1A] overflow-hidden">
 @if($event->getFirstMediaUrl('card_image') || $event->card_image)
 <img src="{{ $event->getFirstMediaUrl('card_image') ?: $event->card_image }}"alt="{{ $event->getTranslation('title', app()->getLocale()) }}"class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
 @endif
 </div>
 <div class="p-5 flex flex-col gap-3">
 <div class="flex items-center justify-between">
 @if($event->eventCategory)
 <span class="text-xs px-2.5 py-1"
 style="background-color: {{ $event->eventCategory->color ?? '#E53E3E' }}20; color: {{ $event->eventCategory->color ?? '#E53E3E' }}">
 {{ $event->eventCategory->getTranslation('title', app()->getLocale()) }}
 </span>
 @endif
 @if($event->date)
 <span class="text-[#666666] text-xs">{{ $event->date->format('Y') }}</span>
 @endif
 </div>
 <h3 class="text-white text-lg font-bold">{{ $event->getTranslation('title', app()->getLocale()) }}</h3>
 @if($event->getTranslation('card_description', app()->getLocale()))
 <p class="text-[#888888] text-[13px] leading-relaxed line-clamp-2">{{ $event->getTranslation('card_description', app()->getLocale()) }}</p>
 @endif
 <div class="flex items-center gap-3 text-xs">
 @if($event->city)
 <div class="flex items-center gap-1.5 text-[#666666]">
 <svg class="w-3.5 h-3.5"fill="none"stroke="currentColor"viewBox="0 0 24 24"stroke-width="2"stroke-linecap="round"stroke-linejoin="round">
 <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12"cy="10"r="3"/>
 </svg>
 <span>{{ $event->city }}</span>
 </div>
 @endif
 @if($event->organization)
 @if($event->organization->pricing_type->value === 'paid')
 <span class="text-bcz-red font-semibold">{{ number_format($event->organization->price_amount, 0) }} {{ $event->organization->price_currency }}</span>
 @else
 <span class="text-green-500 font-semibold">{{ __('archive.free') }}</span>
 @endif
 @endif
 </div>
 </div>
 </a>
 @endforeach
 </div>

 @if($events->hasPages())
 {{ $events->links() }}
 @endif
 @endif
</div>
