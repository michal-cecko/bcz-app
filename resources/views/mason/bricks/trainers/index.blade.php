@if($trainers->isNotEmpty())
 <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
 @foreach($trainers as $trainer)
 <div class="bg-white shadow-sm border overflow-hidden">
 <div class="aspect-square bg-gray-100 flex items-center justify-center">
 @if($trainer->avatar_url)
 <img src="{{ $trainer->avatar_url }}"alt="{{ $trainer->name }}"class="w-full h-full object-cover">
 @else
 <svg class="w-20 h-20 text-gray-300"fill="currentColor"viewBox="0 0 24 24"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
 @endif
 </div>
 <div class="p-4">
 <h3 class="font-semibold text-lg">{{ $trainer->name }}</h3>
 </div>
 </div>
 @endforeach
 </div>
@else
 <p class="text-gray-500 text-center py-8">Momentálne nie sú k dispozícii žiadni tréneri.</p>
@endif
