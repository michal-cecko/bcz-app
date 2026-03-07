@if(! empty($cards))
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($cards as $card)
            <div class="bg-white rounded-xl shadow-sm border p-6">
                @if(! empty($card['icon']))
                    <x-dynamic-component :component="$card['icon']" class="w-8 h-8 text-primary-600 mb-4" />
                @endif
                @if(! empty($card['title']))
                    <h3 class="text-lg font-semibold mb-2">{{ $card['title'] }}</h3>
                @endif
                @if(! empty($card['description']))
                    <p class="text-gray-600">{{ $card['description'] }}</p>
                @endif
            </div>
        @endforeach
    </div>
@endif
