@if(! empty($items))
    <div class="grid grid-cols-2 md:grid-cols-{{ min(count($items), 4) }} gap-6 text-center">
        @foreach($items as $item)
            <div class="p-4">
                @if(! empty($item['icon']))
                    <x-dynamic-component :component="$item['icon']" class="w-8 h-8 mx-auto mb-2 text-primary-600" />
                @endif
                @if(! empty($item['number']))
                    <div class="text-3xl font-bold">{{ $item['number'] }}</div>
                @endif
                @if(! empty($item['label']))
                    <div class="text-sm text-gray-500 mt-1">{{ brick_trans($item['label']) }}</div>
                @endif
            </div>
        @endforeach
    </div>
@endif
