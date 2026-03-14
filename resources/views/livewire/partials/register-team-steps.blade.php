@php
    $steps = [
        1 => 'Účet',
        2 => 'Tím',
        3 => 'Plán',
        4 => 'Hotovo',
    ];
@endphp

<div class="flex items-center justify-center">
    <div class="flex items-center w-full max-w-[600px]">
        @foreach($steps as $num => $label)
            <div class="flex items-center gap-2">
                @php
                    $isCompleted = $step > $num || ($step === 4 && $num === 4);
                    $isCurrent = $step === $num;
                    $bgColor = $isCompleted ? ($num === 4 ? 'bg-green-500' : 'bg-[#1A1A1A]') : ($isCurrent ? 'bg-bcz-red' : 'bg-[#1A1A1A]');
                    $borderColor = $isCompleted ? 'border-green-500' : ($isCurrent ? 'border-bcz-red' : 'border-bcz-faint');
                    $textColor = ($isCompleted || $isCurrent) ? 'text-white' : 'text-bcz-dim';
                    $labelColor = ($isCompleted || $isCurrent) ? 'text-white font-semibold' : 'text-bcz-dim font-medium';
                @endphp
                <div class="flex items-center justify-center w-7 h-7 rounded-full {{ $bgColor }} border {{ $borderColor }}">
                    @if($isCompleted && $num < $step)
                        <svg class="w-3.5 h-3.5 {{ $num === 4 ? 'text-white' : 'text-green-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <span class="text-xs {{ $textColor }} font-semibold">{{ $num }}</span>
                    @endif
                </div>
                <span class="text-xs {{ $labelColor }}">{{ $label }}</span>
            </div>
            @if($num < 4)
                @php
                    $lineColor = $step > $num ? ($step === 4 ? 'bg-green-500' : 'bg-bcz-red') : 'bg-bcz-border';
                @endphp
                <div class="flex-1 h-px {{ $lineColor }} mx-2"></div>
            @endif
        @endforeach
    </div>
</div>
