@if(! empty($levels))
 <div class="space-y-8">
 @foreach($levels as $level)
 <div>
 @if(! empty($level['name']))
 <div class="inline-block px-4 py-1 rounded-full text-sm font-bold text-white mb-4"style="background-color: {{ $level['color'] ?? '#ef4444' }}">
 {{ brick_trans($level['name']) }}
 </div>
 @endif
 @if(! empty($level['cards']))
 <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
 @foreach($level['cards'] as $card)
 <div class="bg-white shadow-sm border p-5 border-t-4"style="border-top-color: {{ $level['color'] ?? '#ef4444' }}">
 @if(! empty($card['title']))
 <h4 class="font-semibold mb-1">{{ brick_trans($card['title']) }}</h4>
 @endif
 @if(! empty($card['description']))
 <p class="text-sm text-gray-600">{!! brick_trans($card['description']) !!}</p>
 @endif
 </div>
 @endforeach
 </div>
 @endif
 </div>
 @endforeach
 </div>
@endif
