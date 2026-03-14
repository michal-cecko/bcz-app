<section class="bg-[#0A0A0A] py-20">
    <div class="max-w-[1440px] mx-auto px-5 md:px-10 lg:px-20 flex flex-col items-center gap-6 lg:gap-10">
        {{-- Header --}}
        <div class="flex flex-col items-center gap-4">
            @if(! empty($title))
                <h2 class="font-display font-bold text-[32px] tracking-wide flex items-center gap-3">
                    <svg class="w-8 h-8 text-bcz-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    {{ brick_trans($title) }}
                </h2>
            @endif
            @if(! empty($subtitle))
                <p class="text-[#888888] text-center max-w-[600px] leading-relaxed">{{ brick_trans($subtitle) }}</p>
            @endif
        </div>

        {{-- Video placeholder / embed --}}
        @php
            $videoSource = $video_source ?? 'url';
            $mediaVideoUrl = ($videoSource === 'media' && ! empty($video_media)) ? brick_media_url($video_media) : null;
        @endphp

        @if($videoSource === 'media' && $mediaVideoUrl)
            <div class="w-full max-w-[900px] aspect-video rounded-2xl overflow-hidden">
                <video src="{{ $mediaVideoUrl }}" class="w-full h-full object-cover" controls playsinline></video>
            </div>
        @elseif($videoSource === 'url' && ! empty($video_url))
            <div class="w-full max-w-[900px] aspect-video rounded-2xl overflow-hidden">
                <iframe src="{{ $video_url }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
            </div>
        @else
            <div class="w-full max-w-[900px] aspect-video rounded-2xl bg-[#0D0D0D] border border-[#222222] flex flex-col items-center justify-center">
                <div class="w-20 h-20 bg-bcz-red rounded-full flex items-center justify-center text-white">
                    <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.5 5.653c0-1.427 1.529-2.33 2.779-1.643l11.54 6.347c1.295.712 1.295 2.573 0 3.286L7.28 19.99c-1.25.687-2.779-.217-2.779-1.643V5.653Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="text-[#333333] text-xs tracking-[2px] font-semibold mt-4">VIDEO PLACEHOLDER</span>
            </div>
        @endif

        {{-- Checkpoints --}}
        @if(! empty($checkpoints))
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-10 pt-5">
                @foreach($checkpoints as $checkpoint)
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-[#22C55E] shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-[#CCCCCC] text-sm">{{ brick_trans($checkpoint['text'] ?? []) }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
