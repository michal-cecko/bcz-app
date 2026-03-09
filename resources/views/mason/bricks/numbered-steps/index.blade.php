@if(! empty($steps))
    <div class="flex flex-col md:flex-row items-start md:items-center justify-center gap-4">
        @foreach($steps as $index => $step)
            @if($index > 0)
                <div class="hidden md:block w-12 h-0.5 bg-gray-300"></div>
            @endif
            <div class="flex md:flex-col items-center md:items-center gap-4 md:gap-2 md:text-center max-w-[200px]">
                <div class="shrink-0 w-12 h-12 rounded-full bg-red-500 text-white flex items-center justify-center font-bold text-lg">
                    {{ $index + 1 }}
                </div>
                <div>
                    @if(! empty($step['title']))
                        <h3 class="font-semibold">{{ brick_trans($step['title']) }}</h3>
                    @endif
                    @if(! empty($step['description']))
                        <p class="text-sm text-gray-600 mt-1">{!! brick_trans($step['description']) !!}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
